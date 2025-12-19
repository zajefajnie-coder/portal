<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register roles and permissions
        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        try {
            // Create roles if they don't exist
            $roles = ['admin', 'moderator', 'user'];
            foreach ($roles as $role) {
                Role::firstOrCreate(['name' => $role]);
            }
        } catch (\Exception $e) {
            // Database might not be migrated yet
        }
    }
}



