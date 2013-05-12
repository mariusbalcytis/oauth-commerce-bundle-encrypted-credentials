<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity;


class SessionRelation
{
    const CLASS_NAME = 'Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\SessionRelation';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var string
     */
    protected $providerSessionId;

    /**
     * @var int
     */
    protected $credentialsId;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $providerKey
     *
     * @return $this
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * @param string $providerSessionId
     *
     * @return $this
     */
    public function setProviderSessionId($providerSessionId)
    {
        $this->providerSessionId = $providerSessionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderSessionId()
    {
        return $this->providerSessionId;
    }

    /**
     * @param int $credentialsId
     *
     * @return $this
     */
    public function setCredentialsId($credentialsId)
    {
        $this->credentialsId = $credentialsId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCredentialsId()
    {
        return $this->credentialsId;
    }


}