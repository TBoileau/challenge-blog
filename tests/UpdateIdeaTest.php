<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UpdateIdeaTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfUpdateIdeaWithoutAuthRedirectToLogin(): void
    {
        $client = static::createClient();

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy([]);

        $client->request('GET', sprintf('/idees/%s/modifier', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function testIfUpdateIdeaOfDifferentUserThrowAccessDenied(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(1);

        $client->loginUser($user);

        $otherUser = $userRepository->findOneBy(2);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $otherUser]);

        $client->request('GET', sprintf('/idees/%s/modifier', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testIfUpdateIdeaWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/modifier', $idea->getSlug()));

        static::assertDbQueries($client);

        $client->submitForm('Modifier', [
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
    public function testIfUpdateIdeaFailsDueToInvalidData(array $formData): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/modifier', $idea->getSlug()));

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
