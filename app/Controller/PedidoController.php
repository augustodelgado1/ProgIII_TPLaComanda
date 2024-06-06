

<?php

require_once './Clases/Pedido.php';

// 8- De las pedidos:
// a- Lo que más se vendió.
// b- Lo que menos se vendió.
// c- Los que no se entregaron en el tiempo estipulado.
// d- Los cancelados.


class PedidoController extends Pedido
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Pedido';
        $unTipo = TipoDeProducto::BuscarPorNombreBD($data['tipoDeProducto']);
        $listaFiltrada = Producto::FiltrarPorTipoDeProductoBD($unTipo) ; 
        $unProducto = Producto::BuscarPorNombre($listaFiltrada,$data['nombreDeProducto']);
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']) ;     


         
        if(isset($unProducto ) && isset($unaOrden) &&  $unProducto !== false)
        {
           
            if(Pedido::Alta( $unaOrden,$unProducto))
            {
                $mensaje = 'Se dio de alta correctamente';
            }
            
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
            $mensaje = "la lista esta vacia";
            if(count($listaDePedidos) > 0)
            {
                $mensaje = Pedido::ToStringList($listaDePedidos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }


    public static function ListarPendientes($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
        $listaDePedidos = Pedido::FiltrarPorEstadoBD('pendiente');

        if(isset($listaDePedidos))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDePedidos) > 0)
            {
                $mensaje = Pedido::ToStringList($listaDePedidos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // d- Los cancelados.
    public static function ListarCancelados($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
        $listaDePedidos = Pedido::FiltrarPorEstadoBD(PEDIDO::ESTADO_CANCELADO);

        if(isset($listaDePedidos))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDePedidos) > 0)
            {
                $mensaje = Pedido::ToStringList($listaDePedidos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }


    public static function CambiarEstadoPreparacion($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unPedido = Pedido::BuscarPedidoPorNumeroDePedidoBD($data['numeroDePedido']);
        $unEmpleado = Empleado::ObtenerUnoPorIdBD($data['idDeEmpleado']);
        $horaEstimada = $data['horaEstimada'];
        $minutosEstimada = $data['minutosEstimados'];

        if(isset($unPedido) && isset( $unEmpleado))
        {
            $unPedido->ModificarIdDeEmpleadoBD($unEmpleado->GetId());
            $unPedido->ModificarEstadoBD(Pedido::ESTADO_INTERMEDIO);
            $unPedido->ModificarTiempoEstimadoBD(DateInterval::createFromDateString($horaEstimada.' hours '.$minutosEstimada .' Minutes'));
            $unPedido->ModificarTiempoDeInicioBD(new DateTime('now'));
        }

        $response->getBody()->write($mensaje);
        
        return $response;
    }
    public static function CambiarEstadoListo($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unPedido = Pedido::BuscarPedidoPorNumeroDePedidoBD($data['numeroDePedido']);
       
        if(isset($unPedido))
        {
            $unPedido->ModificarEstadoBD(Pedido::ESTADO_FINAL);
            $unPedido->ModificarTiempoDeFinalizacionBD(new DateTime("now"));
            
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    

    // c- Los que no se entregaron en el tiempo estipulado.

    
}

?>
