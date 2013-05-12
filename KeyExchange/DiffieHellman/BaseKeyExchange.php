<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange\DiffieHellman;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\KeyExchange;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange\KeyExchangeInterface;
use Zend\Crypt\PublicKey\DiffieHellman;

abstract class BaseKeyExchange implements KeyExchangeInterface
{
    protected $privateKeyResource;
    protected $privateKeyContent;
    protected $privateKeyPassword;


    public function __construct($privateKeyContent, $privateKeyPassword = null)
    {
        $this->privateKeyContent = $privateKeyContent;
        $this->privateKeyPassword = $privateKeyPassword;
    }

    /**
     * @return KeyExchange
     */
    public function generateKeyExchange()
    {
        $diffieHellman = new DiffieHellman($this->getPrime(), $this->getGenerator());
        $diffieHellman->generateKeys();
        $serverPublicKey = base64_encode($diffieHellman->getPublicKey(DiffieHellman::FORMAT_BINARY));
        $serverPrivateKey = base64_encode($diffieHellman->getPrivateKey(DiffieHellman::FORMAT_BINARY));

        $keyExchange = new KeyExchange();
        $keyExchange->setPrivateParameters(array('private_key' => $serverPrivateKey));
        $keyExchange->setPublicParameters(array('public_key' => $serverPublicKey));
        $keyExchange->setType($this->getKeyExchangeType());

        return $keyExchange;
    }

    /**
     * @param array   $keyExchangeParameters
     * @param array   $additionalParameters
     * @param integer $sharedKeyLength
     *
     * @throws \InvalidArgumentException
     * @return string binary shared key for symmetric algorithm
     */
    public function generateCommonKey(array $keyExchangeParameters, array $additionalParameters, $sharedKeyLength)
    {
        if (!isset($keyExchangeParameters['private_key'])) {
            throw new \InvalidArgumentException('Parameter private_key is missing');
        }
        if (!isset($additionalParameters['encrypted_public_key'])) {
            throw new \InvalidArgumentException('Parameter encrypted_public_key is missing');
        }

        $encryptedClientPublicKey = base64_decode($additionalParameters['encrypted_public_key']);
        openssl_private_decrypt($encryptedClientPublicKey, $clientPublicKey, $this->getCertificatePrivateKey());

        $generatedPrivateKey = base64_decode($keyExchangeParameters['private_key']);

        $diffieHellman = new DiffieHellman($this->getPrime(), $this->getGenerator());
        $diffieHellman->setPrivateKey($generatedPrivateKey, DiffieHellman::FORMAT_BINARY);
        $diffieHellman->generateKeys();
        $secretKey = $diffieHellman->computeSecretKey(
            $clientPublicKey,
            DiffieHellman::FORMAT_BINARY,
            DiffieHellman::FORMAT_BINARY
        );

        return substr(hash('sha256', $secretKey, true), 0, $sharedKeyLength);
    }

    protected function getCertificatePrivateKey()
    {
        if ($this->privateKeyResource === null) {
            $this->privateKeyResource = openssl_pkey_get_private($this->privateKeyContent, $this->privateKeyPassword);
        }
        return $this->privateKeyResource;
    }

    /**
     * @return string big decimal
     */
    protected abstract function getPrime();

    /**
     * @return string big decimal
     */
    protected abstract function getGenerator();

    /**
     * @return string
     */
    protected abstract function getKeyExchangeType();
}