<?php

declare(strict_types=1);

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Dto\CreateBook;
use App\Api\Dto\UpdateBook;
use App\Api\Dto\DiscountBook;
use App\Entity\Book as BookEntity;
use App\State\DiscountBookProcessor;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    stateOptions: new Options(entityClass: BookEntity::class),
    operations: [
        new Get(uriTemplate: '/books/{id}', uriVariables: ['id']),
        new Post(uriTemplate: '/books', input: CreateBook::class),
        new Post(
            uriTemplate: '/books/{id}/discount',
            uriVariables: ['id'],
            input: DiscountBook::class,
            processor: DiscountBookProcessor::class,
            status: 200,
        ),
        new Patch(
            uriTemplate: '/books/{id}',
            uriVariables: ['id'],
            input: UpdateBook::class,
        ),
    ],
)]
#[Map(source: BookEntity::class)]
final class Book
{
    public int $id;

    #[Map(source: 'title')]
    public string $name;

    public string $description;

    public string $isbn;

    #[Map(transform: [self::class, 'formatPrice'])]
    public string $price;

    public static function formatPrice(mixed $price, object $source, ?object $target): int|string
    {
        if ($target instanceof self) {
            return number_format($price / 100, 2).'$';
        }

        if ($target instanceof BookEntity) {
            return 100 * (int) str_replace('$', '', $price);
        }

        throw new \LogicException(\sprintf('Unexpected "%s" source.', $source::class));
    }
}
