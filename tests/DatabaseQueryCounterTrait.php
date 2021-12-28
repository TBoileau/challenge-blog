<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait DatabaseQueryCounterTrait
{
    public static function assertDbQueries(KernelBrowser $client, int $expect = 3): void
    {
        Assert::assertLessThanOrEqual(
            $expect,
            $client->getProfile()->getCollector('db')->getQueryCount()
        );
    }
}
