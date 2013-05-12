<?php


namespace Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Certificate;

use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Entity\Certificate;
use Maba\Bundle\OAuthCommerceEncryptedCredentialsBundle\Hash\HasherInterface;
use Symfony\Component\Routing\RouterInterface;

class LocalCertificateProvider implements CertificateProviderInterface
{
    protected $hasher;
    protected $router;
    protected $route;
    protected $certificateContent;

    protected $certificate;


    public function __construct(RouterInterface $router, $route)
    {
        $this->router = $router;
        $this->route = $route;
    }

    public function setCertificateContent($certificateContent)
    {
        $this->certificateContent = $certificateContent;

        return $this;
    }

    public function setHasher($hasher)
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * @return Certificate
     */
    public function getCertificate()
    {
        if ($this->certificate === null) {
            $this->certificate = new Certificate();
            $this->certificate->setHash($this->hasher->hash($this->certificateContent))
                ->setHashType($this->hasher->getType())
                ->setUrl($this->router->generate($this->route, array(), RouterInterface::ABSOLUTE_URL))
            ;
        }

        return $this->certificate;
    }

}