<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Decrypting;

interface DecryptingInterface
{
    /**
     * @param string $data
     * @param string $iv
     * @param string $key
     *
     * @return string
     */
    public function decrypt($data, $iv, $key);

    /**
     * @return string
     */
    public function generateInitializationVector();

    /**
     * @return integer
     */
    public function getKeyLength();

    /**
     * @return string
     */
    public function getType();
}