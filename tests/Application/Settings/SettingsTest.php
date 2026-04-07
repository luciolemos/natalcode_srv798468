<?php

declare(strict_types=1);

namespace Tests\Application\Settings;

use App\Application\Settings\Settings;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    public function testGetReturnsAllSettingsWhenKeyIsEmpty(): void
    {
        $settings = new Settings([
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true,
        ]);

        $this->assertSame(
            [
                'displayErrorDetails' => true,
                'logErrors' => true,
                'logErrorDetails' => true,
            ],
            $settings->get()
        );
    }

    public function testGetReturnsSpecificValueWhenKeyIsProvided(): void
    {
        $settings = new Settings([
            'db' => [
                'host' => 'localhost',
            ],
        ]);

        $this->assertSame(['host' => 'localhost'], $settings->get('db'));
    }
}
