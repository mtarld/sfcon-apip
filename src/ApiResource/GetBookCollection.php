<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[GetCollection(
    shortName: 'Book',
    uriTemplate: '/books',
    itemUriTemplate: '/books/{id}',
    stateOptions: new Options(entityClass: BookEntity::class),
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
