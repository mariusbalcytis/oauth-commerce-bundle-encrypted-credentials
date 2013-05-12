<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Hash;


interface HasherInterface
{
    /**
     * @param string $data
     *
     * @return string
     */
    public function hash($data);

    /**
     * @return string
     */
    public function getType();
}