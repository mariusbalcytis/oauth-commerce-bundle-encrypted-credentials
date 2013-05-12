<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\TokenProvider;

use Doctrine\ORM\EntityManager;
use Maba\Bundle\OAuthCommerceCommonBundle\Entity\AccessToken;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Certificate\CertificateProviderInterface;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\CredentialsHandler\CredentialsHandlerInterface;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Decrypting\DecryptingInterface;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Cipher;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\LocalSession;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Session;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidCredentialsException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidScopeException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\KeyExchange\KeyExchangeInterface;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Manager\TokenProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class LocalTokenProvider implements TokenProviderInterface
{
    /**
     * @var CredentialsHandlerInterface
     */
    protected $credentialsHandler;

    /**
     * @var CertificateProviderInterface
     */
    protected $certificateProvider;

    /**
     * @var KeyExchangeInterface
     */
    protected $keyExchange;

    /**
     * @var DecryptingInterface
     */
    protected $decrypting;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $expirationInterval;


    public function __construct(
        CertificateProviderInterface $certificateProvider,
        CredentialsHandlerInterface $credentialsHandler,
        DecryptingInterface $decrypting,
        KeyExchangeInterface $keyExchange,
        $key,
        $expirationInterval = 'P2D'
    ) {
        $this->certificateProvider = $certificateProvider;
        $this->credentialsHandler = $credentialsHandler;
        $this->decrypting = $decrypting;
        $this->expirationInterval = $expirationInterval;
        $this->key = $key;
        $this->keyExchange = $keyExchange;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string       $credentialsType
     * @param ParameterBag $parameters
     *
     * @return bool
     */
    public function supportsSession($credentialsType, ParameterBag $parameters)
    {
        return $this->credentialsHandler->supportsSession($credentialsType, $parameters);
    }

    /**
     * @param string $credentialsType
     * @param array  $parameters
     *
     * @return Session
     */
    public function createSession($credentialsType, array $parameters)
    {
        $session = new Session();
        $session->setCertificate($this->certificateProvider->getCertificate());
        $session->setKeyExchange($this->keyExchange->generateKeyExchange());
        $cipher = new Cipher();
        $iv = base64_encode($this->decrypting->generateInitializationVector());
        $cipher->setType($this->decrypting->getType())->setIv($iv);
        $session->setCipher($cipher);

        $sessionId = $this->saveSession($credentialsType, $parameters, $session);

        $session->setSessionId($sessionId);

        return $session;
    }

    /**
     * Returns unique key for token provider in current system. Used for identifying provider in database
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string   $sessionId
     * @param string   $encryptedCredentials
     * @param string[] $scopes
     * @param array    $additionalParameters
     *
     * @return AccessToken
     * @throws InvalidCredentialsException
     * @throws InvalidScopeException
     */
    public function createToken($sessionId, $encryptedCredentials, array $scopes, array $additionalParameters)
    {
        $localSession = $this->loadSession($sessionId);

        $commonKey = $this->keyExchange->generateCommonKey(
            $localSession->getKeyExchangeParameters(),
            $additionalParameters,
            $this->decrypting->getKeyLength()
        );
        $iv = base64_decode($localSession->getIv());

        $decrypted = $this->decrypting->decrypt($encryptedCredentials, $iv, $commonKey);

        parse_str($decrypted, $privateParameters);
        $credentials = new ParameterBag($localSession->getPublicParameters() + $privateParameters);

        $userId = $this->credentialsHandler->handleCredentials($credentials, new ParameterBag($scopes));

        $accessToken = new AccessToken();
        $accessToken->setScopes($scopes);
        $accessToken->setUserId($userId);
        $expiresAt = new \DateTime();
        $expiresAt = $expiresAt->add(new \DateInterval($this->expirationInterval));
        $accessToken->setExpires($expiresAt);

        return $accessToken;
    }


    /**
     * @param string  $credentialsType
     * @param array   $parameters
     * @param Session $session
     *
     * @return int
     */
    protected function saveSession($credentialsType, array $parameters, Session $session)
    {
        $localSession = new LocalSession();
        $localSession
            ->setCredentialsType($credentialsType)
            ->setPublicParameters($parameters)
            ->setIv($session->getCipher()->getIv())
            ->setKeyExchangeParameters($session->getKeyExchange()->getParameters())
        ;

        $this->entityManager->persist($localSession);
        $this->entityManager->flush();

        return $localSession->getId();
    }

    /**
     * @param int $sessionId
     *
     * @return LocalSession
     */
    protected function loadSession($sessionId)
    {
        return $this->entityManager->find(LocalSession::CLASS_NAME, $sessionId);
    }

}