<?php

namespace App\Mail;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Extended MailMessage that tracks email type and metadata for logging
 *
 * This class extends Laravel's MailMessage to include additional properties
 * that can be used by the EmailLoggingListener to automatically log emails
 * to the EmailLog model.
 */
class LoggedMailMessage extends MailMessage
{
    /**
     * The type of email being sent
     *
     * @var string|null
     */
    public $emailType;

    /**
     * The user ID associated with this email
     *
     * @var int|null
     */
    public $userId;

    /**
     * Additional metadata for the email log
     *
     * @var array
     */
    public $metadata = [];

    /**
     * Set the email type for logging purposes
     *
     * @param string $type
     * @return $this
     */
    public function emailType(string $type): self
    {
        $this->emailType = $type;
        return $this;
    }

    /**
     * Set the user ID for logging purposes
     *
     * @param int|null $userId
     * @return $this
     */
    public function forUser(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Set metadata for logging purposes
     *
     * @param array $metadata
     * @return $this
     */
    public function withMetadata(array $metadata): self
    {
        foreach ($metadata as $key => $value) {
            if ($value === null) {
                // Skip null values entirely
                continue;
            }

            // Ensure all metadata values are stored as strings
            $this->metadata[$key] = (string) $value;
        }

        return $this;
    }

    /**
     * Add a single metadata key-value pair
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addMetadata(string $key, $value): self
    {
        if ($value !== null) {
            $this->metadata[$key] = (string) $value;
        }

        return $this;
    }

    /**
     * Get the email type
     *
     * @return string|null
     */
    public function getEmailType(): ?string
    {
        return $this->emailType;
    }

    /**
     * Get the user ID
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Get the metadata
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
