<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints as Assert;

final class DiscountBook
{
    #[Assert\Range(min: 0, max: 100)]
    public int $percentage;

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public static function handleLinks(QueryBuilder $queryBuilder, array $uriVariables, QueryNameGeneratorInterface $queryNameGenerator, array $context): void
    {
        $queryBuilder
            ->andWhere($queryBuilder->getRootAliases()[0].'.id = :id')
            ->setParameter('id', $uriVariables['id']);
    }
}
