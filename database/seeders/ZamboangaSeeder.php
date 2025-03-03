<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ZamboangaSeeder extends Seeder
{
    public function run()
    {
        // First, check column sizes to understand the schema
        $this->checkColumnSizes();

        // Retrieve Zamboanga Peninsula region (already exists)
        $regionCode = '09'; // Region IX - Zamboanga Peninsula

        try {
            // Add Zamboanga del Sur if not exists
            $zamboangaDelSur = Province::where('code', '097300')->first();
            
            if (!$zamboangaDelSur) {
                // Use DB facade to handle unexpected column requirements
                DB::table('provinces')->insert([
                    'code' => '097300',
                    'name' => 'Zamboanga del Sur',
                    'region_id' => $regionCode,
                    'correspondence_code' => '097300000',
                    'province_id' => '09730' // Add this as it's required
                ]);
                $zamboangaDelSur = Province::where('code', '097300')->first();
                $this->command->info('Province: ' . $zamboangaDelSur->name . ' created');
            } else {
                $this->command->info('Province: ' . $zamboangaDelSur->name . ' already exists');
            }

            // Add Zamboanga City if not exists
            $zamboangaCity = City::where('code', '093170')->first();
            
            if (!$zamboangaCity) {
                DB::table('cities')->insert([
                    'code' => '093170',
                    'name' => 'City of Zamboanga',
                    'province_id' => '09317', 
                    'region_id' => $regionCode,
                    'correspondence_code' => '093170000',
                    'city_id' => '09317'  // May be required based on schema
                ]);
                $zamboangaCity = City::where('code', '093170')->first();
                $this->command->info('City: ' . $zamboangaCity->name . ' created');
            } else {
                $this->command->info('City: ' . $zamboangaCity->name . ' already exists');
            }

        // Add Zamboanga City barangays with shortened codes
        $barangays = [
            ['code' => '09317001', 'name' => 'Ayala'],
            ['code' => '09317002', 'name' => 'Baluno'],
            ['code' => '09317003', 'name' => 'Baliwasan'],
            ['code' => '09317004', 'name' => 'Boalan'],
            ['code' => '09317005', 'name' => 'Bolong'],
            ['code' => '09317006', 'name' => 'Buenavista'],
            ['code' => '09317007', 'name' => 'Bunguiao'],
            ['code' => '09317008', 'name' => 'Cabatangan'],
            ['code' => '09317009', 'name' => 'Cacao'],
            ['code' => '09317010', 'name' => 'Calabasa'],
            ['code' => '09317011', 'name' => 'Calarian'],
            ['code' => '09317012', 'name' => 'Campo Islam'],
            ['code' => '09317013', 'name' => 'Canelar'],
            ['code' => '09317014', 'name' => 'Capisan'],
            ['code' => '09317015', 'name' => 'Cawit'],
            ['code' => '09317016', 'name' => 'Culianan'],
            ['code' => '09317017', 'name' => 'Curuan'],
            ['code' => '09317018', 'name' => 'Dabuy'],
            ['code' => '09317019', 'name' => 'Divisoria'],
            ['code' => '09317020', 'name' => 'Dulian (Upper)'],
            ['code' => '09317021', 'name' => 'Dulian (Lower)'],
            ['code' => '09317022', 'name' => 'Guiwan'],
            ['code' => '09317023', 'name' => 'La Paz'],
            ['code' => '09317024', 'name' => 'Labuan'],
            ['code' => '09317025', 'name' => 'Lamisahan'],
            ['code' => '09317026', 'name' => 'Landang Gua'],
            ['code' => '09317027', 'name' => 'Landang Laum'],
            ['code' => '09317028', 'name' => 'Lanzones'],
            ['code' => '09317029', 'name' => 'Lapakan'],
            ['code' => '09317030', 'name' => 'Latuan'],
            ['code' => '09317031', 'name' => 'Licomo'],
            ['code' => '09317032', 'name' => 'Limaong'],
            ['code' => '09317033', 'name' => 'Limpapa'],
            ['code' => '09317034', 'name' => 'Lubigan'],
            ['code' => '09317035', 'name' => 'Lumayang'],
            ['code' => '09317036', 'name' => 'Lumbangan'],
            ['code' => '09317037', 'name' => 'Lunzuran'],
            ['code' => '09317038', 'name' => 'Maasin'],
            ['code' => '09317039', 'name' => 'Malagutay'],
            ['code' => '09317040', 'name' => 'Mampang'],
            ['code' => '09317041', 'name' => 'Manalipa'],
            ['code' => '09317042', 'name' => 'Mangusu'],
            ['code' => '09317043', 'name' => 'Manicahan'],
            ['code' => '09317044', 'name' => 'Mariki'],
            ['code' => '09317045', 'name' => 'Mercedes'],
            ['code' => '09317046', 'name' => 'Muti'],
            ['code' => '09317047', 'name' => 'Pamucutan'],
            ['code' => '09317048', 'name' => 'Pangapuyan'],
            ['code' => '09317049', 'name' => 'Panubigan'],
            ['code' => '09317050', 'name' => 'Pasilmanta'],
            ['code' => '09317051', 'name' => 'Pasonanca'],
            ['code' => '09317052', 'name' => 'Putik'],
            ['code' => '09317053', 'name' => 'Quiniput'],
            ['code' => '09317054', 'name' => 'Recodo'],
            ['code' => '09317055', 'name' => 'Rio Hondo'],
            ['code' => '09317056', 'name' => 'Salaan'],
            ['code' => '09317057', 'name' => 'San Jose Cawa-Cawa'],
            ['code' => '09317058', 'name' => 'San Jose Gusu'],
            ['code' => '09317059', 'name' => 'San Roque'],
            ['code' => '09317060', 'name' => 'Sangali'],
            ['code' => '09317061', 'name' => 'Santa Barbara'],
            ['code' => '09317062', 'name' => 'Santa Catalina'],
            ['code' => '09317063', 'name' => 'Santa Maria'],
            ['code' => '09317064', 'name' => 'Santo Niño'],
            ['code' => '09317065', 'name' => 'Sibulao'],
            ['code' => '09317066', 'name' => 'Sinubong'],
            ['code' => '09317067', 'name' => 'Sinunuc'],
            ['code' => '09317068', 'name' => 'Tagasilay'],
            ['code' => '09317069', 'name' => 'Taguiti'],
            ['code' => '09317070', 'name' => 'Talabaan'],
            ['code' => '09317071', 'name' => 'Talisayan'],
            ['code' => '09317072', 'name' => 'Talon-Talon'],
            ['code' => '09317073', 'name' => 'Taluksangay'],
            ['code' => '09317074', 'name' => 'Tetuan'],
            ['code' => '09317075', 'name' => 'Tictapul'],
            ['code' => '09317076', 'name' => 'Tigbalabag'],
            ['code' => '09317077', 'name' => 'Tigtabon'],
            ['code' => '09317078', 'name' => 'Tolosa'],
            ['code' => '09317079', 'name' => 'Tumaga'],
            ['code' => '09317080', 'name' => 'Tumalutab'],
            ['code' => '09317081', 'name' => 'Tumitus'],
            ['code' => '09317082', 'name' => 'Victoria'],
            ['code' => '09317083', 'name' => 'Vitali'],
            ['code' => '09317084', 'name' => 'Zambowood'],
            ['code' => '09317085', 'name' => 'Zone I'],
            ['code' => '09317086', 'name' => 'Zone II'],
            ['code' => '09317087', 'name' => 'Zone III'],
            ['code' => '09317088', 'name' => 'Zone IV'],
        ];

        $count = 0;
        $cityId = $zamboangaCity->code;
        
        // Show what we're using for city_id
        $this->command->info("Using city_id: {$cityId} for barangays");
        
        foreach ($barangays as $barangay) {
            try {
                $existingBarangay = Barangay::where('code', $barangay['code'])->first();
                
                if (!$existingBarangay) {
                    // Fix: Use a shorter correspondence code that fits in varchar(9)
                    // Extract just the last digits for a shorter code
                    $shortCode = substr($barangay['code'], -5); // Take only last 5 digits
                    $correspondenceCode = '9317'.$shortCode; // 9 chars total
                    
                    DB::table('barangays')->insert([
                        'code' => $barangay['code'],
                        'name' => $barangay['name'],
                        'city_id' => $cityId,
                        'province_id' => '09317',
                        'region_id' => $regionCode,
                        'correspondence_code' => $correspondenceCode
                    ]);
                    $count++;
                }
            } catch (\Exception $e) {
                $this->command->error("Error adding barangay {$barangay['name']}: " . $e->getMessage());
            }
        }
        
        $this->command->info("Added {$count} barangays to Zamboanga City");
        
    } catch (\Exception $e) {
        $this->command->error("Error: " . $e->getMessage());
        $this->command->error($e->getTraceAsString());
    }
}

/**
 * Check and display column sizes to help diagnose issues
 */
private function checkColumnSizes()
{
    $tables = ['regions', 'provinces', 'cities', 'barangays'];
    $columns = ['code', 'region_id', 'province_id', 'city_id', 'correspondence_code'];
    
    $this->command->info("Checking database schema...");
    
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $columnInfo = DB::select("SHOW COLUMNS FROM {$table} WHERE Field IN ('" . implode("','", $columns) . "')");
            $this->command->info("Table {$table} column sizes:");
            
            foreach ($columnInfo as $column) {
                $this->command->info("  - {$column->Field}: {$column->Type}");
            }
        } else {
            $this->command->error("Table {$table} does not exist!");
        }
    }
}
}