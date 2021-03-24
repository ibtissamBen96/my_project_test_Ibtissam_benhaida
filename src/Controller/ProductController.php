<?php

namespace App\Controller;
use App\Entity\Product;

use App\Repository\ProductRepository;
use App\Form\ProductType;
use App\Controller\Catgorie;
use App\Service\ProductFormHandler;

use App\Service\CalculPrixTTC;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\HttpFoundation\File\Exception\FileException;


use Symfony\Component\Form\FormFactoryInterface;


class ProductController extends AbstractController
{

    private $productRepository;
    private $fromFactory;
    private $productFormHandler;

    public function __construct(ProductRepository $productRepository,
    ProductFormHandler $productFormHandler ,
    FormFactoryInterface $fromFactory )
    {
        $this->productRepository = $productRepository;

        $this->productFormHandler = $productFormHandler;

    }


    /**
     * @Route("/product", name="product")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();


        return $this->render('product/product.html.twig',[
            'products'=>$products
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="delete_product")
     */
   
    public function deleteproduct($id,Request $request){


        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
            $this->productRepository->deleteproduct($product);       
            $this->addFlash('success', 'produit bien supprimer');
            
        }
        return $this->redirectToRoute('product');
    }




     /**
     * @Route("/product/add", name="create_product")
     */

    public function new(Request $request): Response
    {
        $product = new Product();
  
        $form = $this->createForm(ProductType::class,$product,['required_ttc'=> false]);
        
            $form->handleRequest($request);

            if ($this->productFormHandler->handle($request,$form)) {
                $this->addFlash('success', 'produit bien ajouter');
                return $this->redirectToRoute('product');
            }
            
            $errors=$form->getErrors(true);
            return $this->render('product/add.html.twig',
            ['form' => $form->createView(),
            'errors' =>$errors
            
            ]);
    }



 /**
     * @Route("/product/update/{id}", name="update_product")
     */

    public function update(Request $request,Product $product )
    {


       // chercher product
       // $product = $this->getDoctrine()->getRepository(Product::class)->find($id);  

          // modifier product
        

            $form = $this->createForm(ProductType::class,$product,[
                'required_ttc'=> false
            ]);

         
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $product=$form->getData();
                $product->setCreateAt(new \DateTime());
                $TTC = $form['ttc']->getData();
                
                if($TTC === FALSE){
                    $product->setPrix($product->getPrix() + ($product->getPrix() * 0.2));
                }
                
               $this->productRepository->updateproduct($product);

                $this->addFlash('success', 'produit bien modifier');
                return $this->redirectToRoute('product');
            }
            
            return $this->render('product/add.html.twig',
            ['form' => $form->createView()]);

    }    


}