<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{

    public function certificateAction()
    {
        return new Response(
            $this->container->getParameter('maba_oauth_commerce_encrypted_credentials.certificate_content'),
            200,
            array('Content-Type' => 'text')
        );
    }
}