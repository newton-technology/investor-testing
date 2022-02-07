<?php

declare(strict_types=1);

namespace Common\Base\Entities;

use Throwable;

use Common\Base\Repositories\Database\EntityAttributeRepositoryInterface;

trait EntityAttributesRepositoryTrait
{
    protected EntityAttributeRepositoryInterface $attributeRepository;

    public function __construct(EntityAttributeRepositoryInterface $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @throws Throwable
     */
    private function addAttributesWithApplyResult(EntityAttributes $entity): void
    {
        $this->fillAttributesEntityId($entity);
        $this->attributeRepository->addAttributes($entity->getAttributes());
        $entity->setAttributes(
            $this->attributeRepository->getAttributesByEntityId($entity->getId())
        );
    }

    /**
     * @throws Throwable
     */
    private function updateAttributesWithApplyResult(EntityAttributes $entity): void
    {
        $this->fillAttributesEntityId($entity);
        $this->attributeRepository->addOrUpdateAttributes($entity->getAttributes());
        $attributes = $this->attributeRepository->getAttributesByEntityId($entity->getId());
        $entity->setAttributes($attributes);
    }

    private function fillAttributesEntityId(EntityAttributes $entity): void
    {
        $entity->setAttributes(
            array_map(
                fn($attribute) => $attribute->setEntityId($entity->getId()),
                $entity->getAttributes()
            )
        );
    }
}
