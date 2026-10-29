<?php

namespace Tests\Feature;

use App\Enums\DesignStatus;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ValueError;

class DesignSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_casts_and_factory_states(): void
    {
        $category = Category::factory()->create();
        $draft = Design::factory()->for($category)->create(['title' => 'تصميم عربي']);
        $published = Design::factory()->for($category)->published()->create();
        $this->assertTrue($draft->category->is($category));
        $this->assertSame(2, $category->designs()->count());
        $this->assertSame(DesignStatus::Draft, $draft->fresh()->status);
        $this->assertSame(DesignStatus::Published, $published->fresh()->status);
        $this->assertInstanceOf(Carbon::class, $draft->created_at);
        $this->assertSame('تصميم عربي', $draft->fresh()->title);
    }

    public function test_model_rejects_invalid_status(): void
    {
        $this->expectException(ValueError::class);
        Design::factory()->make(['status' => 'archived']);
    }

    public function test_database_rejects_invalid_status_without_eloquent(): void
    {
        $category = Category::factory()->create();
        $this->expectException(QueryException::class);
        DB::table('designs')->insert(['title' => 'Invalid', 'category_id' => $category->id, 'status' => 'archived', 'thumbnail' => 'placeholder.svg']);
    }

    public function test_database_rejects_missing_category(): void
    {
        $this->expectException(QueryException::class);
        Design::factory()->create(['category_id' => 999]);
    }

    public function test_category_deletion_is_restricted_when_used(): void
    {
        $design = Design::factory()->create();
        $this->expectException(QueryException::class);
        $design->category->delete();
    }

    public function test_database_sets_required_creation_date_and_default_draft(): void
    {
        $category = Category::factory()->create();
        $id = DB::table('designs')->insertGetId(['title' => 'Default', 'category_id' => $category->id, 'thumbnail' => 'placeholder.svg']);
        $design = Design::findOrFail($id);
        $this->assertNotNull($design->created_at);
        $this->assertSame(DesignStatus::Draft, $design->status);
    }

    public function test_database_rejects_null_creation_date(): void
    {
        $category = Category::factory()->create();
        $this->expectException(QueryException::class);
        DB::table('designs')->insert(['title' => 'Invalid', 'category_id' => $category->id, 'thumbnail' => 'placeholder.svg', 'created_at' => null]);
    }
}
