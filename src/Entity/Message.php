<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'messenger_messages')]
#[ORM\Index(fields: ['queueName'])]
#[ORM\Index(fields: ['availableAt'])]
#[ORM\Index(fields: ['deliveredAt'])]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', nullable: false, options: ['autoincrement' => true])]
    private int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $body;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $headers;

    #[ORM\Column(name: 'queue_name', type: 'string', nullable: false, length: 190)]
    private string $queueName;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'available_at', type: 'datetime', nullable: false)]
    private DateTimeInterface $availableAt;

    #[ORM\Column(name: 'delivered_at', type: 'datetime', nullable: true)]
    private DateTimeInterface $deliveredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): string
    {
        return $this->headers;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
