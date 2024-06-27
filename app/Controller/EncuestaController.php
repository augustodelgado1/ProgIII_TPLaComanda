

<?php

require_once './Clases/Encuesta.php';
require_once './Clases/Puntuacion.php';
require_once './Clases/Mesa.php';


// 7- De los empleados:
// a- Los días y horarios que se ingresaron al sistema.
// b- Cantidad de operaciones de todos por sector.
// c- Cantidad de operaciones de todos por sector, listada por cada empleado.
// d- Cantidad de operaciones de cada uno por separado.
// e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos.


class EncuestaController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
        
        $unaEncuesta = new Encuesta($unaOrden->GetId(),$data['nombreDelCliente'],$data['mensaje']);
        $idDeEncuesta = $unaEncuesta->AgregarBD();
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Mesa",$data['puntuacionDeLaMesa']);
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Restaurante",$data['puntuacionDelRestaurante']);
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Cocinero",$data['puntuacionDelCocinero']);
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Mozo",$data['puntuacionDelMozo']);

        if($idDeEncuesta > 0)
        {
            $mensaje = 'la encuesta se relizo correctamente <br>'. $unaEncuesta->ToString();
        }

        $response->getBody()->write($mensaje);

        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);
        $mensaje = 'no se pudo modificar';

        if(Encuesta::ModificarUnoBD($data['id'],$data['nombreDelCliente'],$unaOrden->GetId(),$data['mensaje']))
        {
            $unaEncuesta = Encuesta::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'la Encuesta se modifico correctamente <br>'.$unaEncuesta->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaEncuesta = Encuesta::ObtenerUnoPorIdBD($data['id']);
        $mensaje = 'no se pudo borrar';

        if(Encuesta::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'Esta Encuesta se borro correctamente: <br>'.$unaEncuesta->ToString();;
        }

        $response->getBody()->write($mensaje);


        return $response;
    }


    public static function Listar($request, $response, array $args)
    {
        $mensaje = 'Hubo un error  al intentar listar las Encuestas';
        
        $listaDeEncuestas = Encuesta::ListarBD();
        if(isset($listaDeEncuestas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeEncuestas) > 0)
            {
                $mensaje = Encuesta::ToStringList($listaDeEncuestas);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    
}

?>
