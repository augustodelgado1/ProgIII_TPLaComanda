

<?php
require_once './Clases/Pedido.php';

class PuntuacionController 
{
    
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta una puntuacion';
        $ultimoID = Puntuacion::DarDeAltaUnPuntuacion($data['idDeEncuesta'],$data['descripcion'],$data['puntuacion']);
        if( $ultimoID > 0)
        {
            $unaPuntuacion = Puntuacion::ObtenerUnoPorIdBD($ultimoID);
            $mensaje = 'la puntuacion se dio de alta: <br>'.$unaPuntuacion->ToString();
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
            $unaPuntuacion = Puntuacion::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'la puntuacion se modifico correctamente: <br>'.$unaPuntuacion->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaPuntuacion = Puntuacion::ObtenerUnoPorIdBD($data['id']);
        $mensaje = 'no se pudo borrar';

        if(Puntuacion::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'Esta puntuacion se borro correctamente:  <br>'.$unaPuntuacion->ToString();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
   
}

?>
