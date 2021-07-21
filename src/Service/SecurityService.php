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

use App\Entity\Account\User;
use Doctrine\Common\Annotations\Reader;
use Pd\WidgetBundle\Widget\WidgetInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\RouterInterface;

/**
 * Find All Method Permissions.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class SecurityService
{
    private array $roles = [];
    private array $excludeClass = [];

    public function __construct(private RouterInterface $router, private WidgetInterface $widget, private Reader $reader)
    {
    }

    /**
     * Get ACL Roles.
     */
    public function getACL(): array
    {
        return [
            User::ROLE_DEFAULT => User::ROLE_DEFAULT,
            User::ROLE_ALL_ACCESS => User::ROLE_ALL_ACCESS,
        ];
    }

    /**
     * Get All Method Roles.
     *
     * @throws \ReflectionException
     */
    public function getRoles(): array
    {
        if (!\count($this->roles)) {
            $this->extractWidgetRoles();
            foreach ($this->getRouterList() as $class => $methods) {
                $reflection = new \ReflectionClass($class);
                $this->extractClassCustomRoles($reflection);
                foreach ($methods as $method) {
                    $this->extractMethodRoles($reflection, $method);
                }
            }
        }

        $roles = [];
        foreach (array_unique($this->roles) as $role) {
            $roleObject = explode('_', $role);
            if (3 === \count($roleObject)) {
                $access = $roleObject[2];
                $roleObject = $roleObject[0] . '_' . $roleObject[1];

                if (isset($roles[$roleObject])) {
                    $roles[$roleObject][$access] = $roleObject . '_' . $access;
                } else {
                    $roles[$roleObject] = [$access => $roleObject . '_' . $access];
                }
            }
        }

        return $roles;
    }

    private function extractClassCustomRoles(\ReflectionClass $reflection): void
    {
        if ($reflection->hasConstant('CUSTOM_ROLES')) {
            $this->roles = array_merge($this->roles, array_values($reflection->getConstant('CUSTOM_ROLES')));
        }
    }

    private function extractMethodRoles(\ReflectionClass $reflection, string $method): void
    {
        if (!$reflection->hasMethod($method)) {
            return;
        }

        // Find Attribute
        $roles = array_map(
            static function ($attribute) {
                $roles = array_map(static fn ($roles) => (array) $roles, $attribute->getArguments());

                return array_merge([], ...$roles);
            },
            $reflection->getMethod($method)->getAttributes(IsGranted::class)
        );

        // Find Annotation
        foreach ($this->reader->getMethodAnnotations($reflection->getMethod($method)) as $access) {
            if ($access instanceof IsGranted) {
                $roles[] = (array) $access->getAttributes();
            }
        }

        $this->roles = array_merge($this->roles, ...$roles);
    }

    private function extractWidgetRoles(): void
    {
        $roles = array_values(array_map(
            static fn ($widget) => $widget->getRole(),
            $this->widget->getWidgets(false)
        ));

        $this->roles = array_merge($this->roles, ...$roles);
    }

    private function getRouterList(): array
    {
        $list = [];
        foreach ($this->router->getRouteCollection()->all() as $router) {
            if ($router->getDefault('_controller')) {
                [$class, $method] = explode('::', $router->getDefault('_controller'));
                if (!class_exists($class) || \in_array($class, $this->excludeClass, true)) {
                    continue;
                }

                if (!isset($list[$class])) {
                    $list[$class] = [];
                }
                $list[$class][] = $method;
            }
        }

        return $list;
    }
}
