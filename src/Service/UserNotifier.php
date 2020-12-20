<?php


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
            return new Recipient((string)$email, (string)$phone);
        }

        return new NoRecipient();
    }
}
