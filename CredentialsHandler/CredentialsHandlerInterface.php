<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\CredentialsHandler;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidCredentialsException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidScopeException;
use Symfony\Component\HttpFoundation\ParameterBag;

interface CredentialsHandlerInterface
{
    /**
     * @param string       $credentialsType
     * @param ParameterBag $parameters
     *
     * @return bool
     */
    public function supportsSession($credentialsType, ParameterBag $parameters);

    /**
     * @param ParameterBag $credentials
     * @param ParameterBag $scopes      can modify scopes
     *
     * @return int|null user id
     * @throws InvalidCredentialsException message of this exception is given to the end client
     * @throws InvalidScopeException       message of this exception is given to the end client
     */
    public function handleCredentials(ParameterBag $credentials, ParameterBag $scopes);
}