<?php /** @noinspection PhpMissingFieldTypeInspection */

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 18:42
 */

namespace Newton\InvestorTesting\Console\Commands;

use Newton\InvestorTesting\Packages\Common\UserRepository;

use Illuminate\Console\Command;

class UserPasswordRemoveCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:password:remove' .
    '{username       : Username (email)}';

    /**
     * @var string
     */
    protected $description = 'Remove user password';

    public function handle(UserRepository $userRepository)
    {
        $username = $this->argument('username');

        $user = $userRepository->getUserByEmail($username);
        if (empty($user)) {
            $this->error("Пользователя с логином {$username} не существует.");
            return;
        }

        $userRepository->updateUser($user->setPassword(null));
    }
}
