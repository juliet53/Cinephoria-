<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        if ($resourceClass !== \App\Entity\Reservation::class) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];
        $qb->andWhere("$rootAlias.user = :current_user")
           ->setParameter('current_user', $user);
    }

    public function applyToCollection(QueryBuilder $qb, QueryNameGeneratorInterface $generator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($qb, $resourceClass);
    }

    public function applyToItem(QueryBuilder $qb, QueryNameGeneratorInterface $generator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($qb, $resourceClass);
    }
}
