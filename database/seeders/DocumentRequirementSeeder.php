<?php

namespace Database\Seeders;

use App\Models\DocumentRequirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentRequirement::insert([
            ['property_type_id' => 1, 'title' => 'Title Deed'],
            ['property_type_id' => 1, 'title' => 'Site Plan'],
            ['property_type_id' => 1, 'title' => 'ID Card (Front & Back)'],
            ['property_type_id' => 2, 'title' => 'Villa Map'],
            ['property_type_id' => 2, 'title' => 'Completion Certificate'],
        ]);
    }
}
