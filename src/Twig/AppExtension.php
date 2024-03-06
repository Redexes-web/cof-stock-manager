<?php

namespace App\Twig;

use Twig\TwigTest;
use Twig\TwigFilter;
use App\DBAL\GenderEnum;
use App\DBAL\CartStatusEnum;
use App\DBAL\OrderStatusEnum;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getTests()
    {
        return [new TwigTest('ondisk', [$this, 'isOnDisk'])];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('in_array', [$this, 'inArray']),
            new TwigFilter('is_granted', [$this, 'isGranted']),
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    public function isGranted($user, $role)
    {
        $reachableRoles = $this->container->get(RoleHierarchyInterface::class)->getReachableRoleNames($user->getRoles());

        foreach ($reachableRoles as $reachableRole) {
            if ($reachableRole === $role) {
                return true;
            }
        }

        return false;
        return $this->container->get(Security::class)->isGranted($role, $user);
    }
    public function inArray($haystack, $needle)
    {
        return in_array($needle, $haystack);
    }

    public function jsonDecode($json)
    {
        return json_decode($json);
    }

    public function isOnDisk($path)
    {
        $projDir = $this->container->get(ParameterBagInterface::class)->get('kernel.project_dir');
        return file_exists($projDir . 'public/' . $path);
    }


    public static function getSubscribedServices(): array
    {
        return [
            ParameterBagInterface::class,
            RoleHierarchyInterface::class
        ];
    }
}
