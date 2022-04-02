<?php

declare(strict_types=1);

namespace Postboy\Mailer;

use Postboy\Contract\Message\MessageInterface;
use Postboy\Contract\Transport\Exception\RecipientsListIsEmptyException;
use Postboy\Contract\Transport\Exception\TransportException;
use Postboy\Contract\Transport\TransportInterface;
use Postboy\Email\Email;
use Postboy\Mailer\IdGenerator\IdGeneratorInterface;
use Postboy\Mailer\IdGenerator\PostboyIdGenerator;
use Postboy\Mailer\XMailerGenerator\PostboyXMailerGenerator;
use Postboy\Mailer\XMailerGenerator\XMailerGeneratorInterface;

class Mailer
{
    public const VERSION = '1.0.0';

    private TransportInterface $transport;
    private Email $sender;
    private IdGeneratorInterface $idGenerator;
    private ?XMailerGeneratorInterface $xMailerGenerator;

    public function __construct(
        TransportInterface $transport,
        Email $sender,
        ?IdGeneratorInterface $idGenerator = null,
        ?XMailerGeneratorInterface $xMailerGenerator = null
    ) {
        $this->transport = $transport;
        $this->sender = $sender;
        $this->idGenerator = $idGenerator ?? new PostboyIdGenerator();
        $this->xMailerGenerator = $xMailerGenerator ?? new PostboyXMailerGenerator();
    }

    /**
     * @param MessageInterface $message
     * @return void
     * @throws RecipientsListIsEmptyException
     * @throws TransportException
     */
    final public function send(MessageInterface $message): void
    {
        $this->applySender($message);
        $this->applyMessageId($message);
        $this->applyXMailer($message);
        $this->checkRecipients($message);
        $this->transport->send($message);
    }

    private function applyMessageId(MessageInterface $message): void
    {
        if (!$message->hasHeader('Message-ID')) {
            $message->setHeader('Message-ID', $this->idGenerator->generateId($message));
        }
    }

    private function applySender(MessageInterface $message): void
    {
        if (!$message->hasHeader('From')) {
            $message->setHeader('From', (string)$this->sender);
        }
    }

    private function applyXMailer(MessageInterface $message): void
    {
        if (!$message->hasHeader('X-Mailer')) {
            $xMailer = $this->xMailerGenerator->generateXMailer($message);
            if (!is_null($xMailer)) {
                $message->setHeader('X-Mailer', $xMailer);
            }
        }
    }

    private function checkRecipients(MessageInterface $message): void
    {
        if (count($message->getRecipients()) === 0) {
            throw new RecipientsListIsEmptyException('message has not recipients');
        }
    }
}
