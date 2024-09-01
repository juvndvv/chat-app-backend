<?php

declare(strict_types=1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\Exception\ChatRoomCannotBeEmptyException;
use App\Chat\Domain\ValueObject\ChatRoomDescription;
use App\Chat\Domain\ValueObject\ChatRoomId;
use App\Chat\Domain\ValueObject\ChatRoomName;
use App\Chat\Domain\ValueObject\MessageId;
use App\Helpers\ValueObjectList;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Exception\MessageAlreadyInChatException;
use App\Shared\Domain\Exception\UserAlreadyInChatRoomException;
use App\Shared\Domain\Exception\UserDoesNotPertainsToChatRoomException;
use App\User\Domain\ValueObject\UserId;

/**
 * Represents a chat room.
 *
 * The `ChatRoom` class encapsulates the logic for managing a chat room, including member management, message handling, and general room information.
 * It provides methods to add and remove members, add messages, and update the room's details.
 *
 * @package App\Chat\Domain\Entity
 */

final class ChatRoom extends Entity
{
    private ChatRoomId $id;
    private ChatRoomName $name;
    private ChatRoomDescription $description;
    private ValueObjectList $members;
    private ValueObjectList $messages;
    private UserId $creatorId;

    /**
     * Sets the chat room ID.
     *
     * @param string $id The ID to set.
     * @return self
     * @throws InvalidArgumentException If the provided ID is not valid.
     */
    public function setId(string $id): self
    {
        $this->id = ChatRoomId::create($id);
        return $this;
    }

    /**
     * Sets the chat room name.
     *
     * @param string $name The name to set.
     * @return self
     * @throws InvalidArgumentException If the provided name is not valid.
     */
    public function setName(string $name): self
    {
        $this->name = ChatRoomName::create($name);
        return $this;
    }

    /**
     * Sets the chat room description.
     *
     * @param string $description The description to set.
     * @return self
     * @throws InvalidArgumentException If the provided description is not valid.
     */
    public function setDescription(string $description): self
    {
        $this->description = ChatRoomDescription::create($description);
        return $this;
    }

    /**
     * Sets the members of the chat room.
     *
     * @param array $members An array of member IDs.
     * @return self
     * @throws ChatRoomCannotBeEmptyException If the members array is empty.
     * @throws InvalidArgumentException If a member ID is not valid.
     */
    public function setMembers(array $members): self
    {
        if (count($members) === 0) {
            throw new ChatRoomCannotBeEmptyException();
        }

        $this->members = new ValueObjectList();

        $this->members->set(array_map(
            function (string $member) {
                return UserId::create($member);
            }, $members));

        return $this;
    }

    /**
     * Sets the messages of the chat room.
     *
     * @param array $messages An array of messages.
     * @return self
     */
    public function setMessages(array $messages): self
    {
        $this->messages = new ValueObjectList();
        $this->messages->set($messages);
        return $this;
    }

    /**
     * Sets the creator ID of the chat room.
     *
     * @param string $creatorId The creator ID to set.
     * @return self
     * @throws InvalidArgumentException If the creator ID is not valid.
     */
    public function setCreatorId(string $creatorId): self
    {
        $this->creatorId = UserId::create($creatorId);
        return $this;
    }

    /**
     * Gets the chat room ID.
     *
     * @return string The chat room ID.
     * @throws LogicException If the ID is not set.
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('ChatRoom\'s id is not set');
        }

        return $this->id->value();
    }

    /**
     * Gets the chat room name.
     *
     * @return string The chat room name.
     * @throws LogicException If the name is not set.
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            throw new LogicException('ChatRoom\'s name is not set');
        }

        return $this->name->value();
    }

    /**
     * Gets the chat room description.
     *
     * @return string The chat room description.
     * @throws LogicException If the description is not set.
     */
    public function getDescription(): string
    {
        if (!isset($this->description)) {
            throw new LogicException('ChatRoom\'s description is not set');
        }

        return $this->description->value();
    }

    /**
     * Gets the members of the chat room.
     *
     * @return array An array of member IDs.
     * @throws LogicException If the members are not set.
     */
    public function getMembers(): array
    {
        if (!isset($this->members)) {
            throw new LogicException('ChatRoom\'s members has not been set');
        }

        $members = $this->members->map(function (UserId $member) {
            return $member->value();
        });

        array_unshift($members, $this->creatorId->value());

        return $members;
    }

    /**
     * Gets the number of members in the chat room.
     *
     * @return int The number of members.
     * @throws LogicException If the members are not set.
     */
    public function getMembersCount(): int
    {
        if (!isset($this->members)) {
            throw new LogicException('ChatRoom\'s members has not been set');
        }

        return $this->members->count();
    }

    /**
     * Gets the messages in the chat room.
     *
     * @return array An array of message IDs.
     * @throws LogicException If the messages are not set.
     */
    public function getMessages(): array
    {
        if (!isset($this->messages)) {
            throw new LogicException('Messages has not been set');
        }

        return $this->messages->map(function (MessageId $messageId) {
            return $messageId->value();
        });
    }

    /**
     * Gets the number of messages in the chat room.
     *
     * @return int The number of members.
     * @throws LogicException If the members are not set.
     */
    public function getMessagesCount(): int
    {
        if (!isset($this->messages)) {
            throw new LogicException('ChatRoom\'s messages has not been set');
        }

        return $this->messages->count();
    }

    /**
     * Checks if the chat room is deleted.
     *
     * @return bool True if the chat room is deleted, otherwise false.
     * @throws LogicException If the deletion status is not set.
     */
    public function isDeleted(): bool
    {
        if (!isset($this->deletedAt)) {
            throw new LogicException('The deleted at must be set');
        }

        return $this->deletedAt->value() !== null;
    }

    /**
     * Updates the name of the chat room.
     *
     * @param string $name The new name to set.
     * @param bool $isBulkUpdate Whether this is part of a bulk update.
     * @return self
     * @throws InvalidArgumentException If the name is not valid.
     */
    public function updateName(string $name, bool $isBulkUpdate = false): self
    {
        $this->setName($name);

        if (!$isBulkUpdate) {
            $this->performUpdate();

            // TODO generate event
        }

        return $this;
    }

    /**
     * Updates the description of the chat room.
     *
     * @param string $description The new description to set.
     * @param bool $isBulkUpdate Whether this is part of a bulk update.
     * @return self
     * @throws InvalidArgumentException If the description is not valid.
     */
    public function updateDescription(string $description, bool $isBulkUpdate): self
    {
        $this->setDescription($description);

        if (!$isBulkUpdate) {
            $this->performUpdate();

            // TODO generate event
        }

        return $this;
    }

    /**
     * Adds a member to the chat room.
     *
     * @param string $newMember The ID of the member to add.
     * @throws LogicException If the members list is not set.
     * @throws UserAlreadyInChatRoomException If the member is already in the chat room.
     * @throws InvalidArgumentException If the user's id is invalid
     */
    public function addMember(string $newMember): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        if ($this->hasMember($newMember)) {
            throw new UserAlreadyInChatRoomException();
        }

        $this->members->append(UserId::create($newMember));

        // TODO generate event
    }

    /**
     * Removes a member from the chat room.
     *
     * @param string $member The ID of the member to remove.
     * @throws LogicException If the members list is not set.
     * @throws UserDoesNotPertainsToChatRoomException If the member is not in the chat room.
     */
    public function removeMember(string $member): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        $success = $this->members->delete($member);

        if (!$success) {
            throw new UserDoesNotPertainsToChatRoomException();
        }

        // TODO generate event
    }

    /**
     * Adds a message to the chat room.
     *
     * @param string $newMessage The ID of the message to add.
     * @throws MessageAlreadyInChatException If the message is already in the chat room.
     * @throws InvalidArgumentException If the message's id is not valid
     */
    public function addMessage(string $newMessage): void
    {
        if ($this->hasMessage($newMessage)) {
            throw new MessageAlreadyInChatException();
        }

        $this->messages->append(MessageId::create($newMessage));

        // TODO generate event
    }

    /**
     * Checks if a member is in the chat room.
     *
     * @param string $member The ID of the member to check.
     * @return bool True if the member is in the chat room, otherwise false.
     */
    public function hasMember(string $member): bool
    {
        return $this->members->contains($member) || $this->creatorId->value() === $member;
    }

    /**
     * Checks if a message is in the chat room.
     *
     * @param string $message The ID of the message to check.
     * @return bool True if the message is in the chat room, otherwise false.
     */
    public function hasMessage(string $message): bool
    {
        return $this->messages->contains($message);
    }
}
