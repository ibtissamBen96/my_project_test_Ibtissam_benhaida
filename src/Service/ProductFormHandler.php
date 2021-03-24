<?php

namespace App\Service;
use App\Service\CalculPrixTTC;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
class ProductFormHandler
{

    private $calculPrixTTC;
    private $paraB;
    private $entityManager;
    public function __construct(CalculPrixTTC $calculPrixTTC,ParameterBagInterface $paraB,
    EntityManagerInterface $entityManager )
    {
        $this->calculPrixTTC=$calculPrixTTC;
        $this->paraB=$paraB;
        $this->entityManager=$entityManager;

    }

    public function handle(Request $request,$form)
    {   
        if ($form->isSubmitted() && $form->isValid()) {
                
            $product=$form->getData();
            $image = $form['image']->getData();

                if ($image) {
                    $newFilename = uniqid().'.'.$image->guessExtension();
                    $image->move(
                            $this->paraB->get('image_directory'),
                            $newFilename
                    );
                    $product->setImage($newFilename);
                }

           $product->setCreateAt(new \DateTime());

           $TTC = $form['ttc']->getData();
            
            if($TTC === FALSE){
                $product->setPrix($this->calculPrixTTC->calculerPrixTTC($product->getPrix()));
                
            }
        
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return true;
        }
        
        
        
    }
}

?>