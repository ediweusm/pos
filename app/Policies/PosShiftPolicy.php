<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PosShift;
use Illuminate\Auth\Access\HandlesAuthorization;

class PosShiftPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PosShift');
    }

    public function view(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('View:PosShift');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PosShift');
    }

    public function update(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('Update:PosShift');
    }

    public function delete(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('Delete:PosShift');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PosShift');
    }

    public function restore(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('Restore:PosShift');
    }

    public function forceDelete(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('ForceDelete:PosShift');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PosShift');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PosShift');
    }

    public function replicate(AuthUser $authUser, PosShift $posShift): bool
    {
        return $authUser->can('Replicate:PosShift');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PosShift');
    }

}