

<?php

require_once './Clases/Orden.php';
require_once './Clases/Mesa.php';
require_once './Clases/Usuario.php';

class OrdenController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
      
        $unaOrden = new Orden($data['nombreDelCliente'],$unaMesa->GetId());
        $unaMesa->ModificarEstadoBD(Mesa::ESTADO_INICIAL);
       
        if( $unaOrden->AgregarBD())
        {
            $mensaje = 'la Orden se dio de alta '.
                        $unaOrden->ToString();
        }
        
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    // ❏ 2- El mozo saca una foto de la mesa y lo relaciona con el pedido.
    public static function AgregarFoto($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::BuscarOrdenPorIdBD($data['codigoDeOrden']);
        File::CrearUnDirectorio('Imagenes');
        File::CrearUnDirectorio('Imagenes/Mesa');
        $mensaje = 'No se pudo guarder la foto';

        if($unaOrden->GuardarImagen($_FILES['imagen']['tmp_name']
        ,"Imagenes/Mesa/",
        $_FILES['imagen']['name']))
        {
            $mensaje = 'La foto se guardo correctamente ';
        }
        
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);

        $mensaje = 'no se pudo dar modificar';

        if(Orden::ModificarUnoBD($unaOrden->GetId(),$data['nombreDelCliente'],$data['idDeMesa']))
        {
            $mensaje = 'El Orden se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);
        $mensaje = 'no se pudo dar de alta';

        if(Orden::BorrarUnoPorIdBD($unaOrden->GetId()))
        {
            $mensaje = 'El Orden se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Ordens';  
        $listaDeOrdens = Orden::ListarBD();

      

        if(isset($listaDeOrdens))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeOrdens) > 0)
            {
                $mensaje = Orden::ToStringList($listaDeOrdens);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarUno($request, $response, array $args)
    {
        $data = $request->getQueryParams();
     
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);

        $mensaje = 'Usted pidio: <br><br>'.$unaOrden->ToString(); 
        
        $response->getBody()->write($mensaje);

        return $response;
    }
}

?>
