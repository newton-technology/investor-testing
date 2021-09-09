<?php /** @noinspection PhpMissingFieldTypeInspection */

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 18:31
 */

namespace Newton\InvestorTesting\Console\Commands;

use Throwable;

use Newton\InvestorTesting\Packages\Common\UserRepository;
use Newton\InvestorTesting\Packages\Common\UserRole;
use Newton\InvestorTesting\Packages\Common\UserRoleRepository;

use Illuminate\Console\Command;

class UserRoleAddCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:role:add' .
    '{username       : Username (email)}' .
    '{role           : Role}';

    /**
     * @var string
     */
    protected $description = 'Add role to user';

    /**
     * @throws Throwable
     */
    public function handle(UserRepository $userRepository, UserRoleRepository $userRoleRepository)
    {
        $username = $this->argument('username');
        $user = $userRepository->getUserByEmail($username);
        if (empty($user)) {
            $this->error("Пользователя с логином {$username} не существует.");
            return;
        }

        $role = (new UserRole())
            ->setUserId($user->getId())
            ->setRole($this->argument('role'));

        if ($userRoleRepository->userRoleExists($role)) {
            $this->warn("Роль \"{$role->getRole()}\" уже назначена пользователю \"{$user->getEmail()}\"");
            return;
        }

        $userRoleRepository->addUserRole($role);
    }
}
