<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Design;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DesignListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create()->fresh());
    }

    public function test_prefix_search_in_english_and_arabic(): void
    {
        foreach (['Design alpha', 'A Design alpha', 'تصميم عربي', 'هذا تصميم عربي'] as $title) {
            Design::factory()->create(['title' => $title]);
        }
        $this->get('/library?title=Design')->assertOk()->assertViewHas('designs', fn ($page) => $page->total() === 1 && $page[0]->title === 'Design alpha');
        $this->get('/library?'.http_build_query(['title' => 'تصميم']))->assertOk()
            ->assertViewHas('designs', fn ($page) => $page->total() === 1 && $page[0]->title === 'تصميم عربي');
    }

    public function test_wildcards_escape_character_and_backslash_are_literal(): void
    {
        foreach (['100% original', '1000 original', 'a_b title', 'axb title', 'a!b title', 'a\b title', "O'Reilly design"] as $title) {
            Design::factory()->create(['title' => $title]);
        }
        foreach (['100%', 'a_', 'a!', 'a'.chr(92), "O'Reilly"] as $prefix) {
            $this->get('/library?'.http_build_query(['title' => $prefix]))->assertOk()
                ->assertViewHas('designs', fn ($page) => $page->total() === 1 && str_starts_with($page[0]->title, $prefix));
        }
    }

    public function test_category_status_and_combined_filters(): void
    {
        $category = Category::factory()->create();
        Design::factory()->for($category)->create(['title' => 'Design draft']);
        $match = Design::factory()->for($category)->published()->create(['title' => 'Design published']);
        Design::factory()->published()->create(['title' => 'Other category']);
        $this->get('/library?category_id='.$category->id)->assertViewHas('designs', fn ($p) => $p->total() === 2);
        $this->get('/library?status=published')->assertViewHas('designs', fn ($p) => $p->total() === 2);
        $this->get('/library?'.http_build_query(['title' => 'Design', 'category_id' => $category->id, 'status' => 'published']))
            ->assertOk()->assertViewHas('designs', fn ($p) => $p->total() === 1 && $p[0]->is($match));
    }

    public function test_pagination_is_stable_preserves_filters_and_eager_loads(): void
    {
        $category = Category::factory()->create();
        Design::factory()->count(26)->for($category)->published()->create(['title' => 'Design same date', 'created_at' => '2025-01-01 10:00:00']);
        $newest = Design::factory()->for($category)->published()->create(['title' => 'Design newest', 'created_at' => '2025-02-01 10:00:00']);
        $query = http_build_query(['title' => 'Design', 'status' => 'published', 'category_id' => $category->id]);
        $first = $this->get('/library?'.$query)->assertOk();
        $page = $first->viewData('designs');
        $this->assertCount(24, $page);
        $this->assertTrue($page[0]->is($newest));
        $this->assertTrue($page[0]->relationLoaded('category'));
        $this->assertSame($newest->id - 1, $page[1]->id);
        $next = $page->nextPageUrl();
        parse_str(parse_url($next, PHP_URL_QUERY), $parameters);
        $this->assertSame('Design', $parameters['title']);
        $this->assertSame('published', $parameters['status']);
        $this->assertSame((string) $category->id, $parameters['category_id']);
        $second = $this->get($next)->assertOk()->viewData('designs');
        $this->assertCount(3, $second);
        $this->assertEmpty(array_intersect($page->pluck('id')->all(), $second->pluck('id')->all()));
        $this->assertSame(range(3, 1), $second->pluck('id')->all());
    }

    public function test_invalid_filters_redirect_to_clean_index_with_field_errors(): void
    {
        $this->from('/library?status=bad')->get('/library?'.http_build_query(['title' => ['array'], 'status' => 'archived', 'category_id' => 999, 'page' => 0]))
            ->assertRedirect('/library')->assertSessionHasErrors(['title', 'category_id', 'status', 'page']);
        $this->get('/library')->assertOk()->assertSee('role="alert"', false);
        $this->get('/library?'.http_build_query(['title' => str_repeat('x', 256)]))->assertSessionHasErrors('title');
    }

    public function test_empty_state_reset_link_and_html_escaping(): void
    {
        Design::factory()->create(['title' => '<script>alert(1)</script>']);
        $this->get('/library')->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/library?title=missing')->assertOk()->assertSee('No designs found')->assertSee('href="'.route('library').'"', false);
    }

    public function test_clearing_filters_restores_the_default_listing(): void
    {
        $category = Category::factory()->create();
        Design::factory()->count(25)->for($category)->published()->create(['title' => 'Design matching']);
        Design::factory()->create(['title' => 'Other draft']);
        $default = $this->get(route('library'))->assertOk()->viewData('designs');
        $filtered = $this->get(route('library', [
            'title' => 'Design', 'category_id' => $category->id, 'status' => 'published', 'page' => 2,
        ]))->assertOk();
        $this->assertSame(25, $filtered->viewData('designs')->total());
        $this->assertSame(2, $filtered->viewData('designs')->currentPage());

        $document = new \DOMDocument;
        @$document->loadHTML($filtered->getContent());
        $links = (new \DOMXPath($document))->query('//a[normalize-space(.)="Clear filters"]');
        $this->assertSame(1, $links->length);
        $resetUrl = $links->item(0)->getAttribute('href');
        $this->assertSame(route('library'), $resetUrl);
        $this->assertNull(parse_url($resetUrl, PHP_URL_QUERY));
        $reset = $this->get($resetUrl)->assertOk()->viewData('designs');
        $this->assertSame(26, $reset->total());
        $this->assertSame(1, $reset->currentPage());
        $this->assertCount(24, $reset);
        $this->assertSame($default->pluck('id')->all(), $reset->pluck('id')->all());
    }

    public function test_queries_fetch_a_page_and_category_in_one_eager_query(): void
    {
        Design::factory()->count(30)->for(Category::factory())->create();
        DB::enableQueryLog();
        $this->get('/library')->assertOk();
        $queries = collect(DB::getQueryLog());
        DB::disableQueryLog();
        $designSelects = $queries->filter(fn ($q) => str_contains($q['query'], 'from "designs"'));
        $this->assertCount(2, $designSelects); // SQL count plus paged data, not all designs.
        $this->assertTrue($designSelects->contains(fn ($q) => str_contains($q['query'], 'limit 24 offset 0')));
        $this->assertCount(2, $queries->filter(fn ($q) => str_contains($q['query'], 'from "categories"'))); // Filter menu + eager load.
    }
}
