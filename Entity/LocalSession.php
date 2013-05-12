<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity;


class LocalSession
{
    const CLASS_NAME = 'Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\LocalSession';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $iv;

    /**
     * @var array
     */
    protected $keyExchangeParameters = array();

    /**
     * @var array
     */
    protected $publicParameters = array();

    /**
     * @var string
     */
    protected $credentialsType;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $iv
     *
     * @return $this
     */
    public function setIv($iv)
    {
        $this->iv = $iv;

        return $this;
    }

    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param array $keyExchangeParameters
     *
     * @return $this
     */
    public function setKeyExchangeParameters($keyExchangeParameters)
    {
        $this->keyExchangeParameters = $keyExchangeParameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeyExchangeParameters()
    {
        return $this->keyExchangeParameters;
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
     * @param string $credentialsType
     *
     * @return $this
     */
    public function setCredentialsType($credentialsType)
    {
        $this->credentialsType = $credentialsType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredentialsType()
    {
        return $this->credentialsType;
    }

}