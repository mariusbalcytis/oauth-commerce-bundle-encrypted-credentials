<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity;


class Session
{
    /**
     * @var Certificate
     */
    protected $certificate;

    /**
     * @var KeyExchange
     */
    protected $keyExchange;

    /**
     * @var Cipher
     */
    protected $cipher;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @param Certificate $certificate
     *
     * @return $this
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @return Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param Cipher $cipher
     *
     * @return $this
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;

        return $this;
    }

    /**
     * @return Cipher
     */
    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * @param KeyExchange $keyExchange
     *
     * @return $this
     */
    public function setKeyExchange($keyExchange)
    {
        $this->keyExchange = $keyExchange;

        return $this;
    }

    /**
     * @return KeyExchange
     */
    public function getKeyExchange()
    {
        return $this->keyExchange;
    }

    /**
     * @param string $sessionId
     *
     * @return $this
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }


}