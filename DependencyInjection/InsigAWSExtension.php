<?php

/*
 * This file is part of the InsigAWSBundle package.
 *
 * (c) Damon Jones <damon@insig.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Insig\AWSBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\Config\FileLocator
    ;

class InsigAWSExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('insig_aws')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('services.xml');

            $validParameters = array(
                'client' => array('access_key_id', 'secret_access_key'),
            );

            foreach ($configs[0] as $section => $parameters) {
                foreach ($parameters as $key => $value) {
                    if (in_array($key, $validParameters[$section])) {
                        $container->setParameter('insig_aws.' . $section . '.' . $key, $value);
                    }
                }
            }
        }
    }

    public function getAlias()
    {
        return 'insig_aws';
    }
}
