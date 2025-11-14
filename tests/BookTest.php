<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\ApiResource\Book;
use App\ApiResource\GetBookCollection;
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
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = new BookEntity();
        $book->title = 'TITLE';
        $book->description = 'DESCRIPTION';
        $book->isbn = '9780061964367';
        $book->price = 100;

        $em->persist($book);
        $em->flush();

        static::createClient()->request('GET', '/api/books/1');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/1',
            'id' => 1,
            'name' => 'TITLE',
            'description' => 'DESCRIPTION',
            'isbn' => '9780061964367',
            'price' => '1.00$',
        ]);
    }

    public function testGetBooks(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book1 = new BookEntity();
        $book1->title = 'TITLE';
        $book1->description = 'DESCRIPTION';
        $book1->isbn = '9780061964367';
        $book1->price = 100;

        $book2 = new BookEntity();
        $book2->title = 'TITLE 2';
        $book2->description = 'DESCRIPTION 2';
        $book2->isbn = '9780061964368';
        $book2->price = 200;

        $em->persist($book1);
        $em->persist($book2);
        $em->flush();

        static::createClient()->request('GET', '/api/books');

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceCollectionJsonSchema(GetBookCollection::class);

        static::assertJsonContains([
            'totalItems' => 2,
            'member' => [
                [
                    '@id' => '/api/books/1',
                    'id' => 1,
                    'name' => 'TITLE',
                    'isbn' => '9780061964367',
                ],
                [
                    '@id' => '/api/books/2',
                    'id' => 2,
                    'name' => 'TITLE 2',
                    'isbn' => '9780061964368',
                ],
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
                'isbn' => '9780061964367',
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
            'isbn' => '9780061964367',
            'price' => '1.00$',
        ]);
    }

    public function testDiscountMissingBook(): void
    {
        static::createClient()->request('POST', '/api/books/99999/discount', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'percentage' => 50,
            ],
        ]);
        static::assertResponseStatusCodeSame(404);
    }

    public function testDiscountBook(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = new BookEntity();
        $book->title = 'TITLE';
        $book->description = 'DESCRIPTION';
        $book->isbn = '9780061964367';
        $book->price = 100;

        $em->persist($book);
        $em->flush();

        static::createClient()->request('POST', '/api/books/1/discount', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'percentage' => 50,
            ],
        ]);
        static::assertResponseStatusCodeSame(200);

        // TODO test response
    }
}
