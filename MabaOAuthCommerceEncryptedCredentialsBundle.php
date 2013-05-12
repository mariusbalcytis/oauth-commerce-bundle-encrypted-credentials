<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\DependencyInjection\AddTaggedCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MabaOAuthCommerceEncryptedCredentialsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddTaggedCompilerPass(
            'maba_oauth_commerce_encrypted_credentials.manager',
            'maba_oauth_commerce_encrypted_credentials.token_provider',
            'addTokenProvider'
        ));
    }

}
