<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class Criterion122MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parentId = 870; // Fixed Parent ID as requested
        
        // Data extracted from the "1.2.2" section of your provided list
 $items =[
[ 'title' => '7.1.2_A_Policy Document on Green Campus Initiatives', 'url' => 'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_A_Policy-Document-on-Green-Campus-Initiatives.pdf' ],
[ 'title' => '7.1.2_B_ Photographs', 'url' => 'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_B_-Photographs.pdf' ],
[ 'title' => '7.1.2_7.1.3_c_Reports', 'url' => 'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.2_7.1.3_c_Reports.pdf' ],
[ 'title' => '7.1.3_ENERGY AUDIT', 'url' => 'https://vikascollege.org/vikas/wp-content/uploads/2024/08/7.1.3_ENERGY-AUDIT.pdf' ],
[ 'title' => 'E-recycling certificate', 'url' => 'https://vikascollege.org/vikas/wp-content/uploads/2024/08/E-recycling-certificate.pdf' ],

];


        // Loop through items and insert them
        foreach ($items as $index => $item) {
            
            // Remove the domain from the URL (exact domain removal)
            $cleanUrl = str_replace('https://vikascollege.org', '', $item['url']);
            
            Menu::create([
                'title' => $item['title'],
                'url' => $cleanUrl, // Storing clean URL without domain
                'parent_id' => $parentId,
                'order' => $index, // Order starts from 0 and increments
                'status' => true,
                // 'create_page' is conceptually false here, so we don't trigger page creation logic
            ]);
        }

        // Clear the cache as done in MenuController
        Cache::forget('menu_tree');

        $this->command->info('Criterion 1.2.2 Menus seeded successfully.');
    }
}