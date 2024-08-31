<?php

namespace Tests\Chat\Domain\Entity;

use App\Shared\Domain\Exception\UserAlreadyInChatRoomException;
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
}
