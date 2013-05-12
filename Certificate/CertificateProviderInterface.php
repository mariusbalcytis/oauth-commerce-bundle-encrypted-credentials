<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Certificate;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Certificate;

interface CertificateProviderInterface
{

    /**
     * @return Certificate
     */
    public function getCertificate();
}