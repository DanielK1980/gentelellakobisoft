<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\AccountBundle\Entity\Category;
use Infogold\AccountBundle\Form\CategoryType;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller {

    /**
     * Lists all Category entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('my.main.admin')->getMainAdmin();
        $categories = $em->getRepository('InfogoldAccountBundle:Category')->findBy(array('company' => $user->getId()));

        return $this->render('InfogoldAccountBundle:category:index.html.twig', array(
                    'categories' => $categories,
        ));
    }

    protected function user() {

        $user = $this->get('security.context')->getToken()->getUser();
        $nrklienta = $user->getNrklienta();

        $em2 = $this->getDoctrine()->getManager();

        $kat = $em2->getRepository('InfogoldUserBundle:User');

        $qbkat = $kat->createQueryBuilder('p');
        $query2 = $qbkat
                ->select('p')
                ->where('p.Nrklienta=' . $nrklienta)
                ->andWhere('p.locked=false')
                ->andWhere('p.roles LIKE :role')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->add('orderBy', 'p.id DESC')
                ->getQuery();
        
        $useradmin = $query2->getOneOrNullResult();

        return $useradmin;
    }

    /**
     * Creates a new Category entity.
     *
     */
    public function newAction(Request $request) {
        
         $user = $this->get('my.main.admin')->getMainAdmin();
         
        $category = new Category();
        $form = $this->createForm(new CategoryType($user->getNrklienta()), $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setCompany($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_index', array('id' => $category->getId()));
        }

        return $this->render('InfogoldAccountBundle:category:new.html.twig', array(
                    'category' => $category,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     */
    public function showAction(Category $category) {
        $deleteForm = $this->createDeleteForm($category);

        return $this->render('InfogoldAccountBundle:category:show.html.twig', array(
                    'category' => $category,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     */
    public function editAction(Request $request, Category $category) {
        
        $user = $this->get('my.main.admin')->getMainAdmin();
        $deleteForm = $this->createDeleteForm($category);
        $editForm = $this->createForm(new CategoryType($user->getNrklienta(),true), $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_edit', array('id' => $category->getId()));
        }

        return $this->render('InfogoldAccountBundle:category:edit.html.twig', array(
                    'category' => $category,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     *
     */
    public function deleteAction(Request $request, Category $category) {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Creates a form to delete a Category entity.
     *
     * @param Category $category The Category entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $category) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('category_delete', array('id' => $category->getId())))
                        ->setMethod('DELETE')                      
                        ->getForm()
        ;
    }

}
