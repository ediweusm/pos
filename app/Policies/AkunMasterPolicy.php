<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AkunMaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class AkunMasterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AkunMaster');
    }

    public function view(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('View:AkunMaster');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AkunMaster');
    }

    public function update(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('Update:AkunMaster');
    }

    public function delete(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('Delete:AkunMaster');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AkunMaster');
    }

    public function restore(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('Restore:AkunMaster');
    }

    public function forceDelete(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('ForceDelete:AkunMaster');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AkunMaster');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AkunMaster');
    }

    public function replicate(AuthUser $authUser, AkunMaster $akunMaster): bool
    {
        return $authUser->can('Replicate:AkunMaster');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AkunMaster');
    }

}