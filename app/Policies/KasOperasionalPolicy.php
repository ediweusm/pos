<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KasOperasional;
use Illuminate\Auth\Access\HandlesAuthorization;

class KasOperasionalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KasOperasional');
    }

    public function view(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('View:KasOperasional');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KasOperasional');
    }

    public function update(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('Update:KasOperasional');
    }

    public function delete(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('Delete:KasOperasional');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KasOperasional');
    }

    public function restore(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('Restore:KasOperasional');
    }

    public function forceDelete(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('ForceDelete:KasOperasional');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KasOperasional');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KasOperasional');
    }

    public function replicate(AuthUser $authUser, KasOperasional $kasOperasional): bool
    {
        return $authUser->can('Replicate:KasOperasional');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KasOperasional');
    }

}