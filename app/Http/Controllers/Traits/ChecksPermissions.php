<?php

namespace App\Http\Controllers\Traits;

trait ChecksPermissions
{
    /**
     * Check if user has read permission and abort if not
     *
     * @param string $form Form name
     * @return void
     */
    protected function checkReadPermission(string $form): void
    {
        $user = auth()->user();
        if (!$user->canRead($form)) {
            abort(403, "You do not have permission to view {$form}.");
        }
    }

    /**
     * Check if user has write permission and abort if not
     *
     * @param string $form Form name
     * @return void
     */
    protected function checkWritePermission(string $form): void
    {
        $user = auth()->user();
        if (!$user->canWrite($form)) {
            abort(403, "You do not have permission to create or edit {$form}.");
        }
    }

    /**
     * Check if user has delete permission and abort if not
     *
     * @param string $form Form name
     * @return void
     */
    protected function checkDeletePermission(string $form): void
    {
        $user = auth()->user();
        if (!$user->canDelete($form)) {
            abort(403, "You do not have permission to delete {$form}.");
        }
    }

    /**
     * Get permission flags for a form to pass to views
     *
     * @param string $form Form name
     * @return array
     */
    protected function getPermissionFlags(string $form): array
    {
        $user = auth()->user();
        return [
            'canWrite' => $user->canWrite($form),
            'canDelete' => $user->canDelete($form),
        ];
    }
}

