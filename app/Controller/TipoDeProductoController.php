

<?php

require_once './Clases/TipoDeProducto.php';
require_once './Clases/Sector.php';

class TipoDeProductoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un TipoDeProducto';
        $unSector = Sector::BuscarPorDescripcionBD($data['sector']) ;   
        

        
        if(isset($data) && isset($unSector))
        {
            $mensaje = 'no se pudo dar de alta';

            if(TipoDeProducto::DarDeAlta($data['nombre'],$unSector))
            {
                $mensaje = 'El TipoDeProducto se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

   
}

?>
