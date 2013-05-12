<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Hash;


class Hasher implements HasherInterface
{
    protected $algorithm;
    protected $type;

    public function __construct($algorithm, $type)
    {
        $this->algorithm = $algorithm;
        $this->type = $type;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public function hash($data)
    {
        return base64_encode(hash($this->algorithm, $data, true));
    }

    public function getType()
    {
        return $this->type;
    }
}