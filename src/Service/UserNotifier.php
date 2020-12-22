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

namespace App\Service;

use App\Repository\UserRepository;
use Pd\UserBundle\Model\ProfileInterface;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Tienvx\Bundle\MbtBundle\Service\UserNotifierInterface;

class UserNotifier implements UserNotifierInterface
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getRecipient(int $userId): RecipientInterface
    {
        $user = $this->userRepository->find($userId);
        $email = $user instanceof UserInterface ? $user->getEmail() : null;
        $profile = $user instanceof UserInterface ? $user->getProfile() : null;
        $phone = $profile instanceof ProfileInterface ? $profile->getPhone() : null;

        if ($email || $phone) {
            return new Recipient((string) $email, (string) $phone);
        }

        return new NoRecipient();
    }
}
