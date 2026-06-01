<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AuditKas;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditKasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AuditKas');
    }

    public function view(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('View:AuditKas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AuditKas');
    }

    public function update(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('Update:AuditKas');
    }

    public function delete(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('Delete:AuditKas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AuditKas');
    }

    public function restore(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('Restore:AuditKas');
    }

    public function forceDelete(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('ForceDelete:AuditKas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AuditKas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AuditKas');
    }

    public function replicate(AuthUser $authUser, AuditKas $auditKas): bool
    {
        return $authUser->can('Replicate:AuditKas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AuditKas');
    }

}