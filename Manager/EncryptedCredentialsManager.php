<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Session;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\SessionRelation;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\ProviderNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EncryptedCredentialsManager
{
    /**
     * @var TokenProviderInterface[]
     */
    protected $tokenProviders = array();

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TokenProviderInterface $tokenProvider
     */
    public function addTokenProvider(TokenProviderInterface $tokenProvider)
    {
        $this->tokenProviders[$tokenProvider->getKey()] = $tokenProvider;
    }

    /**
     * @param string       $credentialsType
     * @param ParameterBag $parameters
     * @param integer      $credentialsId
     *
     * @throws \Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\ProviderNotFoundException
     * @return Session
     */
    public function createSession($credentialsType, ParameterBag $parameters, $credentialsId)
    {
        foreach ($this->tokenProviders as $tokenProvider) {
            if ($tokenProvider->supportsSession($credentialsType, $parameters)) {
                $session = $tokenProvider->createSession($credentialsType, $parameters->all());
                $session->setSessionId(
                    $this->relateSession($tokenProvider->getKey(), $session->getSessionId(), $credentialsId)
                );
                return $session;
            }
        }
        throw new ProviderNotFoundException('No provider found for credentials type and parameters');
    }

    /**
     * @param string       $sessionId
     * @param string       $encryptedCredentials
     * @param array        $scopes
     * @param ParameterBag $params
     * @param int          $credentialsId
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\ProviderNotFoundException
     * @return \Maba\Bundle\OAuthCommerceCommonBundle\Entity\AccessToken
     */
    public function createToken($sessionId, $encryptedCredentials, array $scopes, ParameterBag $params, $credentialsId)
    {
        /** @var SessionRelation $sessionRelation */
        $sessionRelation = $this->entityManager->find(SessionRelation::CLASS_NAME, $sessionId);
        if (!$sessionRelation) {
            throw new ProviderNotFoundException('Session with such ID not found');
        } elseif ($sessionRelation->getCredentialsId() !== $credentialsId) {
            throw new AccessDeniedException('This session is related to another client');
        }
        $providerKey = $sessionRelation->getProviderKey();
        if (!isset($this->tokenProviders[$providerKey])) {
            throw new ProviderNotFoundException('Provider with this key is no longer active');
        }

        return $this->tokenProviders[$providerKey]
            ->createToken($sessionRelation->getProviderSessionId(), $encryptedCredentials, $scopes, $params->all())
            ->setCredentialsId($credentialsId);
    }

    /**
     * @param string $providerKey
     * @param string $internalSessionId
     * @param int    $credentialsId
     *
     * @return string
     */
    protected function relateSession($providerKey, $internalSessionId, $credentialsId)
    {
        $sessionRelation = new SessionRelation();
        $sessionRelation->setProviderKey($providerKey);
        $sessionRelation->setProviderSessionId($internalSessionId);
        $sessionRelation->setCredentialsId($credentialsId);
        $this->entityManager->persist($sessionRelation);
        $this->entityManager->flush();
        return $sessionRelation->getId();
    }
}