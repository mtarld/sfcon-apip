<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\Book;
use App\ApiResource\DiscountBook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @implements ProcessorInterface<DiscountBook, Book>
 */
final readonly class DiscountBookProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<BookEntity, BookEntity> $persistProcessor
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
        if (!$entity = $context['request']->attributes->get('read_data')) {
            throw new NotFoundHttpException('Not Found');
        }

        $entity->price = (int) ($entity->price * (1 - $data->percentage / 100));
        $entity = $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->objectMapper->map($entity, $operation->getClass());
    }
}
