<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionroleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // Membuat izin
        // // pengguna
        // Permission::create(['name' => 'tambah pengguna']);
        // Permission::create(['name' => 'lihat pengguna']);
        // Permission::create(['name' => 'edit pengguna']);
        // Permission::create(['name' => 'hapus pengguna']);

        // // lokasi
        // Permission::create(['name' => 'tambah lokasi']);
        // Permission::create(['name' => 'lihat lokasi']);
        // Permission::create(['name' => 'edit lokasi']);
        // Permission::create(['name' => 'hapus lokasi']);

        // // Membuat peran
        // // developer
        // $roledeveloper = Role::create(['name' => 'Developer']);
        // // pengguna
        // $roledeveloper->givePermissionTo('tambah pengguna');
        // $roledeveloper->givePermissionTo('lihat pengguna');
        // $roledeveloper->givePermissionTo('edit pengguna');
        // $roledeveloper->givePermissionTo('hapus pengguna');
        // // lokasi
        // $roledeveloper->givePermissionTo('tambah lokasi');
        // $roledeveloper->givePermissionTo('lihat lokasi');
        // $roledeveloper->givePermissionTo('edit lokasi');
        // $roledeveloper->givePermissionTo('hapus lokasi');


        // // owner
        // $roleowner = Role::create(['name' => 'Owner']);
        // // pengguna
        // $roleowner->givePermissionTo('lihat pengguna');
        // $roleowner->givePermissionTo('edit pengguna');
        // // lokasi
        // $roleowner->givePermissionTo('lihat lokasi');


        // // super admin
        // $rolesuperadmin = Role::create(['name' => 'Superadmin']);
        // // pengguna
        // $rolesuperadmin->givePermissionTo('lihat pengguna');
        // $rolesuperadmin->givePermissionTo('edit pengguna');
        // // lokasi
        // $rolesuperadmin->givePermissionTo('tambah lokasi');
        // $rolesuperadmin->givePermissionTo('lihat lokasi');
        // $rolesuperadmin->givePermissionTo('edit lokasi');


        // // admin
        // $admin = Role::create(['name' => 'admin']);
        // // pengguna
        // $admin->givePermissionTo('lihat pengguna');
        // $admin->givePermissionTo('edit pengguna');
        // // lokasi
        // $admin->givePermissionTo('lihat lokasi');

        // Membuat izin
        // pengguna
        // Permission::create(['name' => 'tambah daftarpenyewa']);
        // Permission::create(['name' => 'lihat daftarpenyewa']);
        // Permission::create(['name' => 'edit daftarpenyewa']);
        // Permission::create(['name' => 'hapus daftarpenyewa']);

        // Membuat peran
        // developer
        $roledeveloper = Role::findByName('Developer');
        $roledeveloper->revokePermissionTo('tambah daftarpenyewa');
        $roledeveloper->revokePermissionTo('lihat daftarpenyewa');
        $roledeveloper->revokePermissionTo('edit daftarpenyewa');
        $roledeveloper->revokePermissionTo('hapus daftarpenyewa');

        // owner
        $roleowner = Role::findByName('Owner');
        $roleowner->revokePermissionTo('lihat daftarpenyewa');
       
        // super admin
        $rolesuperadmin = Role::findByName('Superadmin');
        $rolesuperadmin->revokePermissionTo('lihat daftarpenyewa');

        // admin
        $admin = Role::findByName('admin');
        $admin->revokePermissionTo('lihat daftarpenyewa');
    }
}
