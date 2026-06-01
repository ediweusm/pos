<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TransaksiKas;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransaksiKasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TransaksiKas');
    }

    public function view(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('View:TransaksiKas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TransaksiKas');
    }

    public function update(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('Update:TransaksiKas');
    }

    public function delete(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('Delete:TransaksiKas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TransaksiKas');
    }

    public function restore(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('Restore:TransaksiKas');
    }

    public function forceDelete(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('ForceDelete:TransaksiKas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TransaksiKas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TransaksiKas');
    }

    public function replicate(AuthUser $authUser, TransaksiKas $transaksiKas): bool
    {
        return $authUser->can('Replicate:TransaksiKas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TransaksiKas');
    }

}