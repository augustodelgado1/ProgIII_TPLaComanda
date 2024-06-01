

<?php

require_once './Clases/Orden.php';
require_once './Clases/Mesa.php';
require_once './Clases/Cliente.php';

class OrdenController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo error con los parametros al intentar dar de alta un Orden';
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        $unCliente = Cliente::BuscarClientePorIdBD($data['idDeCliente']);
       
        if(isset($unCliente ) &&  isset($unaMesa ) )
        {
            $mensaje = 'la Orden no se pudo dar de alta';
            if(Orden::DarDeAlta($unaMesa ,$unCliente))
            {
                $mensaje = 'la Orden se dio de alta';
            }
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

    public static function ListarPedidos($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $mensaje = 'el numero de orden es incorrecto';  
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);

    
        if(isset($unaOrden))
        {
            $listaDePedidos =  $unaOrden->ObtenerListaDePedidos();
            $mensaje = 'Hubo un error  al intentar listar pedidos'; 

            if(isset($listaDePedidos) && count($listaDePedidos) > 0)
            {
                $mensaje = Pedido::ToStringList($listaDePedidos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
