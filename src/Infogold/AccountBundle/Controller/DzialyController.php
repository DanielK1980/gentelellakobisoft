<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\AccountBundle\Entity\Dzialy;
use Infogold\AccountBundle\Form\DzialyType;

/**
 * Dzialy controller.
 *
 */
class DzialyController extends Controller {

    /**
     * Lists all Dzialy entities.
     *
     */
    public function indexAction() {
        $User = $this->get('my.main.admin')->getMainAdmin();
        
        $userId = $User->getNrklienta();
        
        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldUserBundle:User');

            $qb = $req->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->where('p.Nrklienta=' . $userId)                
                    ->andWhere('p.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->getQuery();
        
            $loggedUser = $query->getSingleResult();
        
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InfogoldAccountBundle:Dzialy')->findBy(
                array('DzialyFirmy' => $loggedUser->getId())
        );

        return $this->render('InfogoldAccountBundle:Dzialy:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Dzialy entity.
     *
     */
    public function createAction(Request $request) {
        
        $User = $this->get('my.main.admin')->getMainAdmin();
        
        $userId = $User->getNrklienta();
        
        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldUserBundle:User');

            $qb = $req->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->where('p.Nrklienta=' . $userId)                
                    ->andWhere('p.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->getQuery();
        
            $loggedUser = $query->getSingleResult();
            
        $entity = new Dzialy();
        
        $entity->setDzialyFirmy($loggedUser);
        
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('dzialy_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldAccountBundle:Dzialy:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Dzialy entity.
     *
     * @param Dzialy $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Dzialy $entity) {
        $form = $this->createForm(new DzialyType(), $entity, array(
            'action' => $this->generateUrl('dzialy_create'),
            'method' => 'POST',
        ));      

        return $form;
    }

    /**
     * Displays a form to create a new Dzialy entity.
     *
     */
    public function newAction() {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
    
        $em = $this->getDoctrine()->getManager();
        
         $req = $em->getRepository('InfogoldAccountBundle:Dzialy');

            $qb = $req->createQueryBuilder('p');

            $query2 = $qb
                    ->select('p')
                    ->leftJoin('p.DzialyFirmy', 'c')
                    ->where('c.Nrklienta=' . $userId)                 
                    ->andWhere('c.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->add('orderBy', 'p.id DESC')
                    ->getQuery();
        
        
            $entities = $query2->getResult();
        
        
        $ilosc = count($entities);

        if ($ilosc == 5) {
            
            return $this->render('InfogoldAccountBundle:Dzialy:index.html.twig', array(
                        'entities' => $entities,
                        'error' => 'Osiągnąłeś maksymalną ilość działów'
            ));
        }

        $entity = new Dzialy();
        $form = $this->createCreateForm($entity);

        return $this->render('InfogoldAccountBundle:Dzialy:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Dzialy entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);


        $req = $em->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->where('p.KonsultantDzialy=' . $id)
                ->getQuery();

        $konsultanci = $query->getResult();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dzialy entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Dzialy:show.html.twig', array(
                    'konsuktanci' => $konsultanci,
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Dzialy entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dzialy entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Dzialy:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Dzialy entity.
     *
     * @param Dzialy $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Dzialy $entity) {
        $form = $this->createForm(new DzialyType(true), $entity, array(
            'action' => $this->generateUrl('dzialy_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

       

        return $form;
    }

    /**
     * Edits an existing Dzialy entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dzialy entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('dzialy_edit', array('id' => $id)));
        }

        return $this->render('InfogoldAccountBundle:Dzialy:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Dzialy entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Dzialy entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dzialy'));
    }

    /**
     * Creates a form to delete a Dzialy entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('dzialy_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Usuń Dział' , 'attr' => array('class' => 'btn btn-danger' , 'icon' => 'times fa-fw')))
                        ->getForm()
        ;
    }

}
