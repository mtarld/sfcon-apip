<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Api\Resource\Book;
use App\Api\Resource\GetBookCollection;
use App\Entity\Book as BookEntity;
use Doctrine\ORM\EntityManagerInterface;

class BookTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testGetMissingBook(): void
    {
        static::createClient()->request('GET', '/api/books/1');
        static::assertResponseStatusCodeSame(404);
    }

    public function testGetBook(): void
    {
        $this->createBook();

        static::createClient()->request('GET', '/api/books/1');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/1',
            'id' => 1,
            'name' => 'TITLE',
            'description' => 'DESCRIPTION',
            'isbn' => '9783058944793',
            'price' => '1.00$',
        ]);
    }

    public function testGetBooks(): void
    {
        $this->createBook();
        $this->createBook(
            title: 'TITLE 2',
            description: 'DESCRIPTION 2',
            isbn: '9781794890268',
            price: 200,
        );

        static::createClient()->request('GET', '/api/books');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(GetBookCollection::class);
        static::assertJsonContains([
            'totalItems' => 2,
            'member' => [
                ['@id' => '/api/books/1'],
                ['@id' => '/api/books/2'],
            ],
        ]);

        static::createClient()->request('GET', '/api/books?name=2');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(GetBookCollection::class);
        static::assertJsonContains([
            'totalItems' => 1,
            'member' => [
                ['@id' => '/api/books/2'],
            ],
        ]);

        static::createClient()->request('GET', '/api/books?isbn=9781794890268');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(GetBookCollection::class);
        static::assertJsonContains([
            'totalItems' => 1,
            'member' => [
                ['@id' => '/api/books/2'],
            ],
        ]);
    }

    public function testCreateBook(): void
    {
        static::createClient()->request('POST', '/api/books', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'TITLE',
                'description' => 'DESCRIPTION',
                'isbn' => '9783058944793',
                'price' => 100,
            ],
        ]);

        static::assertResponseStatusCodeSame(201);
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/1',
            'id' => 1,
            'name' => 'TITLE',
            'description' => 'DESCRIPTION',
            'isbn' => '9783058944793',
            'price' => '1.00$',
        ]);
    }

    public function testUpdateBook(): void
    {
        $this->createBook();

        static::createClient()->request('PATCH', '/api/books/1', [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'TITLE 2',
                'description' => 'DESCRIPTION 2',
                'isbn' => '9781794890268',
                'price' => 200,
            ],
        ]);

        static::assertResponseStatusCodeSame(200);
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/1',
            'id' => 1,
            'name' => 'TITLE 2',
            'description' => 'DESCRIPTION 2',
            'isbn' => '9783058944793', // not updated
            'price' => '1.00$', // not updated
        ]);
    }

    public function testDiscountMissingBook(): void
    {
        static::createClient()->request('POST', '/api/books/1/discount', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'percentage' => 50,
            ],
        ]);
        static::assertResponseStatusCodeSame(404);
    }

    public function testDiscountBook(): void
    {
        $this->createBook();

        static::createClient()->request('POST', '/api/books/1/discount', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'percentage' => 50,
            ],
        ]);
        static::assertResponseStatusCodeSame(200);
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            'price' => '0.50$',
        ]);
    }

    private function createBook(
        ?string $title = null,
        ?string $description = null,
        ?string $isbn = null,
        ?int $price = null,
    ): void {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = new BookEntity();
        $book->title = $title ?? 'TITLE';
        $book->description = $description ?? 'DESCRIPTION';
        $book->isbn = $isbn ?? '9783058944793';
        $book->price = $price ?? 100;

        $em->persist($book);
        $em->flush();
    }
}
