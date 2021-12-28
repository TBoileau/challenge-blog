<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LikeIdeaTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfLikeIdeaWithoutAuthRedirectToLogin(): void
    {
        $client = static::createClient();

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy([]);

        $client->request('GET', sprintf('/idees/%s/aimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function testIfLikeIdeaWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/aimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('idea_list');

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->find($idea->getId());

        $this->assertCount(1, $idea->getLikes());

        $client->request('GET', sprintf('/idees/%s/aimer', $idea->getSlug()));

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('idea_list');

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->find($idea->getId());

        $this->assertCount(0, $idea->getLikes());
    }
}
