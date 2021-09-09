<?php /** @noinspection PhpMissingFieldTypeInspection */

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 18:42
 */

namespace Newton\InvestorTesting\Console\Commands;

use Throwable;

use Newton\InvestorTesting\Packages\Common\User;
use Newton\InvestorTesting\Packages\Common\UserRepository;

use Illuminate\Console\Command;
use Illuminate\Hashing\HashManager;

class UserPasswordChangeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:password:add' .
    '{username       : Username (email)}';

    /**
     * @var string
     */
    protected $description = 'Add password to user';

    /**
     * @throws Throwable
     */
    public function handle(HashManager $hashManager, UserRepository $userRepository)
    {
        $username = $this->argument('username');

        $user = $userRepository->getUserByEmail($username);
        if (empty($user)) {
            if (!$this->confirm("Создать пользователя с логином {$username}?")) {
                return;
            }

            $user = (new User())->setEmail($username);
            $userRepository->addUser($user);
        }

        $password = $this->secret("Пароль для пользователя {$user->getEmail()}:");
        $userRepository->updateUser($user->setPassword($hashManager->make($password)));
    }
}
