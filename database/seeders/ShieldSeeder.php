<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[{"id":1,"name":"Administrator","email":"admin@admin.com","email_verified_at":null,"created_at":"2026-05-30T16:04:25.000000Z","updated_at":"2026-05-30T16:04:25.000000Z","cabang_id":1,"gudang_id":2,"password":"password","roles":["super_admin"],"permissions":[]},{"id":2,"name":"Dila","email":"dila@admin.com","email_verified_at":null,"created_at":"2026-05-30T19:32:22.000000Z","updated_at":"2026-06-04T12:29:06.000000Z","cabang_id":1,"gudang_id":2,"password":"password","roles":["Kasir"],"permissions":[]},{"id":3,"name":"Fitri","email":"fitri@admin.com","email_verified_at":null,"created_at":"2026-05-30T19:33:36.000000Z","updated_at":"2026-06-04T12:33:34.000000Z","cabang_id":1,"gudang_id":1,"password":"password","roles":["Admin"],"permissions":[]}]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:AkunCfg","View:AkunCfg","Create:AkunCfg","Update:AkunCfg","Delete:AkunCfg","DeleteAny:AkunCfg","Restore:AkunCfg","ForceDelete:AkunCfg","ForceDeleteAny:AkunCfg","RestoreAny:AkunCfg","Replicate:AkunCfg","Reorder:AkunCfg","ViewAny:AkunMaster","View:AkunMaster","Create:AkunMaster","Update:AkunMaster","Delete:AkunMaster","DeleteAny:AkunMaster","Restore:AkunMaster","ForceDelete:AkunMaster","ForceDeleteAny:AkunMaster","RestoreAny:AkunMaster","Replicate:AkunMaster","Reorder:AkunMaster","ViewAny:AuditKas","View:AuditKas","Create:AuditKas","Update:AuditKas","Delete:AuditKas","DeleteAny:AuditKas","Restore:AuditKas","ForceDelete:AuditKas","ForceDeleteAny:AuditKas","RestoreAny:AuditKas","Replicate:AuditKas","Reorder:AuditKas","ViewAny:Gudang","View:Gudang","Create:Gudang","Update:Gudang","Delete:Gudang","DeleteAny:Gudang","Restore:Gudang","ForceDelete:Gudang","ForceDeleteAny:Gudang","RestoreAny:Gudang","Replicate:Gudang","Reorder:Gudang","ViewAny:JurnalBarang","View:JurnalBarang","Create:JurnalBarang","Update:JurnalBarang","Delete:JurnalBarang","DeleteAny:JurnalBarang","Restore:JurnalBarang","ForceDelete:JurnalBarang","ForceDeleteAny:JurnalBarang","RestoreAny:JurnalBarang","Replicate:JurnalBarang","Reorder:JurnalBarang","ViewAny:Kategori","View:Kategori","Create:Kategori","Update:Kategori","Delete:Kategori","DeleteAny:Kategori","Restore:Kategori","ForceDelete:Kategori","ForceDeleteAny:Kategori","RestoreAny:Kategori","Replicate:Kategori","Reorder:Kategori","ViewAny:Pelanggan","View:Pelanggan","Create:Pelanggan","Update:Pelanggan","Delete:Pelanggan","DeleteAny:Pelanggan","Restore:Pelanggan","ForceDelete:Pelanggan","ForceDeleteAny:Pelanggan","RestoreAny:Pelanggan","Replicate:Pelanggan","Reorder:Pelanggan","ViewAny:Pembelian","View:Pembelian","Create:Pembelian","Update:Pembelian","Delete:Pembelian","DeleteAny:Pembelian","Restore:Pembelian","ForceDelete:Pembelian","ForceDeleteAny:Pembelian","RestoreAny:Pembelian","Replicate:Pembelian","Reorder:Pembelian","ViewAny:PenyesuaianStok","View:PenyesuaianStok","Create:PenyesuaianStok","Update:PenyesuaianStok","Delete:PenyesuaianStok","DeleteAny:PenyesuaianStok","Restore:PenyesuaianStok","ForceDelete:PenyesuaianStok","ForceDeleteAny:PenyesuaianStok","RestoreAny:PenyesuaianStok","Replicate:PenyesuaianStok","Reorder:PenyesuaianStok","ViewAny:Produk","View:Produk","Create:Produk","Update:Produk","Delete:Produk","DeleteAny:Produk","Restore:Produk","ForceDelete:Produk","ForceDeleteAny:Produk","RestoreAny:Produk","Replicate:Produk","Reorder:Produk","ViewAny:Satuan","View:Satuan","Create:Satuan","Update:Satuan","Delete:Satuan","DeleteAny:Satuan","Restore:Satuan","ForceDelete:Satuan","ForceDeleteAny:Satuan","RestoreAny:Satuan","Replicate:Satuan","Reorder:Satuan","ViewAny:Supplier","View:Supplier","Create:Supplier","Update:Supplier","Delete:Supplier","DeleteAny:Supplier","Restore:Supplier","ForceDelete:Supplier","ForceDeleteAny:Supplier","RestoreAny:Supplier","Replicate:Supplier","Reorder:Supplier","ViewAny:TransaksiKas","View:TransaksiKas","Create:TransaksiKas","Update:TransaksiKas","Delete:TransaksiKas","DeleteAny:TransaksiKas","Restore:TransaksiKas","ForceDelete:TransaksiKas","ForceDeleteAny:TransaksiKas","RestoreAny:TransaksiKas","Replicate:TransaksiKas","Reorder:TransaksiKas","ViewAny:Role","View:Role","Create:Role","Update:Role","Delete:Role","DeleteAny:Role","Restore:Role","ForceDelete:Role","ForceDeleteAny:Role","RestoreAny:Role","Replicate:Role","Reorder:Role","View:Dashboard","View:LaporanBukuBesar","View:LaporanLabaRugi","View:LaporanNeraca","View:PosKasir","View:TutupBuku","View:ProdukTerlarisChart","View:StatsOverview","View:TrenPenjualanChart","ViewAny:MutasiStok","View:MutasiStok","Create:MutasiStok","Update:MutasiStok","Delete:MutasiStok","DeleteAny:MutasiStok","Restore:MutasiStok","ForceDelete:MutasiStok","ForceDeleteAny:MutasiStok","RestoreAny:MutasiStok","Replicate:MutasiStok","Reorder:MutasiStok","ViewAny:User","View:User","Create:User","Update:User","Delete:User","DeleteAny:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","ViewAny:KasOperasional","View:KasOperasional","Create:KasOperasional","Update:KasOperasional","Delete:KasOperasional","DeleteAny:KasOperasional","Restore:KasOperasional","ForceDelete:KasOperasional","ForceDeleteAny:KasOperasional","RestoreAny:KasOperasional","Replicate:KasOperasional","Reorder:KasOperasional","ViewAny:Cabang","View:Cabang","Create:Cabang","Update:Cabang","Delete:Cabang","DeleteAny:Cabang","Restore:Cabang","ForceDelete:Cabang","ForceDeleteAny:Cabang","RestoreAny:Cabang","Replicate:Cabang","Reorder:Cabang","ViewAny:PosShift","View:PosShift","Create:PosShift","Update:PosShift","Delete:PosShift","DeleteAny:PosShift","Restore:PosShift","ForceDelete:PosShift","ForceDeleteAny:PosShift","RestoreAny:PosShift","Replicate:PosShift","Reorder:PosShift"]},{"name":"Kasir","guard_name":"web","permissions":["ViewAny:AkunCfg","View:AkunCfg","Create:AkunCfg","Update:AkunCfg","Delete:AkunCfg","DeleteAny:AkunCfg","Restore:AkunCfg","ForceDelete:AkunCfg","ForceDeleteAny:AkunCfg","RestoreAny:AkunCfg","Replicate:AkunCfg","Reorder:AkunCfg","ViewAny:AkunMaster","View:AkunMaster","Create:AkunMaster","Update:AkunMaster","Delete:AkunMaster","DeleteAny:AkunMaster","Restore:AkunMaster","ForceDelete:AkunMaster","ForceDeleteAny:AkunMaster","RestoreAny:AkunMaster","Replicate:AkunMaster","Reorder:AkunMaster","ViewAny:AuditKas","View:AuditKas","Create:AuditKas","Update:AuditKas","Delete:AuditKas","DeleteAny:AuditKas","Restore:AuditKas","ForceDelete:AuditKas","ForceDeleteAny:AuditKas","RestoreAny:AuditKas","Replicate:AuditKas","Reorder:AuditKas","ViewAny:JurnalBarang","View:JurnalBarang","Create:JurnalBarang","Update:JurnalBarang","Delete:JurnalBarang","DeleteAny:JurnalBarang","Restore:JurnalBarang","ForceDelete:JurnalBarang","ForceDeleteAny:JurnalBarang","RestoreAny:JurnalBarang","Replicate:JurnalBarang","Reorder:JurnalBarang","ViewAny:Kategori","View:Kategori","Create:Kategori","Update:Kategori","Delete:Kategori","DeleteAny:Kategori","Restore:Kategori","ForceDelete:Kategori","ForceDeleteAny:Kategori","RestoreAny:Kategori","Replicate:Kategori","Reorder:Kategori","ViewAny:Pelanggan","View:Pelanggan","Create:Pelanggan","Update:Pelanggan","Delete:Pelanggan","DeleteAny:Pelanggan","Restore:Pelanggan","ForceDelete:Pelanggan","ForceDeleteAny:Pelanggan","RestoreAny:Pelanggan","Replicate:Pelanggan","Reorder:Pelanggan","ViewAny:Produk","View:Produk","Create:Produk","Update:Produk","Delete:Produk","DeleteAny:Produk","Restore:Produk","ForceDelete:Produk","ForceDeleteAny:Produk","RestoreAny:Produk","Replicate:Produk","Reorder:Produk","ViewAny:Satuan","View:Satuan","Create:Satuan","Update:Satuan","Delete:Satuan","DeleteAny:Satuan","Restore:Satuan","ForceDelete:Satuan","ForceDeleteAny:Satuan","RestoreAny:Satuan","Replicate:Satuan","Reorder:Satuan","ViewAny:Supplier","View:Supplier","Create:Supplier","Update:Supplier","Delete:Supplier","DeleteAny:Supplier","Restore:Supplier","ForceDelete:Supplier","ForceDeleteAny:Supplier","RestoreAny:Supplier","Replicate:Supplier","Reorder:Supplier","ViewAny:TransaksiKas","View:TransaksiKas","Create:TransaksiKas","Update:TransaksiKas","Delete:TransaksiKas","DeleteAny:TransaksiKas","Restore:TransaksiKas","ForceDelete:TransaksiKas","ForceDeleteAny:TransaksiKas","RestoreAny:TransaksiKas","Replicate:TransaksiKas","Reorder:TransaksiKas","View:PosKasir","ViewAny:MutasiStok","View:MutasiStok","Create:MutasiStok","Update:MutasiStok","Delete:MutasiStok","DeleteAny:MutasiStok","Restore:MutasiStok","ForceDelete:MutasiStok","ForceDeleteAny:MutasiStok","RestoreAny:MutasiStok","Replicate:MutasiStok","Reorder:MutasiStok","ViewAny:KasOperasional","View:KasOperasional","Create:KasOperasional","Update:KasOperasional","Delete:KasOperasional","DeleteAny:KasOperasional","Restore:KasOperasional","ForceDelete:KasOperasional","ForceDeleteAny:KasOperasional","RestoreAny:KasOperasional","Replicate:KasOperasional","Reorder:KasOperasional","ViewAny:PosShift","View:PosShift","Create:PosShift","Update:PosShift","Delete:PosShift","DeleteAny:PosShift","Restore:PosShift","ForceDelete:PosShift","ForceDeleteAny:PosShift","RestoreAny:PosShift","Replicate:PosShift","Reorder:PosShift"]},{"name":"Admin","guard_name":"web","permissions":["ViewAny:AkunCfg","View:AkunCfg","Create:AkunCfg","Update:AkunCfg","Delete:AkunCfg","DeleteAny:AkunCfg","Restore:AkunCfg","ForceDelete:AkunCfg","ForceDeleteAny:AkunCfg","RestoreAny:AkunCfg","Replicate:AkunCfg","Reorder:AkunCfg","ViewAny:AkunMaster","View:AkunMaster","Create:AkunMaster","Update:AkunMaster","Delete:AkunMaster","DeleteAny:AkunMaster","Restore:AkunMaster","ForceDelete:AkunMaster","ForceDeleteAny:AkunMaster","RestoreAny:AkunMaster","Replicate:AkunMaster","Reorder:AkunMaster","ViewAny:AuditKas","View:AuditKas","Create:AuditKas","Update:AuditKas","Delete:AuditKas","DeleteAny:AuditKas","Restore:AuditKas","ForceDelete:AuditKas","ForceDeleteAny:AuditKas","RestoreAny:AuditKas","Replicate:AuditKas","Reorder:AuditKas","ViewAny:Gudang","View:Gudang","Create:Gudang","Update:Gudang","Delete:Gudang","DeleteAny:Gudang","Restore:Gudang","ForceDelete:Gudang","ForceDeleteAny:Gudang","RestoreAny:Gudang","Replicate:Gudang","Reorder:Gudang","ViewAny:JurnalBarang","View:JurnalBarang","Create:JurnalBarang","Update:JurnalBarang","Delete:JurnalBarang","DeleteAny:JurnalBarang","Restore:JurnalBarang","ForceDelete:JurnalBarang","ForceDeleteAny:JurnalBarang","RestoreAny:JurnalBarang","Replicate:JurnalBarang","Reorder:JurnalBarang","ViewAny:Kategori","View:Kategori","Create:Kategori","Update:Kategori","Delete:Kategori","DeleteAny:Kategori","Restore:Kategori","ForceDelete:Kategori","ForceDeleteAny:Kategori","RestoreAny:Kategori","Replicate:Kategori","Reorder:Kategori","ViewAny:Pelanggan","View:Pelanggan","Create:Pelanggan","Update:Pelanggan","Delete:Pelanggan","DeleteAny:Pelanggan","Restore:Pelanggan","ForceDelete:Pelanggan","ForceDeleteAny:Pelanggan","RestoreAny:Pelanggan","Replicate:Pelanggan","Reorder:Pelanggan","ViewAny:Pembelian","View:Pembelian","Create:Pembelian","Update:Pembelian","Delete:Pembelian","DeleteAny:Pembelian","Restore:Pembelian","ForceDelete:Pembelian","ForceDeleteAny:Pembelian","RestoreAny:Pembelian","Replicate:Pembelian","Reorder:Pembelian","ViewAny:PenyesuaianStok","View:PenyesuaianStok","Create:PenyesuaianStok","Update:PenyesuaianStok","Delete:PenyesuaianStok","DeleteAny:PenyesuaianStok","Restore:PenyesuaianStok","ForceDelete:PenyesuaianStok","ForceDeleteAny:PenyesuaianStok","RestoreAny:PenyesuaianStok","Replicate:PenyesuaianStok","Reorder:PenyesuaianStok","ViewAny:Produk","View:Produk","Create:Produk","Update:Produk","Delete:Produk","DeleteAny:Produk","Restore:Produk","ForceDelete:Produk","ForceDeleteAny:Produk","RestoreAny:Produk","Replicate:Produk","Reorder:Produk","ViewAny:Satuan","View:Satuan","Create:Satuan","Update:Satuan","Delete:Satuan","DeleteAny:Satuan","Restore:Satuan","ForceDelete:Satuan","ForceDeleteAny:Satuan","RestoreAny:Satuan","Replicate:Satuan","Reorder:Satuan","ViewAny:Supplier","View:Supplier","Create:Supplier","Update:Supplier","Delete:Supplier","DeleteAny:Supplier","Restore:Supplier","ForceDelete:Supplier","ForceDeleteAny:Supplier","RestoreAny:Supplier","Replicate:Supplier","Reorder:Supplier","ViewAny:TransaksiKas","View:TransaksiKas","Create:TransaksiKas","Update:TransaksiKas","Delete:TransaksiKas","DeleteAny:TransaksiKas","Restore:TransaksiKas","ForceDelete:TransaksiKas","ForceDeleteAny:TransaksiKas","RestoreAny:TransaksiKas","Replicate:TransaksiKas","Reorder:TransaksiKas","ViewAny:MutasiStok","View:MutasiStok","Create:MutasiStok","Update:MutasiStok","Delete:MutasiStok","DeleteAny:MutasiStok","Restore:MutasiStok","ForceDelete:MutasiStok","ForceDeleteAny:MutasiStok","RestoreAny:MutasiStok","Replicate:MutasiStok","Reorder:MutasiStok","ViewAny:User","View:User","Create:User","Update:User","Delete:User","DeleteAny:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","ViewAny:KasOperasional","View:KasOperasional","Create:KasOperasional","Update:KasOperasional","Delete:KasOperasional","DeleteAny:KasOperasional","Restore:KasOperasional","ForceDelete:KasOperasional","ForceDeleteAny:KasOperasional","RestoreAny:KasOperasional","Replicate:KasOperasional","Reorder:KasOperasional","ViewAny:Cabang","View:Cabang","Create:Cabang","Update:Cabang","Delete:Cabang","DeleteAny:Cabang","Restore:Cabang","ForceDelete:Cabang","ForceDeleteAny:Cabang","RestoreAny:Cabang","Replicate:Cabang","Reorder:Cabang","ViewAny:PosShift","View:PosShift","Create:PosShift","Update:PosShift","Delete:PosShift","DeleteAny:PosShift","Restore:PosShift","ForceDelete:PosShift","ForceDeleteAny:PosShift","RestoreAny:PosShift","Replicate:PosShift","Reorder:PosShift"]}]';
        $directPermissions = '[]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
