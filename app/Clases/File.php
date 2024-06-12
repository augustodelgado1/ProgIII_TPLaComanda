
<!-- 
    Alumno:Augusto Delgado
    Div A332
 -->
<?php

class File
{
    public static function MoverArchivoSubido($tmpNombre,$rutaASubir,$nombreDeArchivo)
    {
        $estado = false;
       
        if(isset($tmpNombre) && isset($rutaASubir))
        {
            $rutaDestino = $rutaASubir . $nombreDeArchivo;
            $estado =  move_uploaded_file($tmpNombre,$rutaDestino);
        }

        return $estado;
    }
    public static function CrearUnDirectorio($ruta)
    {
        $estado = false;

        if(!file_exists($ruta)  )
        {
            $estado = mkdir($ruta);
        }

        return $estado;
    }

    public static function LeerArchivoCsv($nombreDeArchivo)
    {
        $listaDeLineas  = null;
        $unArchivo = fopen($nombreDeArchivo,"r");

        if(isset($unArchivo)){

            $listaDeLineas = [];
    
            while(($unaLinea = fgetcsv($unArchivo)) !== false){

                if(isset($unaLinea))
                {
                    array_push($listaDeLineas,$unaLinea);
                }
            }

            fclose($unArchivo);
        }

        return   $listaDeLineas ;
    }
    public static function EscribirGenerico($lista,$nombreDeArchivo,$funcEscribirUno)
    {
        $estado = false;
        $unArchivo = fopen($nombreDeArchivo,"w");

        if(isset( $unArchivo) && isset($lista) && isset($funcEscribirUno)){
            
            $estado = true;
            
            foreach($lista as $unArray)
            {
               
                if(!call_user_func($funcEscribirUno,$unArray,$unArchivo))
                {
                    $estado = false;
                    break;
                }
            }
        }

        return $estado;
    }

    public static function ValidarNombreDelArchivo($data)
    {
    
        return  isset($data['nombreDelArchivo']);
    }
    public static function ValidarExistenciaDelArchivo($data)
    {
        return  file_exists($data['nombreDelArchivo']);
    }



    
}


?>



