<?php

declare(strict_types=1);

namespace Staff\Postboy\Mailer;

use Postboy\Contract\Message\MessageInterface;
use Postboy\Contract\Transport\TransportInterface;

class TestTransport implements TransportInterface
{
    /**
     * @var MessageInterface[]
     */
    private array $messages = [];

    /**
     * @inheritDoc
     */
    public function send(MessageInterface $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return MessageInterface|null
     */
    public function pull(): ?MessageInterface
    {
        if (empty($this->messages)) {
            return null;
        }
        return array_shift($this->messages);
    }
}
