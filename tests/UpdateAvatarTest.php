<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class UpdateAvatarTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;

    public function testIfUpdateIdeaWithoutAuthRedirectToLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/avatar');

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function testIfUpdateAvatarWorks(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $client->request('GET', '/avatar');

        static::assertDbQueries($client);

        $client->submitForm('Modifier', [
            'user[avatarFile]' => $this->createImage(),
        ]);

        static::assertDbQueries($client);

        $this->assertResponseStatusCodeSame(302);
    }

    private function createImage(): UploadedFile
    {
        $filename = sprintf('%s.png', (string) Uuid::v4());
        $filePath = sprintf('%s/../public/uploads/%s', __DIR__, $filename);
        copy(sprintf('%s/../public/uploads/image.png', __DIR__), $filePath);

        return new UploadedFile($filePath, $filename, null, null, true);
    }
}
