<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PenyesuaianStok;
use Illuminate\Auth\Access\HandlesAuthorization;

class PenyesuaianStokPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PenyesuaianStok');
    }

    public function view(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('View:PenyesuaianStok');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PenyesuaianStok');
    }

    public function update(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('Update:PenyesuaianStok');
    }

    public function delete(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('Delete:PenyesuaianStok');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PenyesuaianStok');
    }

    public function restore(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('Restore:PenyesuaianStok');
    }

    public function forceDelete(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('ForceDelete:PenyesuaianStok');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PenyesuaianStok');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PenyesuaianStok');
    }

    public function replicate(AuthUser $authUser, PenyesuaianStok $penyesuaianStok): bool
    {
        return $authUser->can('Replicate:PenyesuaianStok');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PenyesuaianStok');
    }

}