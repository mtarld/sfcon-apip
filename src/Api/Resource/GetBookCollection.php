<?php

declare(strict_types=1);

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\Isbn;

#[GetCollection(
    uriTemplate: '/books',
    itemUriTemplate: '/books/{id}',
    stateOptions: new Options(entityClass: BookEntity::class),
    parameters: [
        'name' => new QueryParameter(
            property: 'title',
            filter: new PartialSearchFilter(),
        ),
        'isbn' => new QueryParameter(
            filter: new ExactFilter(),
            constraints: [new Isbn()],
        ),
    ],
)]
#[Map(source: BookEntity::class)]
final class GetBookCollection
{
    public function __construct(
        public int $id,
        #[Map(source: 'title')]
        public string $name,
        public string $isbn,
    ) {
    }
}
