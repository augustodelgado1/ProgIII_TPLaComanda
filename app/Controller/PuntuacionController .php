

<?php
require_once './Clases/Pedido.php';

class PuntuacionController 
{
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Sector';
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';
           

            if(Puntuacion::DarDeAltaUnPuntuacion($data['idDeEncuesta'],$data['descripcion'],$data['puntuacion']))
            {
                $mensaje = 'El Sector se dio de alta';
            }
        }

        $response->getBody()->write($mensaje);
        return $response;
    }


    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo dar modificar';

        if(Puntuacion::ModificarUnoBD($data['id'],$data['descripcion'],$data['puntuacion'],$data['idDeEncuesta']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Puntuacion::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
   
}

?>
