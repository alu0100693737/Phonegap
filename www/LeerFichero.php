<?php
header('Content-Type: text/html; charset=UTF-8');
$pregunta = $_GET["pregunta"];
$temaElegido = $_GET["temaElegido"];
$nombreFichero = $temaElegido.".txt";

$preguntaOinformacion= substr($pregunta, -1);

if($preguntaOinformacion != "?"){
    if(strcmp ($temaElegido , "Examenes" ) == 0){
      $fichero = fopen("docs/".$nombreFichero,"a");
      fputs($fichero, $pregunta."\r\n");
      $jsondata["message"] = "Información añadida correctamente";
    }else{
      $jsondata["message"] = "No se puede añadir información sobre este tema";
    }
}else{
    $contenido = explode(" ",$pregunta);
    $preguntaFinal = explode("?",$contenido[count($contenido)-1]);
    $fichero = fopen("docs/".$nombreFichero,"r");
    $linea="";
    $encontrada=false;
    if(strcmp ($temaElegido , "Examenes" ) == 0){
      $latitud = (double)($_GET["latitud"]);
      $longitud = (double)($_GET["longitud"]);

      while(!feof($fichero) && ($encontrada==false)){
          $linea = fgets($fichero);
          $lineaEnPartes = explode(",",$linea);
          $latitudMaxFichero = (double)($lineaEnPartes[1]);
          $latitudMinFichero = (double)($lineaEnPartes[2]);
          $longitudMaxFichero = (double)($lineaEnPartes[3]);
          $longitudMinFichero = (double)($lineaEnPartes[4]);
          $examenCorrecto = stripos($linea, $preguntaFinal[0]);
          if(($examenCorrecto == true)&&(($latitud >= $latitudMinFichero) && ($latitud <= $latitudMaxFichero)) && (($longitud >= $longitudMinFichero) && ($longitud <= $longitudMaxFichero))){
             $encontrada = true;
          }
      }
    }else{
      while(!feof($fichero) && ($encontrada==false)){
          $linea = fgets($fichero);
          $encontrada = stripos($linea, $preguntaFinal[0]);

      }
    }
    if($encontrada === false && $temaElegido == "Examenes"){
            $jsondata["message"] = "Ese examen no se realiza en el aula en la que se encuentra";
            $jsondata["message"] = "No tengo informacion para contestar a esa pregunta";
    }else{
      if($encontrada === false && $temaElegido != "Examenes"){
        $jsondata["message"] = "No tengo informacion para contestar a esa pregunta";
      }else{
        if($encontrada === true && $temaElegido == "Examenes"){
          $jsondata["message"] = $lineaEnPartes[0];
        }else{
          $jsondata["message"] = $linea;
        }
      }
    }

  }
fclose($fichero);

$resultadosJson = json_encode($jsondata);
//echo $resultadosJson;
echo $_GET['jsoncallback'] . '(' . $resultadosJson . ');';

?>
