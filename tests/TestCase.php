<?php

namespace Tests;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createTestUserEntity(
        ?string $id = null,
        ?string $name = null,
        ?string $firstLastName = null,
        ?string $secondLastName = null,
        bool   $secondLastNameNull = false,
        ?string $email = null,
        ?bool $canExecCommands = null
    ): User {
        $now = new DateTimeImmutable();
        return User::build()
            ->setId($id ?? UserId::generate()->value())
            ->setName($name ?? 'Nombre')
            ->setFirstLastName($firstLastName ?? 'Apellido')
            ->setSecondLastName($secondLastNameNull ? null : $secondLastName ?? 'Segundo Apellido')
            ->setEmail($email ?? 'test@test.com')
            ->setCanExecCommands($canExecCommands ?? false)
            ->setCreatedAt($now)
            ->setUpdatedAt($now);
    }}
