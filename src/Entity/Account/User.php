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

namespace App\Entity\Account;

use Doctrine\ORM\Mapping as ORM;
use Pd\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User Accounts.
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\Account\UserRepository")
 * @UniqueEntity(fields="email", message="email_already_taken")
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="boolean", name="locked")
     */
    protected $freeze;
}
