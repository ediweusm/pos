<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Gudang;
use Illuminate\Auth\Access\HandlesAuthorization;

class GudangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Gudang');
    }

    public function view(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('View:Gudang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Gudang');
    }

    public function update(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('Update:Gudang');
    }

    public function delete(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('Delete:Gudang');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Gudang');
    }

    public function restore(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('Restore:Gudang');
    }

    public function forceDelete(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('ForceDelete:Gudang');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Gudang');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Gudang');
    }

    public function replicate(AuthUser $authUser, Gudang $gudang): bool
    {
        return $authUser->can('Replicate:Gudang');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Gudang');
    }

}