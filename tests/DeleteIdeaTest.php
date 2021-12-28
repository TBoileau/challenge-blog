<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DeleteIdeaTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfDeleteIdeaWithoutAuthRedirectToLogin(): void
    {
        $client = static::createClient();

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy([]);

        $client->request('GET', sprintf('/idees/%s/supprimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function testIfDeleteIdeaOfDifferentUserThrowAccessDenied(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(1);

        $client->loginUser($user);

        $otherUser = $userRepository->findOneBy(2);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $otherUser]);

        $client->request('GET', sprintf('/idees/%s/supprimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testIfDeleteIdeaWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/supprimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('idea_list');
    }
}
