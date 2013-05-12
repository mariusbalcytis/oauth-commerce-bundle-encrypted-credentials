<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MabaOAuthCommerceEncryptedCredentialsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config['local'] as $key => $localConfig) {
            $provider = new DefinitionDecorator('maba_oauth_commerce_encrypted_credentials.base_local_token_provider');
            $provider->setArguments(array(
                $this->createCertificateProvider($localConfig['certificate'], $container, $key),
                new Reference($localConfig['credentials_handler']),
                $this->createDecrypting($localConfig['decrypting']),
                $this->createKeyExchange($localConfig['key_exchange']),
                $key,
                $localConfig['expiration_interval'],
            ));
            $provider->addTag('maba_oauth_commerce_encrypted_credentials.token_provider');
            $container->setDefinition(
                'maba_oauth_commerce_encrypted_credentials.local_token_provider.' . $key,
                $provider
            );
        }

    }

    protected function createCertificateProvider(array $certificate, ContainerBuilder $container, $key)
    {
        if (!empty($certificate['id'])) {
            return new Reference($certificate['id']);
        } else {
            $provider = new DefinitionDecorator(
                'maba_oauth_commerce_encrypted_credentials.base_local_certificate_provider'
            );
            $provider->addMethodCall('setCertificateContent', array($certificate['content']));
            $provider->addMethodCall('setHasher', array($this->createHasher($certificate['hash'])));
            $id = 'maba_oauth_commerce_encrypted_credentials.base_local_certificate_provider.' . $key;
            $container->setDefinition($id, $provider);
            return new Reference($id);
        }
    }
    protected function createDecrypting(array $config)
    {
        if (!empty($config['id'])) {
            return new Reference($config['id']);
        } else {
            return new Definition(
                'Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Decrypting\MCryptDecrypting',
                array($config['algorithm'], $config['type'])
            );
        }
    }
    protected function createKeyExchange(array $config)
    {
        if (!empty($config['id'])) {
            return new Reference($config['id']);
        } else {
            return new Definition(
                'Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange\DiffieHellman\Group2KeyExchange',
                array($config['private_key'], $config['private_key_password'])
            );
        }
    }
    protected function createHasher(array $config)
    {
        if (!empty($config['id'])) {
            return new Reference($config['id']);
        } else {
            return new Definition(
                'Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Hash\Hasher',
                array($config['algorithm'], $config['type'])
            );
        }
    }
}
