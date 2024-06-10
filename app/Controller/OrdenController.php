

<?php

require_once './Clases/Orden.php';
require_once './Clases/Mesa.php';
require_once './Clases/Usuario.php';
require_once './Clases/File.php';

class OrdenController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        File::CrearUnDirectorio('Imagenes');
        File::CrearUnDirectorio('Imagenes/Mesa');
        
        $unaOrden = new Orden($data['nombreDelCliente'],$unaMesa->GetId());
        $unaOrden->GuardarImagen($_FILES['imagen']['tmp_name'],"Imagenes/Mesa/",$_FILES['imagen']['name']);

        if( $unaOrden->AgregarBD())
        {
            $mensaje = 'la Orden se dio de alta ';
        }
        
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo dar modificar';

        if(Orden::ModificarUnoBD($data['id'],$data['nombreDelCliente'],$data['idDeMesa']))
        {
            $mensaje = 'El Orden se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Orden::BorrarUnoPorIdBD($data['id']))
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
        $data = $request->getHeaders();
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);
    
        if($unaOrden->VerificarIdDeMesa($unaMesa->GetId()))
        {
            $mensaje = 'Usted pidio: <br><br>'.$unaOrden->ToString(); 
        }

        $response->getBody()->write($mensaje);

        return $response;
    }
}

?>
