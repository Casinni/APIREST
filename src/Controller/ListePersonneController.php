<?php

namespace App\Controller;
use App\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ListePersonneController extends AbstractController
{
    /**
     * @Route("/liste", name="liste",methods={"GET"})
     */
    public function liste(Request $request, ManagerRegistry $doctrine): JsonResponse
    {//recuperation du repository grace au manager
        $em=$doctrine->getManager();
        $personneRepository=$em->getRepository(Personne::class);
    //personneRepository herite de servciceEntityRepository ayant les methodes pour recuperer les données de la bdd
        $listePersonnes=$personneRepository->findAll();
        $resultat=[];
		foreach($listePersonnes as $pers){
			array_push($resultat,$pers->getNom());
		}
		$reponse=new JsonResponse($resultat);
		
		
        return $reponse;
    }
     /**
     * @Route("/insert", name="insert_personne", methods={"POST"})
     */
    public function insertPersonne(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        if ($request->isMethod('POST')) {
            // Get Entity Manager
            $em = $doctrine->getManager();
            $personne = new Personne();
           //get value from _POST

           if(!empty($request->query->get('nom')) && $request->query->get('nom')!=null){
            $personne->setNom($request->query->get('nom'));
        }
            $personne->setPrenom($request->query->get('prenom'));
            $datenaiss=$request->query->get('date_naiss');
                $datetime = new \DateTime($datenaiss);
                $personne->setDateNaiss($datetime);
            
            $em->persist($personne);
            //insertion en bdd
            $em->flush();
            $resultat = ["ok"];
        } else {
            $resultat = ["nok"];
        }

        // Send JSON response
        return new JsonResponse($resultat);
    }

 /**
     * @Route("/personne/{id}",name="personne_single",methods={"GET"},requirements={"nom"="[0-9]{1,30}"})
     */
    public function getFromId(Request $request, $id, ManagerRegistry $doctrine): JsonResponse
    {
        if ($request->isMethod('GET')) {
            // Get Entity Manager
            $em = $doctrine->getManager();
            $personne = $em->getRepository(Personne::class)->find($id);
            $resultat = [
                "id" => $personne->getId(),
                "nom" => $personne->getNom(),
                "prenom" => $personne->getPrenom(),
                "date_naiss" => $personne->getDateNaiss()->format("d-m-Y") ?? "",
            ];
        } else {
            $resultat = ["nok"];
        }

        // Send JSON response
        return new JsonResponse($resultat);
    }
    /**
     * @Route("/delete", name="personne_delete",methods={"DELETE"} )
     */
    public function delete(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        if ($request->isMethod('DELETE')) {
            // Get Entity Manager
            $em = $doctrine->getManager();
            // Getid
            $id = $request->query->get('id');
            $personne = $em->getRepository(Personne::class)->find($id);
            $em->remove($personne);
            $em->flush();
            $resultat = ["ok"];
        } else {
            $resultat = ["nok"];
        }

        // Send JSON response
        return new JsonResponse($resultat);
    }

  /**
     * @Route("/update", name="UPDATE_personne", methods={"PUT"})
     */
    public function updatePersonne(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        if ($request->isMethod('PUT')) {
            // Get Entity Manager
            $em = $doctrine->getManager();
          
           //get value from 
           $id=$request->query->get('id');
           $em = $doctrine->getManager();
           $personne = $em->getRepository(Personne::class)->find($id);
         
            $personne->setNom($request->query->get('nom'));
            $personne->setPrenom($request->query->get('prenom'));
            $datenaiss=$request->query->get('date_naiss');
            $datetime = new \DateTime($datenaiss);
            $personne->setDateNaiss($datetime);
            
            $em->persist($personne);
            //insertion en bdd
            $em->flush();
            $resultat = ["ok"];
        } else {
            $resultat = ["nok"];
        }

        // Send JSON response
        return new JsonResponse($resultat);
    }

}
