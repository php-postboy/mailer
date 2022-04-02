<?php

declare(strict_types=1);

namespace Postboy\Mailer\XMailerGenerator;

use Postboy\Contract\Message\MessageInterface;

interface XMailerGeneratorInterface
{
    /**
     * @param MessageInterface $message
     * @return string|null
     */
    public function generateXMailer(MessageInterface $message): ?string;
}
