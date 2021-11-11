<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create and assing Roles and permissions
        $this->call(PermissionsDemoSeeder::class);
        // $this->call(SpaTableSeeder::class);

        // populate Sections and Areas
        \App\Models\Section::factory(10)->create()->each(function (Section $section) {
            $section->areas()->saveMany(\App\Models\Area::factory(10)->make(['section_id' => $section->id]));
        });
        \App\Models\Criteria::factory(10)->create();

        $areas = \App\Models\Area::all();

        \App\Models\Criteria::all()->each(function ($criteria) use ($areas) { 
            $criteria->areas()->attach(
                $areas->random(rand(1, 3))->pluck('id')->toArray()
            ); 
        });
    }
}
