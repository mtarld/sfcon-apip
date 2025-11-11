<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\ApiResource\Book;
use App\Entity\Book as BookEntity;
use Doctrine\ORM\EntityManagerInterface;

class BookTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testGetBook(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = new BookEntity();
        $book->title = 'TITLE';
        $book->description = 'DESCRIPTION';
        $book->isbn = '9780061964367';

        $em->persist($book);
        $em->flush();

        static::createClient()->request('GET', '/api/books/'.$book->id);

        static::assertResponseIsSuccessful();
        static::assertMatchesResourceItemJsonSchema(Book::class);
        static::assertJsonContains([
            '@id' => '/api/books/1',
            'id' => 1,
            'name' => 'TITLE',
            'description' => 'DESCRIPTION',
            'isbn' => '9780061964367',
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

        $book2 = new BookEntity();
        $book2->title = 'TITLE 2';
        $book2->description = 'DESCRIPTION 2';
        $book2->isbn = '9780061964368';

        $em->persist($book1);
        $em->persist($book2);
        $em->flush();

        $r = static::createClient()->request('GET', '/api/books');

        static::assertResponseIsSuccessful();

        // TODO this does not work: ApiPlatform\Metadata\Exception\OperationNotFoundException: Operation "" not found for resource "Book".
        // static::assertMatchesResourceCollectionJsonSchema(Book::class);

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
        ]);
    }
}
