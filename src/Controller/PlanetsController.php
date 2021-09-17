<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanetsController extends AbstractController
{
    
    public function index($valorplanet): Response
    {   
      /** Cogemos los datos del planeta los
       * concatenamos abrimos y decodificamos el json
       */
      $number_planet=$valorplanet;
      $ruta_api="https://swapi.dev/api/planets/";
      $leer_api=file_get_contents($ruta_api);
      $decodificacionjson=json_decode($leer_api,true);
      
      /**
       * Contammos el total de planetas y cogemos el vector de 
       * resultados que sale de la decodificacion
       */
      $totalplanetas=$decodificacionjson["count"];
      $resultadosjson=$decodificacionjson["results"];
      /**
       * Creamos un metodo para buscar el planeta y luego 
       * pintarlo en la vista
       */
      $PlanetaJson=$this->BusquedaPlaneta($resultadosjson,$number_planet);

        $fecha=date('Y');
        return $this->render('planets/index.html.twig', [
            'fechavista' => $fecha,
            'jsonplanetas'=>$PlanetaJson
        ]);

        
    }


    public function Planet(): Response
    {  
     /*Valores de fecha y url*/
     $host="http://"; $host.=$_SERVER["HTTP_HOST"];
     $url=$_SERVER["REQUEST_URI"];$uri=$_SERVER["REQUEST_URI"];
     $fecha=date('Y'); $urlactual=$host.$url;

     /*valores para el json y apertura de ficehros*/
     $contar_post=count($_POST);
     $rotation_periodaleatorio=rand(1,23);
     $orbital_periodaletatorio=rand(1,500);
     $diameter_aletatorio=rand(10000,50000);
     $starwars_json='./json/starwars.json';

     /*Comprobamos que el json existe en la ruta solicitada*/
     $MetodoComprobarJson=$this->ComprobarJson();
     
    /*
     * Sino existe creamos el fichero json y la variable planetsw la ponemos sin 
     * contenido para que no la pinte por twig
     */
     if($MetodoComprobarJson=="No")
     {  
        $crearjson=fopen($starwars_json, 'w');
        echo '<div align="center">';
        echo "<h3>No hay fichero json de planetas</h3>";
        echo '</div>';
        $PlanetSw="";

     }
     
     elseif($MetodoComprobarJson=="si")
     { 
       /**
        *  Si existe el fichero json comprobamos 
        *  si esta vacío o con contenido
        */
       $EstadoFichero=$this->EstadoJson();

        /**
         * Si el json estavacio despues de medirlo lo creamos con el primer
         * planeta que mandamos por post haciendo el formato json
         * con crearplanetaprimera y luego escribiendolo directamente
         * todo lo que hubiera anteriormente y lo nuevo que se meta
         */
        if($EstadoFichero==0)
        { $crearjson=fopen($starwars_json, 'w');

          /*Devuelve el formato json para escribirlo en el fichero*/
          $MetodoCrearPlaneta=$this->CrearPlanetaPrimera($_POST);

          $contenido_starwars=$MetodoCrearPlaneta;
          $fwrite=fwrite($crearjson,$contenido_starwars);

          /*Comprobamos que el fichero se haya escrito correctamente*/
          
          if ($fwrite === false)
          {echo '<div align="center">';
          echo "<h3>El nuevo contenido no se ha metido correctamente</h3>";
          echo '</div>';
          echo "<br>";}
          $PlanetSw="";
          

        }
        elseif($EstadoFichero!=0)
        { 
          /**
           *Obtenemos mediante Verplanetas los planetas que tenemos en este justo instante
           */  
          $MetodoVerPlanetas=$this->VerPlanetas();
          $PlanetSw=$MetodoVerPlanetas["starwars"];
          $contarplanetas=count($PlanetSw);


          /**
           * si se mandan valores por formulario por Post 
           * devolvemos la cadena en formato json para meterla al archivo
           * y añadirla a lo que hubiese en el Json
           */
          
          if ($_POST)
          {
              $MetodoAnadirPlaneta=$this->AnadirPlaneta($_POST);
              $contenido_starwars=$MetodoAnadirPlaneta;

              $MetodoComprobarPlaneta=$this->ComprobarPlaneta($_POST["id"],$_POST["name"]);

          /**
           * Comprobamos que ni el id ni el nombre esten repetidos
           * mostrando los mensajes de error correspondientes
           */    
              if ($MetodoComprobarPlaneta=="repetidoplanetaid")
              { echo '<div align="center">';
                echo "<h3>El número de planeta está repetido</h3>";
                echo '</div>';
              }
              else
              {  if ($MetodoComprobarPlaneta=="repetidoplanetanombre")
                 {echo '<div align="center">';
                  echo "<h3>El nombre del planeta está repetido</h3>";
                  echo '</div>';}
                  else
                  {  if ($MetodoComprobarPlaneta=="correcto")
                     { /*Escribimos en el json con el modo w*/
                       $crearjson=fopen($starwars_json, 'w');
                       $escritura_anadirjson=fwrite($crearjson,$contenido_starwars);
                          /*comprobamos que se añade el contenido correctamenet*/
                          if ($escritura_anadirjson === false)
                          { echo '<div align="center">';
                        echo "<h3>El contenido no se ha añadido correctamente al json</h3>";
                            echo '</div>';
                          }
                          else{
                            echo '<div align="center">';
                            echo "<h3>El contenido se ha añadido correctamente al json</h3>";
                            echo '</div>';
                          }
                     }//if

                  }//else
              }//else

            }//if post
  
        }//elseif

     }//elseif
        
        return $this->render('planets/planet.html.twig', [
            'fechavista' => $fecha,
            'urlactual'=>$urlactual,
            'uri'=>$uri,
            'rotation_period'=>$rotation_periodaleatorio,
            'orbital_period'=>$orbital_periodaletatorio,
            'diameter'=>$diameter_aletatorio,
            'planetas'=>$PlanetSw

        ]);

    

    	
    }
    /**
     * Buscamos un valor concreto en el json y sacamos un nuevo 
     * vector personalizado para mostrar los datos que se nos indican.
     */
    public function BusquedaPlaneta($jsonplanetas,$numeroplaneta)
    {   
        $vectorformateado=array();
 
        foreach ($jsonplanetas as $clave=>$planetas):
        $posicionexacta=$numeroplaneta-1;
    
        if ($posicionexacta==$clave):
        $contador_peliculas=count($planetas["films"]);
        $vectorformateado["id"]=$numeroplaneta;
        $vectorformateado["name"]=$planetas["name"];
        $vectorformateado["orbital_period"]=$planetas["orbital_period"];
        $vectorformateado["diameter"]=$planetas["diameter"];
        $vectorformateado["films_count"]=$contador_peliculas;
        $vectorformateado["created"]=$planetas["created"];
        $vectorformateado["edited"]=$planetas["edited"];
        $vectorformateado["url"]=$planetas["url"];
        endif;
        endforeach;

        $JsonPlanetaCodificado=json_encode($vectorformateado,JSON_FORCE_OBJECT);
      

        return $JsonPlanetaCodificado;
    }

    
    /**
     * Se comprueba en el json con el id y el nombre si hay valores repetidos
     * mandando varios mensajes repetidoplanetaid repetidoplanetanombre correcto
     */
    public function ComprobarPlaneta($idplaneta,$nombreplaneta)
    {
        $json_starwars='./json/starwars.json';
        $existir_json_starwars=file_exists($json_starwars);
       
        $leer_starwars=file_get_contents($json_starwars);
        $decodificar_starwars=json_decode($leer_starwars,true);

        $starwars_contain=$decodificar_starwars["starwars"];
    
        $contar=0;
        foreach ($starwars_contain as $sw=>$decsw):
        $comparar_cadenaname=strcmp($decsw["name"],$nombreplaneta);
         
         if ($decsw["id"]==$idplaneta):
         $mensaje="repetidoplanetaid";
         break;
         else:
            if($comparar_cadenaname==0):
            $mensaje="repetidoplanetanombre";
            break;
            else:
            $mensaje="correcto";
            endif;
         endif;

        $contar++;
        endforeach;
        
        $comprobacion=$mensaje;


        return $comprobacion; 
    }

    /*Se comprueba que el archivo json se encuentre y exista en la ruta
     * indicada
     */
    public function ComprobarJson()
    {

    $json_sw='./json/starwars.json';
    $existir_jsonsw=file_exists($json_sw);


        if($existir_jsonsw):
        $estado="si";
        elseif(!$existir_jsonsw):
        $estado="No";    
        endif;

     return $estado;
    }

   /**
    * Abrimos un json como un fichero y lo decodificamos para poderlo
    * trabajar
    */
    public function VerPlanetas()
    {
      $json_sw='./json/starwars.json';
      $leer_sw=file_get_contents($json_sw);
      $decodificar_sw=json_decode($leer_sw,true);

      return $decodificar_sw;
    }
    /**
     * Abriendo el json y devolviendo el contenido en una linea  
     * contamos la longitud de este para saber si está vacío
     */
    public function EstadoJson()
    { 
      $json_sw='./json/starwars.json';
      $leer_sw=file_get_contents($json_sw);
      $longitud_sw=strlen($leer_sw);
      return $longitud_sw;
    }

    /**
    * Creamos la cadena del json para rellenar el archivo por primera vez
    *
    */
     public function CrearPlanetaPrimera($PostPlaneta)
     {
       $starwars_json= './json/starwars.json';


       $contenido_starwars='{';
       $contenido_starwars.='"starwars":[{';
       $contenido_starwars.='"id":"'.$PostPlaneta["id"].'",';
       $contenido_starwars.='"name":"'.$PostPlaneta["name"].'",';
       $contenido_starwars.='"rotation_period":"'.$PostPlaneta["rotation_period"].'",';
       $contenido_starwars.='"orbital_period":"'.$PostPlaneta["orbital_period"].'",';
       $contenido_starwars.='"diameter":"'.$PostPlaneta["diameter"].'"';
       $contenido_starwars.='}'; 
       $contenido_starwars.=']'; 
       $contenido_starwars.='}';
       
      return $contenido_starwars;
    
     }

    /**
     * Añadimos al json leyendo en primer lugar lo que hubiese y metiendo 
     * nuestro contenido nuevo en un un fichero nuevo que se chafa
     */
     public function AnadirPlaneta($PostPlaneta)
     {
        $starwars_json= './json/starwars.json';
        /*leemos primeramente para ver que es lo que hay*/  
        $leerjson_starwars=file_get_contents($starwars_json); 
        $decodificarjson_starwars=json_decode($leerjson_starwars,true);



        $vector_sw=$decodificarjson_starwars["starwars"];
        $contenido_starwars='{';
        $contenido_starwars.='"starwars":[{';

        $totalvector=count($vector_sw);
        $contador=1;

        foreach ($vector_sw as $clave1=>$starwars):

        $contenido_starwars.='"id":"'.$starwars["id"].'",';
        $contenido_starwars.='"name":"'.$starwars["name"].'",';
        $contenido_starwars.='"rotation_period":"'.$starwars["rotation_period"].'",';
        $contenido_starwars.='"orbital_period":"'.$starwars["orbital_period"].'",';
        $contenido_starwars.='"diameter":"'.$starwars["diameter"].'"}';

        /*comprobamos el ultimo valor que formará parte del json */
         if ($contador==$totalvector):
         $contenido_starwars.="";
         else:
          $contenido_starwars.=',{';
         endif;
       
        $contador++;

        endforeach;  

        $codificar_post=json_encode($PostPlaneta,JSON_FORCE_OBJECT);
        $contenido_starwars.=',';
        $contenido_starwars.=$codificar_post;

        $contenido_starwars.=']';
        $contenido_starwars.='}';  

        return $contenido_starwars;
        
     }


   
}
