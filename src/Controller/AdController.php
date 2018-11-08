<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Ad::class);

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


    /**
     * Permet de créer une annonce
     *
     * @Route("ads/new", name="ads_create")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Reponse
     */
    /*public function create() 
    {
        $ad = new Ad();

        $form = $this->createFormBuilder($ad)
                ->add('title')
                ->add('introduction')
                ->add('content')
                ->add('rooms')
                ->add('price')
                ->add('coverImage')
                ->getForm();

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }*/
    public function create(Request $request, ObjectManager $manager) 
    {
        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // $manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', 'Votre nouvelle annonce est bien enregistrée !');

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affiche le form d'édition
     *
     * @Route("ads/{slug}/edit", name="ads_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce appartient à un autre utilisateur")
     * 
     * @return Response
     */
    public function edit(Ad $ad = null, Request $request, ObjectManager $manager) 
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){        
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', 'Votre annonce a bien été modifiée !');

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad   
        ]);
    }
    
    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    /*public function show($slug, AdRepository $repo) 
    {
        $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }*/
    /**
     * @Route("/ads/{slug}", name="ads_show")     
     */
    public function show(Ad $ad = null) 
    {
        if ($ad == null) {            
            return $this->redirectToRoute('ads_index');
        }

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }   
    
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("ads/{slug}/delete", name="ads_delete")
     *
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Pas le droite d'accéder à cette ressource")
     * 
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager) 
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', 'Annonce supprimée !');

        return $this->redirectToRoute("ads_index");
    }

}