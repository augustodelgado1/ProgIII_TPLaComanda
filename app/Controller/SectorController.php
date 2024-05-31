

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

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Mesas';  
        $listaDeSectores = Sector::ListarBD();

      

        if(isset($listaDeSectores))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeSectores) > 0)
            {
                $mensaje = Sector::ToStringList($listaDeSectores);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }



   
}

?>
