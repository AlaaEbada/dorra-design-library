<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Design;
use App\Models\User;
use Database\Seeders\DesignLibrarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class SmallDesignLibrarySeeder extends DesignLibrarySeeder
{
    public const DESIGN_COUNT = 28;

    public const CHUNK_SIZE = 7;
}

class SampleDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_small_seeder_covers_chunk_boundaries_and_required_distribution(): void
    {
        $this->seed(SmallDesignLibrarySeeder::class);
        $this->assertSame(28, Design::count());
        $this->assertSame(12, Category::count());
        $this->assertSame(2, User::count());
        $this->assertSame(14, Design::where('status', 'draft')->count());
        $this->assertSame(14, Design::where('status', 'published')->count());
        $this->assertSame(14, Design::where('title', 'like', 'Design sample %')->count());
        $this->assertSame(14, Design::where('title', 'like', 'تصميم تجريبي %')->count());
        $this->assertSame(14, DB::table('designs')->distinct()->count('created_at'));
        $this->assertSame(1, DB::table('designs')->distinct()->count('thumbnail'));
        $this->assertFileExists(public_path(Design::firstOrFail()->thumbnail));
        $this->assertSame(0, Design::doesntHave('category')->count());
    }

    public function test_repeat_run_preserves_dataset_and_accounts(): void
    {
        $this->seed(SmallDesignLibrarySeeder::class);
        $before = DB::table('designs')->orderBy('id')->get()->toJson();
        $password = User::where('email', 'admin@dorra.test')->value('password');
        $this->seed(SmallDesignLibrarySeeder::class);
        $this->assertSame($before, DB::table('designs')->orderBy('id')->get()->toJson());
        $this->assertSame($password, User::where('email', 'admin@dorra.test')->value('password'));
        $this->assertSame(12, Category::count());
        $this->assertSame(2, User::count());
    }

    public function test_partial_dataset_is_preserved_and_rejected(): void
    {
        Design::factory()->create(['title' => 'Existing design']);
        try {
            $this->seed(SmallDesignLibrarySeeder::class);
            $this->fail('Partial dataset was not rejected.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Existing data was preserved', $exception->getMessage());
        }
        $this->assertSame(1, Design::count());
        $this->assertSame('Existing design', Design::firstOrFail()->title);
        $this->assertSame(0, User::count());
    }

    public function test_production_environment_is_rejected(): void
    {
        $this->app->instance('env', 'production');
        $this->expectException(RuntimeException::class);
        $this->app->make(SmallDesignLibrarySeeder::class)->run();
    }
}
