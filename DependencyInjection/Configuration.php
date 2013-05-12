<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('maba_o_auth_commerce_encrypted_credentials');

        $rootNode
            ->children()
                ->arrayNode('local')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('certificate')->isRequired()->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('content')->end()
                                ->arrayNode('hash')->children()
                                    ->scalarNode('id')->end()
                                    ->scalarNode('algorithm')->end()
                                    ->scalarNode('type')->end()
                                ->end()->end()
                            ->end()->end()
                            ->arrayNode('decrypting')->isRequired()->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('algorithm')->end()
                                ->scalarNode('type')->end()
                            ->end()->end()
                            ->arrayNode('key_exchange')->isRequired()->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('private_key')->end()
                                ->scalarNode('private_key_password')->defaultNull()->end()
                            ->end()->end()
                            ->scalarNode('credentials_handler')->isRequired()->end()
                            ->scalarNode('expiration_interval')
                                ->defaultValue('P2D')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
