<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Dcat\Admin\Models\AdminTablesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
    	$this->call(UsersTableSeeder::class);
		$this->call(RepliesTableSeeder::class);
        $this->call(TopicsTableSeeder::class);
        $this->call(LinksTableSeeder::class);
        //$this->call(AdminTablesSeeder::class);
    }
}
