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
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;
use Tienvx\Bundle\MbtBundle\Service\NotifyHelperInterface;

class NotifyHelper implements NotifyHelperInterface
{
    protected UserRepository $userRepository;
    protected UrlGeneratorInterface $router;
    protected string $mailSenderAddress;
    protected string $mailSenderName;

    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $router,
        string $mailSenderAddress,
        string $mailSenderName
    ) {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->mailSenderAddress = $mailSenderAddress;
        $this->mailSenderName = $mailSenderName;
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

    public function getBugUrl(BugInterface $bug): string
    {
        return $this->router->generate('admin_bug_view', ['bug' => $bug->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getFromAddress(): Address
    {
        return new Address($this->mailSenderAddress, $this->mailSenderName);
    }
}
