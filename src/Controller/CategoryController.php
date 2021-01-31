<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CreateCategoryType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Categories::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/create", name="category-create")
     */
    public function create(Request $request)
    {
        $category = new Categories();

        $form = $this->createForm(CreateCategoryType::class, $category);
        $category->setActive('1');
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            
            

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Categoría registrada correctamente');

            return $this->redirectToRoute('category');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="category-edit")
     */
    public function update(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Categories::class)->find($id);

        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $category->setActive('1');

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Categoría actualizada correctamente');

            return $this->redirectToRoute('category');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category-edit")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Categories::class)->find($id);

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Categoría eliminada correctamente');

        return $this->redirectToRoute('category');
    }

    /**
     * @Route("/category/status/{id}", name="category-edit")
     */
    public function status($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Categories::class)->find($id);
        
        if($category->getActive() == '1')
        {
            $category->setActive('0');
        }else{
            $category->setActive('1');
        }

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'El estado de la categoría cambió correctamente');

        return $this->redirectToRoute('category');
    }
}
