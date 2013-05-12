<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Decrypting;

use Zend\Crypt\Symmetric\Mcrypt;
use Zend\Math\Rand;

class MCryptDecrypting implements DecryptingInterface
{
    /**
     * @var \Zend\Crypt\Symmetric\Mcrypt
     */
    protected $crypt;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $algorithm
     * @param string $type
     */
    public function __construct($algorithm, $type)
    {
        $this->crypt = new Mcrypt();
        $this->crypt->setAlgorithm($algorithm);
        $this->crypt->setMode('cbc');
        $this->type = $type;
    }

    /**
     * @param string $data
     * @param string $iv
     * @param string $key
     *
     * @return string
     */
    public function decrypt($data, $iv, $key)
    {
        $this->crypt->setKey($key);
        $this->crypt->setSalt($iv);
        $decrypted = $this->crypt->decrypt($iv . $data);
        return $decrypted;
    }

    /**
     * @return string binary string for IV to use with this encrypting type
     */
    public function generateInitializationVector()
    {
        return Rand::getBytes($this->crypt->getSaltSize(), true);
    }

    /**
     * @return integer
     */
    public function getKeyLength()
    {
        return $this->crypt->getKeySize();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


}