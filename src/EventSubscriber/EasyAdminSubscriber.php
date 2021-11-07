<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;
use Tienvx\Bundle\MbtBundle\Service\SelenoidHelperInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private HttpClientInterface $client, private SelenoidHelperInterface $selenoidHelper)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setAuthor'],
            BeforeEntityDeletedEvent::class => ['deleteVideoAndLog'],
        ];
    }

    public function setAuthor(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Task) && !($entity instanceof Model)) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->setAuthor($user->getId());
        }
    }

    public function deleteVideoAndLog(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Task) && !($entity instanceof Bug)) {
            return;
        }

        try {
            $this->client->request('DELETE', $this->selenoidHelper->getVideoUrl($entity));
            $this->client->request('DELETE', $this->selenoidHelper->getLogUrl($entity));
        } catch (ClientException $exception) {
        }
    }
}
