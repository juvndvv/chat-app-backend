<?php

namespace Tests;

use App\Chat\Domain\Entity\ChatRoom;
use App\Chat\Domain\ValueObject\ChatRoomId;
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
        ?bool $canExecCommands = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ): User {
        $now = new DateTimeImmutable();

        return User::build()
            ->setId($id ?? UserId::generate()->value())
            ->setName($name ?? 'Jose ' . uniqid())
            ->setFirstLastName($firstLastName ?? 'Revuelta ' . uniqid())
            ->setSecondLastName($secondLastNameNull ? null : $secondLastName ?? 'Manu' . uniqid())
            ->setEmail($email ?? uniqid() . '@test.com')
            ->setCanExecCommands($canExecCommands ?? false)
            ->setCreatedAt($createdAt ?? $now)
            ->setUpdatedAt($updatedAt ?? $now);
    }

    public function createTestChatRoomEntity(
        ?string $id = null,
        ?string $name = null,
        ?string $description = null,
        ?array $members = null,
        ?array $messages = null,
        ?string $creatorId = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
        ?DateTimeImmutable $deletedAt = null
    )
    {
        $now = new DateTimeImmutable();

        if ($members === null) {
            $members = [];

            for ($i = 0; $i < 10; $i++) {
                $members[] = $this->createTestUserEntity()->getId();
            }
        }

        $chatRoom = ChatRoom::build()
            ->setId($id ?? ChatRoomId::generate()->value())
            ->setName($name ?? 'Chat de prueba')
            ->setDescription($description ?? 'Descripcion de prueba')
            ->setMembers($members)
            ->setMessages($messages ?? [])
            ->setCreatorId($creatorId ?? UserId::generate()->value())
            ->setCreatedAt($createdAt ?? $now)
            ->setUpdatedAt($updatedAt ?? $now);

        if ($deletedAt !== null) {
            $chatRoom->setDeletedAt($deletedAt);
        }

        return $chatRoom;
    }
}
