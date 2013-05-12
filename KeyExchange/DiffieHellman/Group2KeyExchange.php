<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange\DiffieHellman;

class Group2KeyExchange extends BaseKeyExchange
{
    const PRIME = '179769313486231590770839156793787453197860296048756011706444423684197180216158519368947833795864925541502180565485980503646440548199239100050792877003355816639229553136239076508735759914822574862575007425302077447712589550957937778424442426617334727629299387668709205606050270810842907692932019128194467627007';
    const GENERATOR = '2';
    const TYPE = 'dh_rsa_2';

    /**
     * @return string big decimal
     */
    protected function getPrime()
    {
        return self::PRIME;
    }

    /**
     * @return string big decimal
     */
    protected function getGenerator()
    {
        return self::GENERATOR;
    }

    /**
     * @return string
     */
    protected function getKeyExchangeType()
    {
        return self::TYPE;
    }

}