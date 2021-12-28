<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateIdeaTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfCreateIdeaWithoutAuthRedirectToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/idees/creer');

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function testIfCreateIdeaWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $client->request('GET', '/idees/creer');

        static::assertDbQueries($client);

        $client->submitForm('Créer', [
            'idea[title]' => 'user+11@email.com',
            'idea[content]' => 'password',
            'idea[tags]' => [1, 2, 3],
        ]);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('idea_list');
    }

    /**
     * @param array{email: string, password: string, nickname: string} $formData
     *
     * @dataProvider provideInvalidData
     */
    public function testIfCreateIdeaFailsDueToInvalidData(array $formData): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $client->request('GET', '/idees/creer');

        static::assertDbQueries($client);

        $client->submitForm('Créer', $formData);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(422);
    }

    public function provideInvalidData(): iterable
    {
        $baseData = static fn (array $data) => $data + [
                'idea[title]' => 'user+11@email.com',
                'idea[content]' => 'password',
                'idea[tags]' => [1, 2, 3],
            ];

        yield 'title is empty' => [$baseData(['idea[title]' => ''])];
        yield 'content is empty' => [$baseData(['idea[content]' => ''])];
    }
}
