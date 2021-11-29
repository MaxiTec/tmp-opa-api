<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Property;
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

        // Creamos Entidades Fuertes Hoteles

        // populate Sections and Areas
        \App\Models\Section::factory(5)->create()->each(function (Section $section) {
            $section->areas()->saveMany(\App\Models\Area::factory(3)->make(['section_id' => $section->id]));
        });
        // Creamos preguntas o criterios a evaluar
        \App\Models\Criteria::factory(10)->create();

        $areas = \App\Models\Area::all();
        // Asignacion de Preguntas por area
        \App\Models\Criteria::all()->each(function ($criteria) use ($areas) { 
            $criteria->areas()->attach(
                $areas->random(rand(1, 3))->pluck('id')->toArray()
            ); 
        });

        \App\Models\Property::factory(3)->create()->each(function (Property $property) {
            $property->CriteriaByArea()->attach(rand(1,5));
        });
    }
}
