

<?php
require_once './Clases/Pedido.php';

class PuntuacionController 
{
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta una puntuacion';
    
        if(Puntuacion::DarDeAltaUnPuntuacion($data['idDeEncuesta'],$data['descripcion'],$data['puntuacion']))
        {
            $mensaje = 'la puntuacion se dio de alta';
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
            $mensaje = 'la puntuacion se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo borrar';

        if(Puntuacion::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'la puntuacion se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
   
}

?>
