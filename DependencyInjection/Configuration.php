<?php

namespace Beeldspraak\SecuredAreaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('beeldspraak_secured_area');

        $rootNode
            ->children()
                ->scalarNode('role')
                    ->defaultValue('PAGE_VIEW')
                    ->info("Role to be used for access granted checks.")
                    ->example('$securityContext->isGranted(\'PAGE_VIEW\' , $contentDocument);')
                ->end()
                ->arrayNode('login_routes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('login_path')->defaultValue('fos_user_security_login')->end()
                        ->scalarNode('check_path')->defaultValue('fos_user_security_check')->end()
                        ->scalarNode('logout_path')->defaultValue('fos_user_security_logout')->end()
                    ->end()
                    ->info('Login routes that should be matched for the "secured area" firewall.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
