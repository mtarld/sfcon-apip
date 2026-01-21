<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Dto\BookCollection;
use App\Entity\Book as BookEntity;
use App\ApiResource\Book;
use Doctrine\ORM\EntityManagerInterface;

final class BookTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testGetMissingBook(): void
    {
        static::createClient()->request('GET', '/api/books/1');
        static::assertResponseStatusCodeSame(404);
    }

    public function testGetBook(): void
    {
        $book = $this->createBook();

        static::createClient()->request('GET', '/api/books/'.$book->id);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/'.$book->id,
            'id' => $book->id,
            'name' => $book->title,
            'description' => $book->description,
            'isbn' => $book->isbn,
            'price' => Book::formatPrice($book->price, $book),
        ]);
    }

    public function testGetBooks(): void
    {
        $bookA = $this->createBook();
        $bookB = $this->createBook(
            title: 'TITLE 2',
            description: 'DESCRIPTION 2',
            isbn: '9781794890268',
            price: 200,
        );

        static::createClient()->request('GET', '/api/books');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookCollection::class);
        static::assertJsonContains([
            'totalItems' => 2,
            'member' => [
                ['@id' => '/api/books/'.$bookA->id],
                ['@id' => '/api/books/'.$bookB->id],
            ],
        ]);

        static::createClient()->request('GET', '/api/books?name=2');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookCollection::class);
        static::assertJsonContains([
            'totalItems' => 1,
            'member' => [
                ['@id' => '/api/books/'.$bookB->id],
            ],
        ]);

        static::createClient()->request('GET', '/api/books?isbn=9781794890268');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(BookCollection::class);
        static::assertJsonContains([
            'totalItems' => 1,
            'member' => [
                ['@id' => '/api/books/'.$bookB->id],
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
        $book = $this->createBook();

        static::createClient()->request('PATCH', '/api/books/'.$book->id, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'TITLE 2',
                'description' => 'DESCRIPTION 2',
            ],
        ]);

        static::assertResponseStatusCodeSame(200);
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/'.$book->id,
            'id' => 1,
            'name' => 'TITLE 2',
            'description' => 'DESCRIPTION 2',
            'isbn' => $book->isbn, // not updated
            'price' => Book::formatPrice($book->price, $book), // not updated
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
        $book = $this->createBook();

        static::createClient()->request('POST', '/api/books/'.$book->id.'/discount', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'percentage' => 20,
            ],
        ]);
        static::assertResponseStatusCodeSame(200);
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            'price' => Book::formatPrice(80, $book),
        ]);
    }

    private function createBook(
        ?string $title = null,
        ?string $description = null,
        ?string $isbn = null,
        ?int $price = null,
    ): BookEntity {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = new BookEntity();
        $book->title = $title ?? 'TITLE';
        $book->description = $description ?? 'DESCRIPTION';
        $book->isbn = $isbn ?? '9783058944793';
        $book->price = $price ?? 100;

        $em->persist($book);
        $em->flush();

        return $book;
    }
}
