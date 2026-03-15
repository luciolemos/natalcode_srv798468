<?php

declare(strict_types=1);

use App\Domain\Agenda\AgendaRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Agenda\FallbackAgendaRepository;
use App\Infrastructure\Persistence\Agenda\MySqlAgendaRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        AgendaRepository::class => function (ContainerInterface $c): AgendaRepository {
            try {
                return new MySqlAgendaRepository($c->get(\PDO::class));
            } catch (\Throwable $exception) {
                return new FallbackAgendaRepository();
            }
        },
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};
