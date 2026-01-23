<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemError;
use Carbon\Carbon;

class SystemErrorSeeder extends Seeder
{
    public function run()
    {
        $types = ['PHP Exception', 'Database Error', 'Integrity Constraint', 'Timeout', 'Validation Error'];
        $files = ['app/Http/Controllers/OrderController.php', 'app/Models/User.php', 'resources/js/Pages/Dashboard.vue', 'app/Services/InventoryService.php'];
        
        for ($i = 0; $i < 20; $i++) {
            SystemError::create([
                'message' => 'Dummy error message ' . ($i + 1) . ': Unexpected value encountered.',
                'type' => $types[array_rand($types)],
                'file' => $files[array_rand($files)],
                'line' => rand(10, 500),
                'status' => rand(0, 100) > 20 ? 'pending' : 'resolved',
                'resolved_at' => null,
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
            ]);
        }
    }
}
