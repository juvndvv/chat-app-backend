<?php

namespace Tests\Chat\Domain\Entity;

use App\Chat\Domain\UserSawChatRoomEvent;
use App\Shared\Domain\Event\Event;
use App\Shared\Domain\Exception\UserAlreadyInChatRoomException;
use App\Shared\Domain\Exception\UserDoesNotPertainsToChatRoomException;
use DateTimeImmutable;
use Tests\TestCase;

final class ChatRoomTest extends TestCase
{
    public function testGetMembers()
    {
        $creatorId = $this->createTestUserEntity()->getId();

        $members = [];

        for ($i = 0; $i < 10; $i++) {
            $members[] = $this->createTestUserEntity()->getId();
        }

        $chatRoom = $this->createTestChatRoomEntity(
            members: $members,
            creatorId: $creatorId,
        );

        $members[] = $creatorId;

        // Have the same length
        $this->assertCount(count($members), $chatRoom->getMembers());

        // There is no difference between arrays
        $diff = array_diff($members, $chatRoom->getMembers());
        $this->assertCount(0, $diff);
    }

    public function testAddMemberSuccess()
    {
        $user = $this->createTestUserEntity();

        $chatRoom = $this->createTestChatRoomEntity();
        $memberCount = $chatRoom->getMembersCount();

        $chatRoom->addMember($user->getId());

        $this->assertTrue($chatRoom->hasMember($user->getId()));
        $this->assertEquals($memberCount + 1, $chatRoom->getMembersCount());
    }

    public function testAddMemberFails()
    {
        $user = $this->createTestUserEntity();

        $this->expectException(UserAlreadyInChatRoomException::class);

        $chatRoom = $this->createTestChatRoomEntity();

        $chatRoom->addMember($user->getId());
        $chatRoom->addMember($user->getId());
    }

    public function testAddMemberFailsAddingOwner()
    {
        $owner = $this->createTestUserEntity();

        $this->expectException(UserAlreadyInChatRoomException::class);

        $chatRoom = $this->createTestChatRoomEntity(
            creatorId: $owner->getId()
        );

        $chatRoom->addMember($owner->getId());
    }

    public function testMarkMessagesAsViewedGeneratesEvent()
    {
        $chatRoom = $this->createTestChatRoomEntity();
        $user = $this->createTestUserEntity();

        $chatRoom->addMember($user->getId());
        $chatRoom->markMessagesAsViewed($user->getId());

        $event = $chatRoom->getEvents()[0];

        $this->assertInstanceOf(UserSawChatRoomEvent::class, $event);
        $payload = $event->getPayload();
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('user', $payload);
        $this->assertArrayHasKey('at', $payload);
        $this->assertEquals($user->getId(), $payload['user']);
        $this->assertInstanceOf(DateTimeImmutable::class, DateTimeImmutable::createFromFormat(Event::DATE_FORMAT, $payload['at']));
    }

    public function testMarkMessagesAsViewedFails()
    {
        $chatRoom = $this->createTestChatRoomEntity();
        $user = $this->createTestUserEntity();

        $this->expectException(UserDoesNotPertainsToChatRoomException::class);
        $chatRoom->markMessagesAsViewed($user->getId());
    }
}
