<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Notification;

use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;

class BugNotification extends Notification implements
    ChatNotificationInterface,
    EmailNotificationInterface,
    SmsNotificationInterface
{
    protected BugInterface $bug;
    protected string $mailSenderAddress;
    protected string $mailSenderName;

    public function __construct(BugInterface $bug, string $mailSenderAddress, string $mailSenderName)
    {
        $this->bug = $bug;
        $this->mailSenderAddress = $mailSenderAddress;
        $this->mailSenderName = $mailSenderName;

        parent::__construct('New bug found');
    }

    public function asChatMessage(RecipientInterface $recipient, ?string $transport = null): ?ChatMessage
    {
        if ('slack' !== $transport) {
            return null;
        }

        $message = ChatMessage::fromNotification($this);
        $message->subject($this->getSubject());
        $message->options(
            (new SlackOptions())
            ->iconEmoji('bug')
            ->username($this->mailSenderName)
            ->block((new SlackSectionBlock())->text($this->getSubject()))
            ->block(new SlackDividerBlock())
            ->block((new SlackSectionBlock())->text(sprintf('Bug id: %d', $this->bug->getId())))
            ->block((new SlackSectionBlock())->text(sprintf('Task id: %d', $this->bug->getTask()->getId())))
            ->block((new SlackSectionBlock())->text(sprintf('Message: %d', $this->bug->getMessage())))
            ->block((new SlackSectionBlock())->text(sprintf('Steps: %d', \count($this->bug->getSteps()))))
        );

        return $message;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);
        $message->getMessage()
            ->from(new Address($this->mailSenderAddress, $this->mailSenderName))
            ->htmlTemplate('emails/bug_notification.html.twig')
            ->context(['bug' => $this->bug])
        ;

        return $message;
    }

    public function asSmsMessage(RecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        if ($recipient instanceof SmsRecipientInterface) {
            return new SmsMessage($recipient->getPhone(), $this->getSubject());
        }

        return null;
    }
}
