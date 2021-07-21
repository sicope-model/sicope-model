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
use Pd\UserBundle\Model\Group as BaseGroup;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User Groups.
 *
 * @ORM\Table(name="user_group")
 * @ORM\Entity(repositoryClass="App\Repository\Account\GroupRepository")
 * @UniqueEntity(fields="name", message="group_already_taken")
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Group extends BaseGroup
{
}
