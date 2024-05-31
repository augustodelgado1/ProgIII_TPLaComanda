

<?php

require_once './Clases/Orden.php';
require_once './Clases/Mesa.php';
require_once './Clases/Cliente.php';

class OrdenController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Orden';
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        
       
        if(isset($codigoDeOrden ) &&  $codigoDeOrden !== false)
        {
           Orden::DarDeAlta($unaMesa ,$data['idDeC']);
            $mensaje = 'la Orden se dio de alta';
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
            $mensaje = Orden::ToStringList($listaDeOrdens);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
