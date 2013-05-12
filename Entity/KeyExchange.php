<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity;


class KeyExchange
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $privateParameters;

    /**
     * @var array
     */
    protected $publicParameters;

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->privateParameters + $this->publicParameters;
    }

    /**
     * @param array $privateParameters
     *
     * @return $this
     */
    public function setPrivateParameters($privateParameters)
    {
        $this->privateParameters = $privateParameters;

        return $this;
    }

    /**
     * @param array $publicParameters
     *
     * @return $this
     */
    public function setPublicParameters($publicParameters)
    {
        $this->publicParameters = $publicParameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getPublicParameters()
    {
        return $this->publicParameters;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


}