<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions setup
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $areaManagerRole = Role::firstOrCreate(['name' => 'Area Manager']);
        $picRole = Role::firstOrCreate(['name' => 'PIC Digital Cabang']);

        // 2. Create 5 Branches (PT Aspacindo Kedaton Motor Yamaha Dealers)
        $branchesData = [
            [
                'kode' => 'AKM-PKU-01',
                'nama_cabang' => 'AKM Pekanbaru',
                'alamat' => 'Jl. Jendral Sudirman No. 120, Pekanbaru, Riau',
                'area' => 'Pekanbaru City',
                'manager_name' => 'Budi Santoso, S.E.',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-SRK-02',
                'nama_cabang' => 'AKM Sorek',
                'alamat' => 'Jl. Lintas Timur No. 45, Sorek Satu, Pelalawan, Riau',
                'area' => 'Pelalawan & Sorek',
                'manager_name' => 'Hendra Wijaya',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-KND-03',
                'nama_cabang' => 'AKM Kandis',
                'alamat' => 'Jl. Raya Pekanbaru-Duri Km 72, Kandis, Siak, Riau',
                'area' => 'Siak & Kandis',
                'manager_name' => 'Siti Nurhaliza, S.E.',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-SPG-04',
                'nama_cabang' => 'AKM Sungai Pagar',
                'alamat' => 'Jl. Poros Kampar Kiri No. 88, Sungai Pagar, Kampar, Riau',
                'area' => 'Kampar & Sungai Pagar',
                'manager_name' => 'Rahmat Hidayat',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-MDN-05',
                'nama_cabang' => 'AKM Medan',
                'alamat' => 'Jl. Sisingamangaraja No. 210, Medan, Sumatera Utara',
                'area' => 'Medan & North Sumatra',
                'manager_name' => 'Agus Pratama',
                'status' => 'active',
            ],
        ];

        $branches = collect();
        foreach ($branchesData as $bData) {
            $branches->push(Branch::create($bData));
        }

        // 3. Create 2 Super Admin Users
        $superAdmin1 = User::create([
            'name' => 'Muhammad Zidane Damara',
            'email' => 'zidane@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $superAdmin1->assignRole($superAdminRole);

        // Super Admin 1 Alias (superadmin@aspacindo.co.id for convenience)
        $superAdmin1Alias = User::create([
            'name' => 'Muhammad Zidane Damara (Super Admin)',
            'email' => 'superadmin@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $superAdmin1Alias->assignRole($superAdminRole);

        $superAdmin2 = User::create([
            'name' => 'Amalia Putri',
            'email' => 'amalia@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $superAdmin2->assignRole($superAdminRole);

        // 4. Create Area Manager User
        $areaManager = User::create([
            'name' => 'Suparman',
            'email' => 'suparman@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $areaManager->assignRole($areaManagerRole);

        // Area Manager Alias (areamanager@aspacindo.co.id for convenience)
        $areaManagerAlias = User::create([
            'name' => 'Suparman (Area Manager)',
            'email' => 'areamanager@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $areaManagerAlias->assignRole($areaManagerRole);

        // 5. Create 5 PIC Digital Users (1 per branch)
        $picSlugList = ['pekanbaru', 'sorek', 'kandis', 'sungaipagar', 'medan'];
        foreach ($branches as $index => $branch) {
            $slug = $picSlugList[$index];

            // Primary Account (e.g. pekanbaru@aspacindo.co.id)
            $picUser = User::create([
                'name' => 'PIC Digital ' . $branch->nama_cabang,
                'email' => $slug . '@aspacindo.co.id',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'status' => 'active',
            ]);
            $picUser->assignRole($picRole);

            // Alias Account with pic. prefix (e.g. pic.pekanbaru@aspacindo.co.id)
            $picUserAlias = User::create([
                'name' => 'PIC Digital ' . $branch->nama_cabang . ' (Alias)',
                'email' => 'pic.' . $slug . '@aspacindo.co.id',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'status' => 'active',
            ]);
            $picUserAlias->assignRole($picRole);
        }
    }
}
