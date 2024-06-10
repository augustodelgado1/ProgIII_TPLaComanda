

<?php

require_once './Clases/Encuesta.php';
require_once './Clases/Puntuacion.php';


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
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);
        $idDeEncuesta = Encuesta::DarDeAlta($unaOrden->GetId(),$data['nombreDelCliente'],$data['mensaje']);
        $mensaje = 'no se pudo dar de alta';
        

        if(Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Mesa",$data['puntuacionDeLaMesa']) && 
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Restaurante",$data['puntuacionDelRestaurante']) && 
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Cocinero",$data['puntuacionDelCocinero']) && 
        Puntuacion::DarDeAltaUnPuntuacion($idDeEncuesta,"Mozo",$data['puntuacionDelMozo']))
        {
            $mensaje = 'la encuesta se relizo correctamente';
        }

        $response->getBody()->write($mensaje);

        return $response;
    }
    // private $id;
    // private $nombreDelCliente;
    // private $idDeOrden;
    // private $mensaje;
    // private $estado;
    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);
        $mensaje = 'no se pudo dar modificar';

        if(Encuesta::ModificarUnoBD($data['id'],$data['nombreDelCliente'],$unaOrden->GetId(),$data['mensaje']))
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

        if(Encuesta::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }


    public static function Listar($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
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
