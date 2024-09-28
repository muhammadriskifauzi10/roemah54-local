<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['name' => 'Kamar', 'route' => null, 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => null, 'order' => 1],
            ['name' => 'Daftar Kamar', 'route' => 'kamar', 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => 1, 'order' => 1],
            ['name' => 'Harga Kamar', 'route' => 'harga', 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => 1, 'order' => 2],
            ['name' => 'Penyewa', 'route' => null, 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => null, 'order' => 2],
            ['name' => 'Daftar Penyewa', 'route' => 'daftarpenyewa', 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => 4, 'order' => 1],
            ['name' => 'Penyewaan Kamar', 'route' => 'penyewaankamar', 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => 4, 'order' => 2],
            ['name' => 'Laporan', 'route' => null, 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => null, 'order' => 3],
            ['name' => 'Transaksi', 'route' => 'transaksi', 'role' => 'Developer|Owner|Superadmin|Admin', 'parent_id' => 7, 'order' => 1],
            ['name' => 'Manajemen Pengguna', 'route' => null, 'role' => 'Developer', 'parent_id' => null, 'order' => 4],
            ['name' => 'Pengguna', 'route' => 'pengguna', 'role' => 'Developer', 'parent_id' => 9, 'order' => 1],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
