

<?php

require_once './Clases/Sector.php';

class SectorController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Sector';

        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Sector::DarDeAltaUnSector($data['nombre']))
            {
                $mensaje = 'El Sector se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

   
}

?>
