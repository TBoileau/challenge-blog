<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListIdeasTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfListingIdeasWorks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/idees');

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testIfPaginationWorks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/idees?page=2');

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testIfSortingWorks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/idees?field=title&order=asc');

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testIfFilteringWorks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/idees');

        $client->submitForm('Filtrer', [
            'filter[keyword]' => 'test',
            'filter[user]' => 1,
            'filter[tags]' => [1, 2, 3]
        ]);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(200);
    }
}