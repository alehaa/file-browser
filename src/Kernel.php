<?php

/* This file is part of file-browser.
 *
 * Copyright (C)
 *  2018 Alexander Haase <ahaase@alexhaase.de>
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * NOTE: This file includes autogenerated code from Symfony, also licensed under
 *       the MIT license.
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * {@inheritdoc}
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Possible extensions of configuration files.
     */
    protected const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    /**
     * Get the application's configuration directory.
     *
     *
     * @return string the application's configuration directory
     */
    public function getConfigDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        /* Register all bundles defined in the 'bundles.php', which is managed
         * by Symfony Flex. */
        $contents = require $this->getConfigDir().'/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(
        ContainerBuilder $container,
        LoaderInterface $loader
    ) {
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);

        $confDir = $this->getConfigDir();

        /* Load configuration files for packages. The configuration files might
         * reside in the 'packages' subdirectory or a subdirectory named after
         * the current environment in 'packages'. */
        $loader->load($confDir.'/packages/*'.self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir.'/packages/'.$this->environment)) {
            $loader->load(
                $confDir.'/packages/'.$this->environment
                        .'/**/*'.self::CONFIG_EXTS,
                'glob'
            );
        }

        /* Load the general configuration files for services and a environment
         * specific one, if available. */
        $loader->load($confDir.'/services'.self::CONFIG_EXTS, 'glob');
        $loader->load(
            $confDir.'/services_'.$this->environment.self::CONFIG_EXTS,
            'glob'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getConfigDir();

        /* Load the route configuration from the configuration directory. The
         * configuration files might reside in the 'routes' subdirectory, a
         * subdirectory named after the current environment in 'routes' or in
         * the configuration path with as 'routes.CONFIG_EXTS'. */
        if (is_dir($confDir.'/routes/')) {
            $routes->import(
                $confDir.'/routes/*'.self::CONFIG_EXTS,
                '/',
                'glob'
            );
        }
        if (is_dir($confDir.'/routes/'.$this->environment)) {
            $routes->import(
                $confDir.'/routes/'.$this->environment
                        .'/**/*'.self::CONFIG_EXTS,
                '/',
                'glob'
            );
        }
        $routes->import($confDir.'/routes'.self::CONFIG_EXTS, '/', 'glob');
    }
}
