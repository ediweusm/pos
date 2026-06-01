<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JurnalBarang;
use Illuminate\Auth\Access\HandlesAuthorization;

class JurnalBarangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JurnalBarang');
    }

    public function view(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('View:JurnalBarang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JurnalBarang');
    }

    public function update(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('Update:JurnalBarang');
    }

    public function delete(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('Delete:JurnalBarang');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JurnalBarang');
    }

    public function restore(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('Restore:JurnalBarang');
    }

    public function forceDelete(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('ForceDelete:JurnalBarang');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JurnalBarang');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JurnalBarang');
    }

    public function replicate(AuthUser $authUser, JurnalBarang $jurnalBarang): bool
    {
        return $authUser->can('Replicate:JurnalBarang');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JurnalBarang');
    }

}