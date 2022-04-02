<?php

declare(strict_types=1);

namespace Postboy\Mailer\IdGenerator;

use Postboy\Contract\Message\MessageInterface;
use Postboy\Email\Email;
use Postboy\Email\Exception\InvalidEmailException;

class PostboyIdGenerator implements IdGeneratorInterface
{
    /**
     * @inheritDoc
     */
    final public function generateId(MessageInterface $message): string
    {
        try {
            $sender = Email::createFromString($message->getHeader('From'));
            $host = explode('@', $sender->getAddress())[1];
        } catch (InvalidEmailException $e) {
            $host = 'postboy.generated';
        }

        return sprintf(
            '<%04x%04x-%04x-%04x-%04x-%04x%04x%04x@%s>',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            $host
        );
    }
}
