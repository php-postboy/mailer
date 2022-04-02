<?php

declare(strict_types=1);

namespace Postboy\Mailer\XMailerGenerator;

use Postboy\Contract\Message\MessageInterface;
use Postboy\Mailer\Mailer;

class PostboyXMailerGenerator implements XMailerGeneratorInterface
{
    /**
     * @inheritDoc
     */
    final public function generateXMailer(MessageInterface $message): ?string
    {
        return 'https://github.com/php-postboy/mailer v' . Mailer::VERSION;
    }
}
