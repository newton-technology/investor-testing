<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.08.2021
 * Time: 18:42
 */

namespace Newton\InvestorTesting\Packages\Common;

use LogicException;
use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class UserRoleRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'user_roles';
    protected string $entity = UserRole::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    public function userRoleExists(UserRole $userRole): bool
    {
        return $this->userRolesExists([$userRole]);
    }

    /**
     * @param UserRole[] $userRoles
     * @return bool
     */
    public function userRolesExists(array $userRoles): bool
    {
        $userId = null;
        $roles = [];
        foreach ($userRoles as $userRole) {
            if ($userId === null) {
                $userId = $userRole->getUserId();
            }
            if ($userId !== $userRole->getUserId()) {
                throw new LogicException('You must specify a list of roles for one user');
            }
            $roles[] = $userRole->getRole();
        }

        $existingRoles = $this->getCollection(
            [
                ['user_id', $userId],
                ['role', 'in', array_unique($roles)],
            ]
        );

        return count($existingRoles) === count($roles);
    }

    public function getUserRole(int $userId, string $role): ?UserRole
    {
        return $this->getEntityByKey(
            [
                ['user_id', $userId],
                ['role', $role]
            ]
        );
    }

    /**
     * @param UserRole $userRole
     * @throws Throwable
     */
    public function addUserRole(UserRole $userRole)
    {
        if (!in_array($userRole->getRole(), UserRole::getAvailableRoles())) {
            throw new LogicException("unsupported role ({$userRole->getRole()})");
        }

        $this->addEntityWithApplyResult($userRole);
    }

    public function removeUserRole(UserRole $userRole)
    {
        $this->deleteEntity($userRole->getId());
    }
}
