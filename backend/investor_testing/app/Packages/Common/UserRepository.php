<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 18:50
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class UserRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'users';
    protected string $entity = User::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @throws Throwable
     */
    public function addUser(User $user)
    {
        $this->addEntityWithApplyResult($user);
    }

    public function updateUser(User $user)
    {
        $this->updateEntityWithApplyResult($user);
    }

    public function getUserById(int $id): ?User
    {
        return $this->getEntityById($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->getEntityByKey(
            [
                ['email', $email],
            ]
        );
    }
}
