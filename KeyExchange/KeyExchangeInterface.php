<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\KeyExchange;

interface KeyExchangeInterface
{

    /**
     * @return KeyExchange
     */
    public function generateKeyExchange();

    /**
     * @param array   $keyExchangeParameters
     * @param array   $additionalParameters
     * @param integer $sharedKeyLength
     *
     * @throws \InvalidArgumentException
     * @return string binary shared key for symmetric algorithm
     */
    public function generateCommonKey(array $keyExchangeParameters, array $additionalParameters, $sharedKeyLength);
}