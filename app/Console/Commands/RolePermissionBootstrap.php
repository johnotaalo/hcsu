<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionBootstrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcsu:role-bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles and permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $roles = \App\Enums\UserType::getKeys();

        $permissions = [
            "Principals",
            "Templates",
            "Agencies",
            "View Agency Focal Points",
            "Get Passport Types",
            "Get Countries",
            "Get Principal Options",
            "Vehicles",
            "Get API Data",
            "TIMS",
            "Adobe Sign Documents",
            "Adobe Sign Signatories",
            "VAT",
            "Focal Point Applications",
            "Other Clients",
            "Users"
        ];

        $this->line("------------------- Setting Up Roles:");

        foreach ($roles as $role) {
            $role = Role::updateOrCreate(['name'    =>  $role, 'guard_name' =>  'api']);
            $this->info("Created {$role->name} Role");
        }

        $this->line("------------------- Setting Up Permissions:");

        $adminRole = Role::where("name", "Administrator")->first();

        foreach ($permissions as $perm_name) {
            $permission = Permission::updateOrCreate(['name'    =>  $perm_name, 'guard_name'    =>  'api']);

            $adminRole->givePermissionTo($permission);

            $this->info("Created {$permission->name} Permission");
        }

        $adminUsers = \App\User::where('user_type', "0")->get();
        $permissions = Permission::all();

        foreach ($adminUsers as $user) {
            if($user->type == "Administrator"){
                $user->assignRole($adminRole);
                foreach ($permissions as $permission) {
                    $user->givePermissionTo($permission);
                }
            }
        }

        $this->info("All permissions granted to {$adminRole->name}");
        $this->line('------------- Application Bootstrapping is Complete: \n');
    }
}
