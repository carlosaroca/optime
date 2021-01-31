<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductType;
use App\Entity\Categories;

use Knp\Component\Pager\PaginatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Products::class)->findByCategoryActive();

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/product/search", name="product-search")
     */
    public function show(PaginatorInterface $paginator, Request $request): Response
    {
        $code = $_GET['code'];

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Products::class)->filterByCode($code);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/product/create", name="product-create")
     */
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Categories::class)->findCategoriesActive();
        
        $product = new Products();

        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Producto registrado correctamente');

            return $this->redirectToRoute('product');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="product-edut")
     */
    public function update(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Categories::class)->findCategoriesActive();
        
        $product = $em->getRepository(Products::class)->find($id);;

        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Producto actualizado correctamente');

            return $this->redirectToRoute('product');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product-delete")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Products::class)->find($id);

        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Producto eliminado correctamente');

        return $this->redirectToRoute('product');
    }

}
