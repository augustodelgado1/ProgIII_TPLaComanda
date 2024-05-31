
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

    
}


?>



