<?php

namespace Infogold\KonsultantBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\RouterInterface;
use Infogold\KonsultantBundle\Entity\CzasPracy;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Doctrine\ORM\EntityManager;

/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface {

    protected $router;
    protected $kernel;
    protected $er;
    protected $em;

    public function __construct(RouterInterface $router, HttpKernelInterface $kernel, SecurityContext $er, EntityManager $em) {
        $this->router = $router;
        $this->kernel = $kernel;
        $this->er = $er;
        $this->em = $em;
    }

    function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $czas = new \DateTime('now');

        $waznoschasla = $token->getUser()->getUpdatePassword()->modify('+25 day');
        $aktywny = $token->getUser()->getisActive();

        if ($czas > $waznoschasla || $aktywny == "nie") {
            $koment = $aktywny == "nie" ? 'Ustal nowe hasło' : 'Twoje hasło wygasło, ustal nowe';
            $controller = 'InfogoldKonsultantBundle:Security:ChangePassword';
            $path = array(
                'id' => $token->getUser()->getId(),
                '_controller' => $controller,
                $request->getSession()->getFlashBag()->add('notice', $koment)
            );
            $subRequest = $request->duplicate(array(), null, $path);

            $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            $anomus = new AnonymousToken(null, 'anon.');

            $this->er->setToken($anomus);
            // $request->setSession(null);
            return $response;
        } else {
           
            $czaszalogowania = new CzasPracy();
            $czaszalogowania->setZalogowanie($czas);
            $czaszalogowania->setWylogowanie(null);
            $czaszalogowania->setKonsultantaCzasy($token->getUser());
            $this->em->persist($czaszalogowania);
            $this->em->flush();
            
            //dodanie czasu zalogowania się
            $uri = $this->router->generate('klienci');
            return new RedirectResponse($uri);
        }
        $uri = $this->router->generate('security_login');
        return new RedirectResponse($uri);
    }

    public function onLogoutSuccess(Request $request) {

        if($this->er->getToken() != NULL){
            
        $czas = new \DateTime('now');
        $repository = $this->em->getRepository('InfogoldKonsultantBundle:CzasPracy');
        $q = $repository->createQueryBuilder('p')
                ->select('MAX(p.id)')            
                ->leftJoin('p.KonsultantaCzasy', 'c')
                ->andwhere('c.id= :konsultantid')              
                ->setParameter('konsultantid', $this->er->getToken()->getUser()->getId())
                ->getQuery();

        $konsultant = $q->getSingleScalarResult();
             
        if ($konsultant) {
           $timeend = $repository->find($konsultant);
           $timeend->setWylogowanie($czas);
           $this->em->flush();
        }

        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);
        }else{

            $referer = $request->headers->get('referer');
             return new RedirectResponse($referer);
        }
    }

}
