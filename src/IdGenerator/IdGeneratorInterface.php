<?php

declare(strict_types=1);

namespace Postboy\Mailer\IdGenerator;

use Postboy\Contract\Message\MessageInterface;

interface IdGeneratorInterface
{
    /**
     * @param MessageInterface $message
     * @return string
     */
    public function generateId(MessageInterface $message): string;
}
