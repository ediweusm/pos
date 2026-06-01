<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AkunCfg;
use Illuminate\Auth\Access\HandlesAuthorization;

class AkunCfgPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AkunCfg');
    }

    public function view(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('View:AkunCfg');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AkunCfg');
    }

    public function update(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('Update:AkunCfg');
    }

    public function delete(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('Delete:AkunCfg');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AkunCfg');
    }

    public function restore(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('Restore:AkunCfg');
    }

    public function forceDelete(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('ForceDelete:AkunCfg');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AkunCfg');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AkunCfg');
    }

    public function replicate(AuthUser $authUser, AkunCfg $akunCfg): bool
    {
        return $authUser->can('Replicate:AkunCfg');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AkunCfg');
    }

}