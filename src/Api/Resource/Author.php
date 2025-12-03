<?php

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Dto\AuthorCollection;
use App\Api\Dto\CreateAuthor;
use App\Api\Dto\UpdateAuthor;
use App\Entity\Author as AuthorEntity;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/authors/{id}',
            uriVariables: ['id'],
        ),
        new GetCollection(
            uriTemplate: '/authors',
            output: AuthorCollection::class,
            parameters: [
                'fullname' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    property: 'fullname',
                ),
            ],
        ),
        new Patch(
            uriTemplate: '/authors/{id}',
            uriVariables: ['id'],
            input: UpdateAuthor::class,
        ),
        new Post(
            uriTemplate: '/authors',
            input: CreateAuthor::class,
        ),
    ],
    stateOptions: new Options(entityClass: AuthorEntity::class),
    jsonStream: true,
)]
class Author
{
    public int $id;
    public string $fullname;
}
