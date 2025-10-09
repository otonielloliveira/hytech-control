<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\SidebarService;

class SidebarWidgetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize default sidebar widgets
        SidebarService::initializeDefaultWidgets();
        
        $this->command->info('Sidebar widgets initialized successfully!');
    }
}
