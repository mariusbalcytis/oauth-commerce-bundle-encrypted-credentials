<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity;


class Certificate
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $hashType;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hashType
     *
     * @return $this
     */
    public function setHashType($hashType)
    {
        $this->hashType = $hashType;

        return $this;
    }

    /**
     * @return string
     */
    public function getHashType()
    {
        return $this->hashType;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


}