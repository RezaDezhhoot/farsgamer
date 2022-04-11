<?php

namespace App\Console\Commands;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetPermissionsAndRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert all of basic permissions and roles';

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
     * @param PermissionRepositoryInterface $permissionRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param UserRepositoryInterface $userRepository
     * @return int
     */
    public function handle(PermissionRepositoryInterface $permissionRepository , RoleRepositoryInterface $roleRepository , UserRepositoryInterface $userRepository)
    {
        $permissions = [
            ['name' => 'show_dashboard' , 'guard_name'=> 'web'], ['name' => 'show_orders', 'guard_name'=> 'web'], ['name' => 'edit_orders', 'guard_name'=> 'web'],
            ['name' => 'delete_orders', 'guard_name'=> 'web'], ['name' => 'show_transactions', 'guard_name'=> 'web'], ['name' => 'edit_transactions', 'guard_name'=> 'web'],
            ['name' => 'cancel_transactions', 'guard_name'=> 'web'], ['name' => 'show_chats', 'guard_name'=> 'web'], ['name' => 'edit_chats', 'guard_name'=> 'web'],
            ['name' => 'delete_chats', 'guard_name'=> 'web'], ['name' => 'show_tickets', 'guard_name'=> 'web'], ['name' => 'edit_tickets', 'guard_name'=> 'web'],
            ['name' => 'delete_tickets', 'guard_name'=> 'web'], ['name' => 'show_notifications', 'guard_name'=> 'web'], ['name' => 'edit_notifications', 'guard_name'=> 'web'],
            ['name' => 'delete_notifications', 'guard_name'=> 'web'], ['name' => 'show_comments', 'guard_name'=> 'web'], ['name' => 'edit_comments', 'guard_name'=> 'web'],
            ['name' => 'delete_comments', 'guard_name'=> 'web'], ['name' => 'show_users', 'guard_name'=> 'web'], ['name' => 'edit_users', 'guard_name'=> 'web'],
            ['name' => 'delete_users', 'guard_name'=> 'web'], ['name' => 'show_sends', 'guard_name'=> 'web'], ['name' => 'edit_sends', 'guard_name'=> 'web'],
            ['name' => 'delete_sends', 'guard_name'=> 'web'], ['name' => 'show_platforms', 'guard_name'=> 'web'], ['name' => 'edit_platforms', 'guard_name'=> 'web'],
            ['name' => 'delete_platforms', 'guard_name'=> 'web'], ['name' => 'show_categories', 'guard_name'=> 'web'], ['name' => 'edit_categories', 'guard_name'=> 'web'],
            ['name' => 'delete_categories', 'guard_name'=> 'web'],  ['name' => 'show_articles', 'guard_name'=> 'web'], ['name' => 'edit_articles', 'guard_name'=> 'web'],
            ['name' => 'delete_articles', 'guard_name'=> 'web'], ['name' => 'show_article_categories', 'guard_name'=> 'web'],
            ['name' => 'edit_article_categories', 'guard_name'=> 'web'], ['name' => 'delete_article_categories', 'guard_name'=> 'web'], ['name' => 'show_cards', 'guard_name'=> 'web'],
            ['name' => 'edit_cards', 'guard_name'=> 'web'], ['name' => 'delete_cards', 'guard_name'=> 'web'], ['name' => 'show_requests', 'guard_name'=> 'web'],
            ['name' => 'edit_requests', 'guard_name'=> 'web'], ['name' => 'delete_requests', 'guard_name'=> 'web'], ['name' => 'show_payments', 'guard_name'=> 'web'],
            ['name' => 'delete_payments', 'guard_name'=> 'web'], ['name' => 'show_securities', 'guard_name'=> 'web'], ['name' => 'edit_securities', 'guard_name'=> 'web'],
            ['name' => 'edit_tasks', 'guard_name'=> 'web'], ['name' => 'delete_tasks', 'guard_name'=> 'web'], ['name' => 'show_tasks', 'guard_name'=> 'web'],
            ['name' => 'show_roles', 'guard_name'=> 'web'], ['name' => 'edit_roles', 'guard_name'=> 'web'], ['name' => 'delete_roles', 'guard_name'=> 'web'],
            ['name' => 'show_settings', 'guard_name'=> 'web'], ['name' => 'show_settings_base', 'guard_name'=> 'web'], ['name' => 'edit_settings_base', 'guard_name'=> 'web'],
            ['name' => 'show_settings_home', 'guard_name'=> 'web'], ['name' => 'edit_settings_home', 'guard_name'=> 'web'],
            ['name' => 'show_settings_aboutUs', 'guard_name'=> 'web'], ['name' => 'edit_settings_aboutUs', 'guard_name'=> 'web'], ['name' => 'show_settings_contactUs', 'guard_name'=> 'web'],
            ['name' => 'edit_settings_contactUs', 'guard_name'=> 'web'], ['name' => 'show_settings_law', 'guard_name'=> 'web'], ['name' => 'edit_settings_law', 'guard_name'=> 'web'],
            ['name' => 'show_settings_chatLaw', 'guard_name'=> 'web'], ['name' => 'edit_settings_chatLaw', 'guard_name'=> 'web'], ['name' => 'show_settings_fag', 'guard_name'=> 'web'],
            ['name' => 'edit_settings_fag', 'guard_name'=> 'web'],['name' => 'show_offends','guard_name'=> 'web'],['name' => 'show_reports','guard_name'=> 'web']
        ];
        $user = [
            'name'=> 'admin',
            'user_name' => 'admin',
            'email' => 'admin@gmail.com',
            'province' => 'Tehran',
            'city' => 'Tehran',
            'phone' => '09336332901',
            'status' => $userRepository::confirmedStatus(),
            'pass_word' => Hash::make('admin'),
            'ip' => 1,
        ];
        try {
            DB::beginTransaction();
            $permissionRepository->insert($permissions);
            $admin = $roleRepository->create(['name' => 'admin']);
            $super_admin = $roleRepository->create(['name' => 'super_admin']);
            $administrator = $roleRepository->create(['name' => 'administrator']);
            $super_admin->syncPermissions($permissionRepository->getAll());
            $administrator->syncPermissions($permissionRepository->getAll());
            $user = $userRepository->create($user);
            $userRepository->syncRoles($user,[$admin,$super_admin,$administrator]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return 0;
    }
}
