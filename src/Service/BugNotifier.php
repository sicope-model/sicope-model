<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
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
use Tienvx\Bundle\MbtBundle\Service\Bug\BugNotifierInterface;

class BugNotifier implements BugNotifierInterface
{
    public function __construct(
        protected UserRepository $userRepository,
        protected NotifierInterface $notifier,
        protected Config $config,
        protected string $emailSender
    ) {
    }

    public function notify(BugInterface $bug): void
    {
        $this->notifier->send(
            new BugNotification($bug, $this->emailSender),
            $this->config->shouldNotifyAuthor() && $bug->getTask()->getAuthor()
                ? $this->getRecipient($bug->getTask()->getAuthor())
                : new NoRecipient()
        );
    }

    protected function getRecipient(int $userId): RecipientInterface
    {
        $user = $this->userRepository->find($userId);
        if (!$user instanceof User) {
            throw new RuntimeException('Task author not found');
        }

        return new Recipient((string) $user->getEmail());
    }
}
