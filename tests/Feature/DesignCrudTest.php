<?php

namespace Tests\Feature;

use App\Enums\DesignStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Design;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignCrudTest extends TestCase
{
    use RefreshDatabase;

    private function account(Role $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_guests_are_redirected_to_login_for_crud_routes(): void
    {
        $design = Design::factory()->create();

        $this->get(route('admin.designs'))->assertRedirect('/login');
        $this->get(route('designs.create'))->assertRedirect('/login');
        $this->post(route('designs.store'), [])->assertRedirect('/login');
        $this->get(route('designs.show', $design))->assertRedirect('/login');
        $this->get(route('designs.edit', $design))->assertRedirect('/login');
        $this->put(route('designs.update', $design), [])->assertRedirect('/login');
        $this->delete(route('designs.destroy', $design))->assertRedirect('/login');
    }

    public function test_both_roles_can_view_a_draft_without_management_actions(): void
    {
        $design = Design::factory()->create(['title' => 'Draft concept', 'status' => DesignStatus::Draft]);

        $this->actingAs($this->account(Role::Admin))
            ->get(route('designs.show', $design))
            ->assertOk()
            ->assertSee('Draft concept')
            ->assertDontSee('Edit design')
            ->assertDontSee('Delete design');

        $this->get(route('library'))
            ->assertOk()
            ->assertDontSee('+ Add design')
            ->assertDontSee('Edit design');

        $this->actingAs($this->account(Role::Viewer))
            ->get(route('designs.show', $design))
            ->assertOk()
            ->assertSee('Draft concept')
            ->assertDontSee('Edit design')
            ->assertDontSee('Delete design');
    }

    public function test_admin_has_a_separate_design_management_dashboard(): void
    {
        $design = Design::factory()->create(['title' => 'Managed design']);

        $this->actingAs($this->account(Role::Admin))
            ->get(route('admin.designs'))
            ->assertOk()
            ->assertSee('Manage designs')
            ->assertSee('Managed design')
            ->assertSee('class="admin-thumbnail"', false)
            ->assertSee('images/design-placeholder.svg')
            ->assertSee('+ Add design')
            ->assertSee('Edit')
            ->assertSee('Delete')
            ->assertSee('name="_token"', false)
            ->assertSee('name="_method" value="DELETE"', false);

        $this->actingAs($this->account(Role::Viewer))
            ->get(route('admin.designs'))
            ->assertForbidden();
    }

    public function test_viewer_direct_write_requests_are_forbidden_without_mutation(): void
    {
        $viewer = $this->account(Role::Viewer);
        $category = Category::factory()->create();
        $design = Design::factory()->for($category)->create(['title' => 'Original title']);
        $payload = ['title' => 'Forged write', 'category_id' => $category->id, 'status' => DesignStatus::Published->value];

        $this->actingAs($viewer)->get(route('designs.create'))->assertForbidden();
        $this->actingAs($viewer)->get(route('admin.designs'))->assertForbidden();
        $this->actingAs($viewer)->post(route('designs.store'), $payload)->assertForbidden();
        $this->actingAs($viewer)->get(route('designs.edit', $design))->assertForbidden();
        $this->actingAs($viewer)->put(route('designs.update', $design), $payload)->assertForbidden();
        $this->actingAs($viewer)->delete(route('designs.destroy', $design))->assertForbidden();

        $this->assertDatabaseMissing('designs', ['title' => 'Forged write']);
        $this->assertDatabaseHas('designs', ['id' => $design->id, 'title' => 'Original title']);
    }

    public function test_admin_can_create_and_update_a_design_with_the_fixed_thumbnail(): void
    {
        $admin = $this->account(Role::Admin);
        $firstCategory = Category::factory()->create(['name' => 'Identity']);
        $secondCategory = Category::factory()->create(['name' => 'Interface']);

        $this->actingAs($admin)->get(route('designs.create'))
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertDontSee('name="thumbnail"', false);

        $this->actingAs($admin)->post(route('designs.store'), [
            'title' => 'واجهة عربية',
            'category_id' => $firstCategory->id,
            'status' => DesignStatus::Draft->value,
        ])->assertRedirect();

        $design = Design::where('title', 'واجهة عربية')->firstOrFail();
        $this->assertDatabaseHas('designs', [
            'id' => $design->id,
            'category_id' => $firstCategory->id,
            'status' => DesignStatus::Draft->value,
            'thumbnail' => 'images/design-placeholder.svg',
        ]);

        $this->actingAs($admin)->put(route('designs.update', $design), [
            'title' => 'Updated interface',
            'category_id' => $secondCategory->id,
            'status' => DesignStatus::Published->value,
        ])->assertRedirect(route('admin.designs'));

        $this->assertDatabaseHas('designs', [
            'id' => $design->id,
            'title' => 'Updated interface',
            'category_id' => $secondCategory->id,
            'status' => DesignStatus::Published->value,
            'thumbnail' => 'images/design-placeholder.svg',
        ]);
    }

    public function test_invalid_input_is_rejected_without_writes_and_preserves_old_input(): void
    {
        $admin = $this->account(Role::Admin);
        $count = Design::count();

        $this->actingAs($admin)->from(route('designs.create'))->post(route('designs.store'), [
            'title' => '',
            'category_id' => 999999,
            'status' => 'archived',
            'thumbnail' => 'forged-image.svg',
        ])->assertRedirect(route('designs.create'))
            ->assertSessionHasErrors(['title', 'category_id', 'status', 'thumbnail'])
            ->assertSessionHasInput('status', 'archived');

        $this->assertSame($count, Design::count());
    }

    public function test_admin_can_delete_a_design(): void
    {
        $admin = $this->account(Role::Admin);
        $design = Design::factory()->create();

        $this->actingAs($admin)->delete(route('designs.destroy', $design))
            ->assertRedirect(route('admin.designs'))
            ->assertSessionHas('success', 'Design deleted successfully.');

        $this->assertDatabaseMissing('designs', ['id' => $design->id]);
    }

    public function test_admin_search_combines_filters_and_preserves_pagination(): void
    {
        $this->actingAs($this->account(Role::Admin));
        $category = Category::factory()->create();
        Design::factory()->count(25)->for($category)->published()->create(['title' => 'Design matching', 'created_at' => '2025-01-01 00:00:00']);
        Design::factory()->for($category)->create(['title' => 'Design draft']);
        Design::factory()->published()->create(['title' => 'Other title']);
        $filters = ['title' => 'Design', 'category_id' => $category->id, 'status' => 'published'];
        $page = $this->get(route('admin.designs', $filters))->assertOk()->viewData('designs');
        $this->assertSame(25, $page->total());
        $this->assertCount(24, $page);
        parse_str(parse_url($page->nextPageUrl(), PHP_URL_QUERY), $parameters);
        $this->assertEquals($filters, array_intersect_key($parameters, $filters));
        $this->get($page->nextPageUrl())->assertOk()->assertViewHas('designs', fn ($designs) => count($designs) === 1);
    }

    public function test_admin_arabic_search_empty_state_and_invalid_filters(): void
    {
        $this->actingAs($this->account(Role::Admin));
        Design::factory()->create(['title' => 'تصميم عربي']);
        Design::factory()->create(['title' => 'هذا تصميم عربي']);
        $this->get(route('admin.designs', ['title' => 'تصميم']))->assertOk()
            ->assertViewHas('designs', fn ($designs) => $designs->total() === 1);
        $this->get(route('admin.designs', ['title' => 'missing']))->assertOk()->assertSee('No designs found');
        $this->get(route('admin.designs', ['status' => 'bad', 'page' => 0]))
            ->assertRedirect(route('admin.designs'))->assertSessionHasErrors(['status', 'page']);
    }
}
