<?php

declare(strict_types=1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\Exception\MessageCreationException;
use App\Chat\Domain\ValueObject\MessageContent;
use App\Chat\Domain\ValueObject\MessageId;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use DateTimeImmutable;
use Random\RandomException;

final class TextMessage extends AbstractMessage
{
    private MessageContent $content;

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
