<?php
 // src/Infogold/KonsultantBundle/Controller/SecurityController.php
 
namespace Infogold\KonsultantBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Infogold\KonsultantBundle\Form\ChangePasswordType;

 
class SecurityController extends Controller 
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        

        return $this->render(
            'InfogoldKonsultantBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                
            )
        );
    }

   
    public function ChangePasswordAction($id) {
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);
        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Konsultant entity.');
        }
        $passwordForm = $this->createForm(new ChangePasswordType(), $entities);
        return $this->render('InfogoldKonsultantBundle:Konsultant:password.html.twig', array(
                    'entity' => $entities,
                    'password_form' => $passwordForm->createView(),
        ));
    }
    public function UpdatePasswordAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);
        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Konsultant entity.');
        }
        
        $starehaslo = $entities->getOldPassword();
        $username = $entities->getUsername();
        $email = $entities->getEmail();
        $passwordForm = $this->createForm(new ChangePasswordType(), $entities);
        $passwordForm->bind($request);
        
        $passold = $entities->getOldPassword();
        $pass = $entities->getPassword();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($entities);
        $password = $encoder->encodePassword($pass, $entities->getSalt());
        $starekonsultanta = $encoder->encodePassword($passold, $entities->getSalt());
      
        $entities->setPassword($password);
        $entities->setOldpassword($entities->getPassword());
        $entities->setIsActive(true);
        $entities->setUsername($username);
        $entities->setEmail($email);
        
        if ($starehaslo == $starekonsultanta) {
            if ($passwordForm->isValid()) {
                $em->persist($entities);
                $em->flush();
                return $this->forward('InfogoldKonsultantBundle:Security:login', array(
                            $this->get('session')->getFlashBag()->add('notice', 'Hasło zostało zmienione, proszę się zalogować')
                ));
            }
        } else {
            return $this->forward('InfogoldKonsultantBundle:Security:ChangePassword', array(
                        'id' => $id,
                        $this->get('session')->getFlashBag()->add('error', 'Stare hasło jest niepoprawne')
                            )
            );
        }
        return $this->render('InfogoldKonsultantBundle:Konsultant:password.html.twig', array(
                    'entity' => $entities,
                    'password_form' => $passwordForm->createView(),
        ));
    }
    
    
}
