<?php

namespace Database\Seeders;

use App\Enums\DesignStatus;
use App\Models\Category;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DesignLibrarySeeder extends Seeder
{
    public const DESIGN_COUNT = 100_000;

    public const CHUNK_SIZE = 1_000;

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Sample data is restricted to local/testing environments.');
        }

        // Disable optional query logging so it cannot retain all insert bindings.
        DB::connection()->disableQueryLog();
        $started = hrtime(true);
        $count = DB::table('designs')->count();
        if ($count === static::DESIGN_COUNT) {
            $this->command?->info('Target design count already exists; no data or accounts changed.');

            return;
        }
        if ($count !== 0) {
            throw new RuntimeException('Designs must be empty or already at the target count. Existing data was preserved; use a separate empty local sample database.');
        }
        if (! is_file(public_path('images/design-placeholder.svg'))) {
            throw new RuntimeException('The shared local placeholder is missing.');
        }

        DB::transaction(function () {
            $this->call(SampleAccountSeeder::class);
            $names = ['Branding', 'Typography', 'Illustration', 'Photography', 'Packaging', 'Editorial', 'Web Design', 'Mobile Design', 'Architecture', 'Interior Design', 'Motion Design', 'Print Design'];
            $categoryIds = [];
            foreach ($names as $name) {
                $categoryIds[] = Category::firstOrCreate(['name' => $name])->id;
            }

            $base = CarbonImmutable::create(2025, 1, 1, 0, 0, 0, 'UTC');
            for ($offset = 0; $offset < static::DESIGN_COUNT; $offset += static::CHUNK_SIZE) {
                $rows = [];
                $end = min($offset + static::CHUNK_SIZE, static::DESIGN_COUNT);
                for ($i = $offset; $i < $end; $i++) {
                    $date = $base->addSeconds(intdiv($i, 2) * 600)->format('Y-m-d H:i:s');
                    $rows[] = [
                        'title' => ($i % 2 === 0 ? 'Design sample ' : 'تصميم تجريبي ').sprintf('%06d', $i + 1),
                        'category_id' => $categoryIds[intdiv($i, 4) % count($categoryIds)],
                        'status' => intdiv($i, 2) % 2 === 0 ? DesignStatus::Draft->value : DesignStatus::Published->value,
                        'thumbnail' => 'images/design-placeholder.svg',
                        'created_at' => $date,
                        'updated_at' => null,
                    ];
                }
                DB::table('designs')->insert($rows);
            }
        });

        $this->command?->info(sprintf('Inserted %d designs in %.3f seconds; peak PHP allocated memory %.2f MiB.', static::DESIGN_COUNT, (hrtime(true) - $started) / 1e9, memory_get_peak_usage(true) / 1048576));
    }
}
