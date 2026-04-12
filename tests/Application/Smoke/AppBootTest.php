<?php

declare(strict_types=1);

namespace Tests\Application\Smoke;

use Slim\App;
use Tests\TestCase;

final class AppBootTest extends TestCase
{
    public function testAppBoots(): void
    {
        $app = $this->getAppInstance();

        $this->assertInstanceOf(App::class, $app);
    }
}
