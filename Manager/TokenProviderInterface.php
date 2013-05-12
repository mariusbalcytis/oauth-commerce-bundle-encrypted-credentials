<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Manager;

use Maba\Bundle\OAuthCommerceCommonBundle\Entity\AccessToken;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Session;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidCredentialsException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidScopeException;
use Symfony\Component\HttpFoundation\ParameterBag;

interface TokenProviderInterface
{
    /**
     * @param string       $credentialsType
     * @param ParameterBag $parameters
     *
     * @return bool
     */
    public function supportsSession($credentialsType, ParameterBag $parameters);

    /**
     * @param string$credentialsType
     * @param array  $parameters
     *
     * @return Session
     */
    public function createSession($credentialsType, array $parameters);

    /**
     * Returns unique key for token provider in current system. Used for identifying provider in database
     *
     * @return string
     */
    public function getKey();

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
    public function createToken($sessionId, $encryptedCredentials, array $scopes, array $additionalParameters);
}