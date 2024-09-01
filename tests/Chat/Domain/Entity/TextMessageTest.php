<?php

namespace Tests\Chat\Domain\Entity;

use App\Chat\Domain\Entity\TextMessage;
use App\Chat\Domain\ValueObject\MessageId;
use App\Shared\Domain\Exception\LogicException;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Tests\TestCase;

final class TextMessageTest extends TestCase
{
    public function testGetId(): void
    {
        $id = MessageId::generate()->value();
        $text = $this->createTestTextMessageEntity(
            id: $id
        );

        $this->assertEquals($id, $text->getId());
    }

    public function testGetIdFails(): void
    {
        $text = TextMessage::build();

        $this->expectException(LogicException::class);
        $text->getId();
    }

    public function testGetUserId(): void
    {
        $id = UserId::generate()->value();
        $text = $this->createTestTextMessageEntity(
            userId: $id
        );

        $this->assertEquals($id, $text->getUserId());
    }

    public function testGetUserIdFails()
    {
        $text = TextMessage::build();

        $this->expectException(LogicException::class);
        $text->getUserId();
    }

    public function testGetViewers()
    {
        $viewers = [];

        for ($i = 0; $i < 10; $i++) {
            $viewers[] = [
                $this->createTestUserEntity()->getId(),
                new DateTimeImmutable(),
            ];
        }

        $text = $this->createTestTextMessageEntity(
            viewers: $viewers
        );

        $viewerIds = array_map(function ($viewer) {
            return $viewer[0];
        }, $viewers);

        $this->assertCount(count($viewers), $text->getViewers());

        $viewersFromEntity = $text->getViewers();

        $remainingIds = array_flip($viewerIds);

        foreach ($viewersFromEntity as $item) {
            $this->assertCount(2, $item);
            $this->assertArrayHasKey('userId', $item);
            $this->assertTrue(is_string($item['userId']));
            $this->assertArrayHasKey('at', $item);
            $this->assertTrue($item['at'] instanceof DateTimeImmutable);

            $userId = $item['userId'];
            $this->assertArrayHasKey($userId, $remainingIds);

            unset($remainingIds[$userId]);
        }

        $this->assertEmpty($remainingIds, 'Some viewer IDs were not found in the entity');
    }

    public function testGetViewersFails()
    {
        $text = TextMessage::build();

        $this->expectException(LogicException::class);
        $text->getViewers();
    }

    public function testIsSent()
    {
        $isSent = false;

        $text = $this->createTestTextMessageEntity(
            isSent: $isSent
        );

        $this->assertFalse($text->isSent());

        $isSent = true;
        $text = $this->createTestTextMessageEntity(
            isSent: $isSent
        );

        $this->assertTrue($text->isSent());
    }

    public function testIsSentFails()
    {
        $text = TextMessage::build();
        $this->expectException(LogicException::class);
        $text->isSent();
    }

    public function testMarkAsViewed()
    {
        $text = $this->createTestTextMessageEntity(
            viewers: []
        );
        $user = $this->createTestUserEntity();

        $now = new DateTimeImmutable();
        $text->markAsViewed($user->getId(), $now);

        $viewers = $text->getViewers();
        $this->assertCount(1, $viewers);
        $this->assertEquals($viewers[0]['userId'], $user->getId());
        $this->assertEquals($viewers[0]['at'], $now);
    }
}
