<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\PlanetsController;

class InicioController extends AbstractController
{
   #[Route('/inicio', name: 'inicio')]
    public function index(): Response
    {  
    	$ObjetoPlanetsController=new PlanetsController();
    	$ComprobacionJson=$ObjetoPlanetsController->ComprobarJson();

    	if ($ComprobacionJson=="No")
    	{$Planetas="";}
        elseif ($ComprobacionJson=="si")
        {$PlanetasDevueltos=$ObjetoPlanetsController->VerPlanetas();
         $Planetas=$PlanetasDevueltos["starwars"];
        }
    	
    
        
        $fecha=date('Y');
        return $this->render('inicio/index.html.twig', [
            'fechavista' => $fecha, 
            'planetas'=>$Planetas
        ]);
     }

   

}