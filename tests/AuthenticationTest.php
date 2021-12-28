<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthenticationTest extends WebTestCase
{
    use DatabaseQueryCounterTrait;
    
    public function testIfAuthenticationWorks(): void
    {
        $client = static::createClient();

        $client->request('GET', '/connexion');

        static::assertDbQueries($client);

        $client->submitForm('Se connecter', [
            'email' => 'user+1@email.com',
            'password' => 'password',
        ]);

        $this->assertResponseStatusCodeSame(302);

        static::assertDbQueries($client);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('home');
    }

    /**
     * @param array{email: string, password: string} $formData
     *
     * @dataProvider provideInvalidCredentials
     */
    public function testIfAuthenticationFailsDueToInvalidCredentials(array $formData): void
    {
        $client = static::createClient();

        $client->request('GET', '/connexion');

        static::assertDbQueries($client);

        $client->submitForm('Se connecter', $formData);

        $this->assertResponseStatusCodeSame(302);

        static::assertDbQueries($client);

        $client->followRedirect();

        static::assertDbQueries($client);

        $this->assertRouteSame('security_login');
    }

    public function provideInvalidCredentials(): iterable
    {
        $baseData = static fn (array $data): array => $data + [
                'email' => 'user+1@email.com',
                'password' => 'password'
            ];

        yield 'email is empty' => [$baseData(['email' => ''])];
        yield 'password is empty' => [$baseData(['password' => ''])];
        yield 'non existent email' => [$baseData(['email' => 'fail@email.com'])];
        yield 'invalid password' => [$baseData(['password' => 'fail'])];
        yield 'csrf invalid' => [$baseData(['_csrf_token' => 'fail'])];
    }
}
