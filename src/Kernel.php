<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import('../config/{services}.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } else {
            $routes->import('../config/{routes}.php');
        }
    }
    
    public function getLogDir()
    {
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            return '/tmp/log/';
        }

        return parent::getLogDir();
    }

    public function getCacheDir()
    {
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            return '/tmp/cache/'.$this->environment;
        }

        return parent::getCacheDir();
    }

    public function getBuildDir(): string
    {
        if (isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            return '/tmp/var/cache/' . $this->environment;
        }
        return parent::getCacheDir();
    }
}
