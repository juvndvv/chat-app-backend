<?php

namespace Tests;

use App\Chat\Domain\Entity\ChatRoom;
use App\Chat\Domain\Entity\TextMessage;
use App\Chat\Domain\ValueObject\ChatRoomId;
use App\Chat\Domain\ValueObject\MessageId;
use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createTestUserEntity(
        ?string            $id = null,
        ?string            $name = null,
        ?string            $firstLastName = null,
        ?string            $secondLastName = null,
        bool               $secondLastNameNull = false,
        ?string            $email = null,
        ?bool              $canExecCommands = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ): User
    {
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
        ?string            $id = null,
        ?string            $name = null,
        ?string            $description = null,
        ?array             $members = null,
        ?array             $messages = null,
        ?string            $creatorId = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
        ?DateTimeImmutable $deletedAt = null
    ): ChatRoom
    {
        $now = new DateTimeImmutable();

        // Add users if they are not set
        if ($members === null) {
            $members = [];

            for ($i = 0; $i < 10; $i++) {
                $members[] = $this->createTestUserEntity()->getId();
            }
        }

        // Add messages if they are not set
        if ($messages === null) {
            $messages = [];

            for ($i = 0; $i < 10; $i++) {
                $messages[] = $this->createTestTextMessageEntity();
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

    public function createTestTextMessageEntity(
        ?string $id = null,
        ?string $userId = null,
        ?array  $viewers = null,
        ?bool   $isSent = null,
        ?string $content = null,
    ): TextMessage
    {
        $now = new DateTimeImmutable();

        // Add viewers if they are not set
        if ($viewers === null) {
            for ($i = 0; $i < 10; $i++) {
                $viewers[] = [
                    $this->createTestUserEntity()->getId(),
                    new DateTimeImmutable(),
                ];
            }
        }

        return TextMessage::build()
            ->setId($id ?? MessageId::generate()->value())
            ->setUserId($userId ?? UserId::generate()->value())
            ->setViewers($viewers)
            ->setIsSent($isSent ?? true)
            ->setContent($content ?? 'Message content ' . uniqid())
            ->setCreatedAt($createdAt ?? $now)
            ->setUpdatedAt($updatedAt ?? $now);
    }
}
