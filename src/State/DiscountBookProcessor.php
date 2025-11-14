<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book as BookEntity;
use App\ApiResource\DiscountBook;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<DiscountBook, BookEntity>
 */
final readonly class DiscountBookProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BookEntity
    {
        $entity = $context['request']->attributes->get('entity_data');
        if (!$entity) {
            throw new NotFoundHttpException('Not Found');
        }

        $entity->price = (int) ($entity->price * (1 - $data->percentage / 100));

        return $entity;
    }
}
