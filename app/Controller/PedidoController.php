

<?php

require_once './Clases/Pedido.php';
require_once './Clases/Producto.php';
require_once './Clases/TipoDeProducto.php';
require_once './Clases/Orden.php';



class PedidoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Pedido';
        $listaFiltrada = Producto::FiltrarPorTipoDeProductoBD($data['unTipoDeProducto']) ; 
        $unProducto = Producto::BuscarPorNombre($listaFiltrada,$data['nombreDeProducto']);
        $unaOrden = Orden::BuscarPorNumeroDeOrdenBD($data['numeroDeOrden']) ;     

            
        if(isset($unProducto ) && isset($unaOrden) &&  $unProducto !== false)
        {
            Pedido::Alta( $unaOrden,$unProducto ,$data['cantidad']);
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
        $listaDePedidos = Pedido::ObtenerListaBD();

        if(isset($listaDePedidos))
        {
            $mensaje = Pedido::ToStringList($listaDePedidos);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
