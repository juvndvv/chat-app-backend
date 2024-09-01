<?php

declare(strict_types=1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\Exception\MessageCreationException;
use App\Chat\Domain\ValueObject\MessageContent;
use App\Chat\Domain\ValueObject\MessageId;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Random\RandomException;

final class TextMessage extends AbstractMessage
{
    private MessageId $id;
    private UserId $userId;
    private MessageContent $content;

    /**
     * Sets the unique identifier of the message.
     *
     * @param string $id The message ID.
     * @return self
     * @throws InvalidArgumentException If the ID is invalid.
     */
    public function setId(string $id): self
    {
        $this->id = MessageId::create($id);
        return $this;
    }

    /**
     * Sets the user ID who sent the message.
     *
     * @param string $userId The user ID.
     * @return self
     * @throws InvalidArgumentException If the user ID is invalid.
     */
    public function setUserId(string $userId): self
    {
        $this->userId = UserId::create($userId);
        return $this;
    }

    /**
     * Sets the content of the message.
     *
     * @param string $content The message content.
     * @return self
     * @throws InvalidArgumentException If the content is invalid.
     */
    public function setContent(string $content): self
    {
        $this->content = MessageContent::create(trim($content));
        return $this;
    }

    /**
     * Sets the timestamp when the message was created.
     *
     * @param DateTimeImmutable $createdAt The creation timestamp.
     * @return self
     * @throws InvalidArgumentException If the timestamp is invalid.
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = DateTimeValueObject::create($createdAt);
        return $this;
    }

    /**
     * Gets the unique identifier of the message.
     *
     * @return string The message ID.
     * @throws LogicException If the message ID is not set.
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('Message\'s id is not set');
        }

        return $this->id->value();
    }

    /**
     * Gets the user ID who sent the message.
     *
     * @return string The user ID.
     * @throws LogicException If the user ID is not set.
     */
    public function getUserId(): string
    {
        if (!isset($this->userId)) {
            throw new LogicException('Message\'s userId is not set');
        }

        return $this->userId->value();
    }

    /**
     * Gets the content of the message.
     *
     * @return string The message content.
     * @throws LogicException If the message content is not set.
     */
    public function getContent(): string
    {
        if (!isset($this->content)) {
            throw new LogicException('Message\'s content is not set');
        }

        return $this->content->value();
    }

    /**
     * Updates the content of the message and marks it as updated.
     *
     * @param string $content The new message content.
     * @return void
     * @throws InvalidArgumentException If the content is invalid.
     */
    public function updateContent(string $content): void
    {
        $this->setContent($content);
        $this->performUpdate();
    }

    /**
     * Creates a new instance of the TextMessage entity with mandatory fields.
     *
     * @param string $userId The user ID who sent the message.
     * @param string $text The content of the message.
     * @param int $try The number of attempts to generate a unique message ID.
     * @return self A new instance of the TextMessage entity.
     * @throws MessageCreationException If the ID generation fails after multiple attempts.
     * @throws InvalidArgumentException If any argument is invalid.
     */
    public static function create(
        string $userId,
        string $text,
        int    $try = 1
    ): self
    {
        try {
            $now = new DateTimeImmutable();

            return self::build()
                ->setId(MessageId::generate()->value())
                ->setUserId($userId)
                ->setContent($text)
                ->setCreatedAt($now)
                ->setUpdatedAt($now)
                ->setDeletedAt(null); // default

            // TODO create event

        } catch (RandomException $e) {
            if ($try === 3) {
                throw new MessageCreationException('Failed to generate message ID after multiple attempts: ' . $e->getMessage());
            }

            $try += 1;
            return self::create($userId, $text, $try);
        }
    }
}
