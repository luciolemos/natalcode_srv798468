<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Actions\ActionError;
use Tests\TestCase;

class ActionErrorTest extends TestCase
{
    public function testJsonSerializeReflectsCurrentValues(): void
    {
        $error = new ActionError(ActionError::VALIDATION_ERROR, 'Campo obrigatorio');
        $error->setType(ActionError::BAD_REQUEST)->setDescription('Payload invalido');

        $this->assertSame(ActionError::BAD_REQUEST, $error->getType());
        $this->assertSame('Payload invalido', $error->getDescription());
        $this->assertSame(
            [
                'type' => ActionError::BAD_REQUEST,
                'description' => 'Payload invalido',
            ],
            $error->jsonSerialize()
        );
    }
}
