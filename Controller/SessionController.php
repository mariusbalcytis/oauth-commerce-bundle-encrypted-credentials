<?php

namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Controller;

use Maba\Bundle\OAuthCommerceCommonBundle\Response\OAuthAccessTokenResponse;
use Maba\Bundle\OAuthCommerceCommonBundle\Security\OAuthCredentialsToken;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidCredentialsException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Exception\InvalidScopeException;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Manager\EncryptedCredentialsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Serializer\Serializer;

class SessionController extends Controller
{

    public function sessionAction()
    {
        $credentialsId = $this->getSecurityToken()->getCredentialsId();
        $parameterBag = new ParameterBag($this->getRequest()->request->all());
        $credentialsType = $parameterBag->get('credentials_type');
        $parameterBag->remove('credentials_type');
        $session = $this->getEncryptedCredentialsManager()
            ->createSession($credentialsType, $parameterBag, $credentialsId);

        return new JsonResponse(array(
            'certificate' => array(
                'url' => $session->getCertificate()->getUrl(),
                'hash_type' => $session->getCertificate()->getHashType(),
                'hash' => $session->getCertificate()->getHash(),
            ),
            'key_exchange' => array(
                'type' => $session->getKeyExchange()->getType(),
                'parameters' => $session->getKeyExchange()->getPublicParameters(),
            ),
            'cipher' => array(
                'type' => $session->getCipher()->getType(),
                'iv' => $session->getCipher()->getIv(),
            ),
            'session_id' => $session->getSessionId(),
        ));
    }

    public function tokenAction()
    {
        $credentialsId = $this->getSecurityToken()->getCredentialsId();
        $parameterBag = new ParameterBag($this->getRequest()->request->all());
        if ($parameterBag->get('grant_type') !== 'urn:marius-balcytis:oauth:grant-type:encrypted-credentials') {
            return new JsonResponse(array('error' => 'unsupported_grant_type'), 400);
        }
        $scopes = array_unique(explode(' ', $parameterBag->get('scope')));
        $sessionId = $parameterBag->get('session_id');
        $encrypted = $parameterBag->get('encrypted_credentials');

        $parameterBag->remove('grant_type');
        $parameterBag->remove('scope');
        $parameterBag->remove('session_id');
        $parameterBag->remove('encrypted_credentials');

        try {
            $accessToken = $this->getEncryptedCredentialsManager()
                ->createToken($sessionId, $encrypted, $scopes, $parameterBag, $credentialsId);
        } catch (InvalidCredentialsException $exception) {
            return new JsonResponse(array(
                'error' => 'invalid_credentials',
                'error_description' => $exception->getMessage(),
            ), 400);
        } catch (InvalidScopeException $exception) {
            return new JsonResponse(array(
                'error' => 'invalid_scope',
                'error_description' => $exception->getMessage(),
            ), 400);
        }


        return new OAuthAccessTokenResponse($accessToken);
    }

    /**
     * @return EncryptedCredentialsManager
     */
    protected function getEncryptedCredentialsManager()
    {
        return $this->get('maba_oauth_commerce_encrypted_credentials.manager');
    }

    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return OAuthCredentialsToken
     */
    protected function getSecurityToken()
    {
        /** @var SecurityContextInterface $securityContext */
        $securityContext = $this->get('security.context');
        $token = $securityContext->getToken();
        if ($token instanceof OAuthCredentialsToken) {
            return $token;
        } else {
            throw new AccessDeniedException('No oauth credentials found in security context');
        }
    }
}
