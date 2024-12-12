<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name'=>'create-spm']);
        Permission::create(['name'=>'create-sppb']);
        Permission::create(['name'=>'timbang-masuk']);
        Permission::create(['name'=>'timbang-keluar']);
        Permission::create(['name'=>'input-karung']);
        Permission::create(['name'=>'approval-avg-berat-karung']);
        

        Role::create(['name' => 'operator-registrasi']);
        Role::create(['name' => 'operator-timbangan']);
        Role::create(['name' => 'operator-b10']);
        Role::create(['name' => 'supervisor-timbangan-registrasi']);
        Role::create(['name' => 'supervisor-b10']);
        Role::create(['name' => 'manager-logistik']);
        Role::create(['name' => 'administrator']);

        $roleAdmin = Role::findByName('administrator');
        $roleAdmin->givePermissionTo('create-spm');
        $roleAdmin->givePermissionTo('create-sppb');
        $roleAdmin->givePermissionTo('timbang-masuk');
        $roleAdmin->givePermissionTo('timbang-keluar');
        $roleAdmin->givePermissionTo('input-karung');
        $roleAdmin->givePermissionTo('approval-avg-berat-karung');

        $rolemanagerlogistik = Role::findByName('manager-logistik');
        $rolemanagerlogistik->givePermissionTo('create-spm');
        $rolemanagerlogistik->givePermissionTo('create-sppb');
        $rolemanagerlogistik->givePermissionTo('timbang-masuk');
        $rolemanagerlogistik->givePermissionTo('timbang-keluar');
        $rolemanagerlogistik->givePermissionTo('input-karung');
        $rolemanagerlogistik->givePermissionTo('approval-avg-berat-karung');

        $rolesupervisortimbanganregistrasi = Role::findByName('supervisor-timbangan-registrasi');
        $rolesupervisortimbanganregistrasi->givePermissionTo('create-spm');
        $rolesupervisortimbanganregistrasi->givePermissionTo('create-sppb');
        $rolesupervisortimbanganregistrasi->givePermissionTo('timbang-masuk');
        $rolesupervisortimbanganregistrasi->givePermissionTo('timbang-keluar');

        $rolesupervisorb10 = Role::findByName('supervisor-b10');
        $rolesupervisorb10->givePermissionTo('input-karung');
        $rolesupervisorb10->givePermissionTo('approval-avg-berat-karung');

        $roleoperatorregistrasi = Role::findByName('operator-registrasi');
        $roleoperatorregistrasi->givePermissionTo('create-spm');
        $roleoperatorregistrasi->givePermissionTo('create-sppb');

        $roleoperatortimbangan = Role::findByName('operator-timbangan');
        $roleoperatortimbangan->givePermissionTo('timbang-masuk');
        $roleoperatortimbangan->givePermissionTo('timbang-keluar');

        $roleoperatorb10 = Role::findByName('operator-b10');
        $roleoperatorb10->givePermissionTo('input-karung');
        
       
        

        

    }
}
