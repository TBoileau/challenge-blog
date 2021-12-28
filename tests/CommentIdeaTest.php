<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CommentIdeaTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;
    
    public function testIfCommentIdeaWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/commenter', $idea->getSlug()));

        static::assertDbQueries($client);

        $client->submitForm('Commenter', [
            'comment[content]' => 'comment',
        ]);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('idea_comment');
    }

    /**
     * @param array{email: string, password: string, nickname: string} $formData
     *
     * @dataProvider provideInvalidData
     */
    public function testIfCommentIdeaFailsDueToInvalidData(array $formData): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $ideaRepository = $client->getContainer()->get(IdeaRepository::class);

        $idea = $ideaRepository->findOneBy(['user' => $user]);

        $client->request('GET', sprintf('/idees/%s/commenter', $idea->getSlug()));

        static::assertDbQueries($client);

        $client->submitForm('Commenter', $formData);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(422);
    }

    public function provideInvalidData(): iterable
    {
        $baseData = static fn (array $data) => $data + [
                'comment[content]' => 'comment',
            ];

        yield 'content is empty' => [$baseData(['comment[content]' => ''])];
    }
}
