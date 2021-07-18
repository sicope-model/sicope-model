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

use App\Exception\RuntimeException;
use App\Notification\BugNotification;
use App\Repository\Account\UserRepository;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;
use Tienvx\Bundle\MbtBundle\Service\Bug\BugNotifierInterface;

class BugNotifier implements BugNotifierInterface
{
    protected UserRepository $userRepository;
    protected NotifierInterface $notifier;
    protected string $mailSenderAddress;
    protected string $mailSenderName;

    public function __construct(
        UserRepository $userRepository,
        NotifierInterface $notifier,
        string $mailSenderAddress,
        string $mailSenderName
    ) {
        $this->userRepository = $userRepository;
        $this->notifier = $notifier;
        $this->mailSenderAddress = $mailSenderAddress;
        $this->mailSenderName = $mailSenderName;
    }

    public function notify(BugInterface $bug): void
    {
        $this->notifier->send(
            new BugNotification($bug, $this->mailSenderAddress, $this->mailSenderName),
            $bug->getTask()->getTaskConfig()->getNotifyAuthor() && $bug->getTask()->getAuthor()
                ? $this->getRecipient($bug->getTask()->getAuthor())
                : new NoRecipient()
        );
    }

    protected function getRecipient(int $userId): RecipientInterface
    {
        $user = $this->userRepository->find($userId);
        if (!$user instanceof UserInterface) {
            throw new RuntimeException('Task author not found');
        }
        $profile = $user->getProfile() ?? null;

        return new Recipient((string) $user->getEmail(), (string) ($profile ? $profile->getPhone() : null));
    }
}
