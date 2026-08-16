<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roleId = DB::table('roles')->where('name', 'Kasir')->value('id');
        if (! $roleId) {
            return;
        }

        $names = [
            'View:PosKasir',
            'ViewAny:PosShift',
            'View:PosShift',
            'Create:PosShift',
            'Update:PosShift',
            'ViewAny:Pelanggan',
            'View:Pelanggan',
            'Create:Pelanggan',
        ];

        $permissions = DB::table('permissions')
            ->where('guard_name', 'web')
            ->whereIn('name', $names)
            ->pluck('id');

        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
        if ($permissions->isNotEmpty()) {
            DB::table('role_has_permissions')->insert(
                $permissions->map(fn (int $permissionId) => [
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ])->all()
            );
        }
    }

    public function down(): void
    {
        // Permissions are deliberately not restored: an operator must assign
        // any exceptional access explicitly through role management.
    }
};
