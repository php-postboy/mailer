<?php

declare(strict_types=1);

namespace Tests\Postboy\Mailer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Postboy\Contract\Message\MessageInterface;
use Postboy\Contract\Transport\Exception\RecipientsListIsEmptyException;
use Postboy\Email\Email;
use Postboy\Mailer\Mailer;
use Postboy\Message\Body\BodyPart;
use Postboy\Message\Body\Stream\StringStream;
use Postboy\Message\Message;
use Staff\Postboy\Mailer\TestTransport;

class MailerTest extends TestCase
{
    public function testSendWithToRecipient()
    {
        $this->runTestSend(['to']);
    }

    public function testSendWithCcRecipient()
    {
        $this->runTestSend(['cc']);
    }

    public function testSendWithBccRecipient()
    {
        $this->runTestSend(['bcc']);
    }

    public function testSendWithToAndCcRecipient()
    {
        $this->runTestSend(['to', 'cc']);
    }

    public function testSendWithToAndBccRecipient()
    {
        $this->runTestSend(['to', 'bcc']);
    }

    public function testSendWithCcAndBccRecipient()
    {
        $this->runTestSend(['cc', 'bcc']);
    }

    public function testSendWithToAndCcAndBccRecipient()
    {
        $this->runTestSend(['to', 'cc', 'bcc']);
    }

    public function testSendWithoutRecipient()
    {
        $transport = new TestTransport();
        $mailer = new Mailer($transport, new Email('test@phpunit.de', 'Unit Test'));
        $text = 'body';
        $contentType = 'text/plain';
        $subject = 'subject';
        $message = $this->createMessage($subject, $text, $contentType);

        $this->expectException(RecipientsListIsEmptyException::class);
        $mailer->send($message);
    }

    private function runTestSend(array $recipientHeaders)
    {
        $transport = new TestTransport();
        $mailer = new Mailer($transport, new Email('test@phpunit.de', 'Unit Test'));
        $text = 'body';
        $contentType = 'text/plain';
        $subject = 'subject';
        $message = $this->createMessage($subject, $text, $contentType);
        foreach ($recipientHeaders as $recipientHeader) {
            $recipient = new Email(
                $recipientHeader . '-recipient@phpunit.de',
                'Email ' . $recipientHeader . ' Recipient'
            );
            $message->setHeader($recipientHeader, (string)$recipient);
        }

        $mailer->send($message);

        $actual = $transport->pull();
        Assert::assertInstanceOf(MessageInterface::class, $actual);
        /** @var BodyPart $actualBody */
        $actualBody = $actual->getBody();
        Assert::assertInstanceOf(BodyPart::class, $actualBody);
        Assert::assertSame($subject, $actual->getHeader('subject'));
        Assert::assertMatchesRegularExpression(
            '/^<[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}@phpunit\.de>$/ui',
            $actual->getHeader('message-id')
        );
        Assert::assertSame($contentType, $actualBody->getContentType());
        Assert::assertSame('https://github.com/php-postboy/mailer v' . Mailer::VERSION, $actual->getHeader('x-mailer'));
        $actualText = '';
        while (!$actualBody->getStream()->eof()) {
            $actualText .= $actualBody->getStream()->read(4);
        }
        Assert::assertSame($text, $actualText);
    }

    private function createMessage(string $subject, string $text, string $contentType): MessageInterface
    {
        $body = new BodyPart(new StringStream($text), $contentType);
        return new Message($body, $subject);
    }
}
