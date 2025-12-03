<?php

declare(strict_types=1);

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Dto\BookCollection;
use App\Api\Dto\CreateBook;
use App\Api\Dto\DiscountBook;
use App\Api\Dto\UpdateBook;
use App\Entity\Book as BookEntity;
use App\State\DiscountBookProcessor;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\Isbn;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/books/{id}',
            uriVariables: ['id'],
        ),
        new GetCollection(
            uriTemplate: '/books',
            output: BookCollection::class,
            parameters: [
                'name' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    property: 'title',
                ),
                'isbn' => new QueryParameter(
                    filter: new ExactFilter(),
                    constraints: [new Isbn()],
                ),
            ],
        ),
        new Patch(
            uriTemplate: '/books/{id}',
            uriVariables: ['id'],
            input: UpdateBook::class,
        ),
        new Post(
            uriTemplate: '/books',
            input: CreateBook::class,
        ),
        new Post(
            uriTemplate: '/books/{id}/discount',
            uriVariables: ['id'],
            status: 200,
            input: DiscountBook::class,
            processor: DiscountBookProcessor::class,
        ),
    ],
    stateOptions: new Options(entityClass: BookEntity::class),
    jsonStream: true,
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

    public Author $author;

    public static function formatPrice(mixed $price, object $source): int|string
    {
        if ($source instanceof BookEntity) {
            return number_format($price / 100, 2).'$';
        }

        if ($source instanceof self) {
            return 100 * (int) str_replace('$', '', $price);
        }

        throw new \LogicException(\sprintf('Unexpected "%s" source.', $source::class));
    }
}
