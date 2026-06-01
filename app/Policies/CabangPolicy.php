<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cabang;
use Illuminate\Auth\Access\HandlesAuthorization;

class CabangPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cabang');
    }

    public function view(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('View:Cabang');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cabang');
    }

    public function update(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('Update:Cabang');
    }

    public function delete(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('Delete:Cabang');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Cabang');
    }

    public function restore(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('Restore:Cabang');
    }

    public function forceDelete(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('ForceDelete:Cabang');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Cabang');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Cabang');
    }

    public function replicate(AuthUser $authUser, Cabang $cabang): bool
    {
        return $authUser->can('Replicate:Cabang');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cabang');
    }

}