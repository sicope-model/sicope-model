<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Service;

use App\Entity\User;
use App\Notification\BugNotification;
use App\Repository\UserRepository;
use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;
use Tienvx\Bundle\MbtBundle\Model\TaskInterface;
use Tienvx\Bundle\MbtBundle\Service\Bug\BugNotifierInterface;

class BugNotifier implements BugNotifierInterface
{
    public function __construct(
        protected UserRepository $userRepository,
        protected NotifierInterface $notifier,
        protected Config $config
    ) {
    }

    public function notify(BugInterface $bug): void
    {
        if ($channels = $this->config->getNotifyChannels()) {
            $this->notifier->send(
                new BugNotification($bug, $this->config->getNotifyEmailSender(), $channels),
                $this->getRecipient($bug->getTask(), $channels)
            );
        }
    }

    protected function getRecipient(TaskInterface $task, array $channels): RecipientInterface
    {
        if (!$this->config->shouldNotifyAuthor() || !$task->getAuthor() || !\in_array('email', $channels)) {
            return new NoRecipient();
        }

        $user = $this->userRepository->find($task->getAuthor());
        if (!$user instanceof User) {
            throw new RuntimeException('Task author not found');
        }

        return new Recipient((string) $user->getEmail());
    }
}
