<?php

namespace Infogold\KlienciBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Infogold\KlienciBundle\Entity\RodzajeKontaktow;
use Infogold\KlienciBundle\Form\RodzajeKontaktowType;

/**
 * RodzajeKontaktow controller.
 *
 */
class RodzajeKontaktowController extends Controller
{
    /**
     * Lists all RodzajeKontaktow entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('my.main.admin')->getMainAdmin();
        $rodzajeKontaktows = $em->getRepository('InfogoldKlienciBundle:RodzajeKontaktow')->findBy(array('company' => $user->getId()));

        return $this->render('InfogoldAccountBundle:Ustawienia:index_contacts_types.html.twig', array(
            'rodzajeKontaktows' => $rodzajeKontaktows,
        ));
    }

    /**
     * Creates a new RodzajeKontaktow entity.
     *
     */
    public function newAction(Request $request)
    {
        $rodzajeKontaktow = new RodzajeKontaktow();
        $form = $this->createForm('Infogold\KlienciBundle\Form\RodzajeKontaktowType', $rodzajeKontaktow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('my.main.admin')->getMainAdmin();
            $rodzajeKontaktow->setCompany($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rodzajeKontaktow);
            $em->flush();

             return $this->redirectToRoute('rodzajekontaktow_index');
          //  return $this->redirectToRoute('rodzajekontaktow_show', array('id' => $rodzajeKontaktow->getId()));
        }

        return $this->render('InfogoldAccountBundle:Ustawienia:new_contacts_types.html.twig', array(
            'rodzajeKontaktow' => $rodzajeKontaktow,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RodzajeKontaktow entity.
     *
     */
    /*
    public function showAction(RodzajeKontaktow $rodzajeKontaktow)
    {
        $deleteForm = $this->createDeleteForm($rodzajeKontaktow);

        return $this->render('InfogoldAccountBundle:Ustawienia:show_contacts_types.html.twig', array(
            'rodzajeKontaktow' => $rodzajeKontaktow,
            'delete_form' => $deleteForm->createView(),
        ));
    }
*/
    /**
     * Displays a form to edit an existing RodzajeKontaktow entity.
     *
     */
    public function editAction(Request $request, RodzajeKontaktow $rodzajeKontaktow)
    {
        $deleteForm = $this->createDeleteForm($rodzajeKontaktow);
        $editForm = $this->createForm('Infogold\KlienciBundle\Form\RodzajeKontaktowType', $rodzajeKontaktow);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rodzajeKontaktow);
            $em->flush();

            return $this->redirectToRoute('rodzajekontaktow_edit', array('id' => $rodzajeKontaktow->getId()));
        }

        return $this->render('InfogoldAccountBundle:Ustawienia:edit_contacts_types.html.twig', array(
            'rodzajeKontaktow' => $rodzajeKontaktow,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a RodzajeKontaktow entity.
     *
     */
    public function deleteAction(Request $request, RodzajeKontaktow $rodzajeKontaktow)
    {
        $form = $this->createDeleteForm($rodzajeKontaktow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rodzajeKontaktow);
            $em->flush();
        }

        return $this->redirectToRoute('rodzajekontaktow_index');
    }

    /**
     * Creates a form to delete a RodzajeKontaktow entity.
     *
     * @param RodzajeKontaktow $rodzajeKontaktow The RodzajeKontaktow entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RodzajeKontaktow $rodzajeKontaktow)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rodzajekontaktow_delete', array('id' => $rodzajeKontaktow->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
