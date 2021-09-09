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
use Newton\InvestorTesting\Packages\Common\UserRoleRepository;

use Illuminate\Console\Command;

class UserRoleRemoveCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:role:remove' .
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
        $user = $userRepository->getUserByEmail($this->argument('username'));
        if (empty($user)) {
            $this->error("Пользователя с логином {$username} не существует.");
            return;
        }

        $roleName = $this->argument('role');
        $userRole = $userRoleRepository->getUserRole($user->getId(), $roleName);
        if ($userRole === null) {
            $this->warn("У пользователя {$user->getEmail()} нет роли \"{$roleName}\".");
            return;
        }

        $userRoleRepository->removeUserRole($userRole);
    }
}
