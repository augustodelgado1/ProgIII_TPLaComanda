

<?php

require_once './Clases/Pedido.php';

// 8- De las pedidos:
// a- Lo que más se vendió.
// b- Lo que menos se vendió.
// c- Los que no se entregaron en el tiempo estipulado.
// d- Los cancelados.


class PedidoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = ['Error' => 'Hubo un error con los parametros al intentar dar de alta un Pedido'];
        $unTipo = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']);
        $listaFiltrada = Producto::FiltrarPorTipoDeProductoBD($unTipo->GetId()) ; 
        $unProducto = Producto::BuscarPorNombre($listaFiltrada,$data['nombreDeProducto']);
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']) ;     

        if(isset($unProducto) && isset($unaOrden) )
        {
            $unPedido = new Pedido($unaOrden->GetId(),$unProducto->GetId());
            
            if($unPedido->AgregarBD())
            {
                $mensaje = ['Ok' =>'Se dio de alta correctamente <br>'.$unPedido->ToString()];
            }
            
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unPedido = Pedido::ObtenerUnoPorCodigoBD($data['codigoDePedido']);
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);
        $unTipo = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']);
        $listaFiltrada = Producto::FiltrarPorTipoDeProductoBD($unTipo->GetId()) ; 
        $unProducto = Producto::BuscarPorNombre($listaFiltrada,$data['nombreDeProducto']);

        $mensaje = ['Error' => 'No se pudo modificar'];

        if(isset($unProducto) && Pedido::ModificarUnoBD($unPedido->GetId(),$unaOrden->GetId(),$unProducto->GetId()))
        {
            $unPedido = Pedido::ObtenerUnoPorCodigoBD($data['codigoDePedido']);
            $mensaje =  ['Ok' =>'El Pedido se modifico correctamente: <br>'.$unPedido->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unPedido = Pedido::ObtenerUnoPorCodigoBD($data['codigoDePedido']);
        $mensaje =  ['Error' => 'No se pudo borrar'];

        if(Pedido::BorrarUnoPorIdBD($unPedido->GetId()))
        {
            $mensaje = 'Este Pedido se borro correctamente: <br>'.$unPedido->ToString();;
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
        $listaDePedidos = Pedido::ObtenerListaBD();

        if(isset($listaDePedidos))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDePedidos) > 0)
            {
                $mensaje = ['OK' => Pedido::ToStringList($listaDePedidos)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarTerminados($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
        $listaDePedidosTerminados = Pedido::FiltrarPorEstadoBD(Pedido::ESTADO_FINAL);

        if(isset($listaDePedidosTerminados))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDePedidosTerminados) > 0)
            {
                $mensaje = ['OK' => Pedido::ToStringList($listaDePedidosTerminados)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function ListarPendientes($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
        $listaDePedidos = Pedido::FiltrarPorEstadoBD(Pedido::ESTADO_INICIAL);

        if(isset($listaDePedidos))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDePedidos) > 0)
            {
                $mensaje = ['OK' => Pedido::ToStringList($listaDePedidos)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    // d- Los cancelados.
    public static function ListarCancelados($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
        $listaDePedidos = Pedido::FiltrarPorEstadoBD(PEDIDO::ESTADO_CANCELADO);

        if(isset($listaDePedidos))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDePedidos) > 0)
            {
                $mensaje =['OK' => Pedido::ToStringList($listaDePedidos)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarNoEntregadoEnElTimpoEstipulado($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
        $listaDePedidos = Pedido::FiltrarPorEstadoDelTiempoBD(PEDIDO::ESTADO_TIEMPO_NOCUMPLIDO);

        if(isset($listaDePedidos))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDePedidos) > 0)
            {
                $mensaje =['OK' => Pedido::ToStringList($listaDePedidos)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function PreapararUnPedido($request, $response, array $args)
    { 
        $dataBody = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = (array) AutentificadorJWT::ObtenerData($token);
       
        $mensaje = ['Error'=>'Hubo un error  al intentar preparar un pedido'];  
       
        $unPedido = Pedido::ObtenerUnoPorCodigoBD($dataBody['codigo']);
       
        
        if(isset($unPedido) && isset($data))
        {
            $unPedido->SetHora($dataBody['hora']);
            $unPedido->SetMinuto($dataBody['minutos']);
            $unPedido->ModificarIdDeEmpleadoBD($data['id']);
            $unPedido->ModificarEstadoBD(Pedido::ESTADO_INTERMEDIO);
            $unPedido->ModificarTiempoEstimadoBD($unPedido->GetTiempoEstimado());
            $unPedido->ModificarTiempoDeInicioBD(new DateTime('now'));
            $mensaje = ['OK'=>'Se modifico Correctamente <br>'.$unPedido->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function FinalizarPreparacionDeUnPedido($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje =['Error'=> 'Hubo un error al intentar finalizar un Pedido'];  
       
        $unPedido = Pedido::ObtenerUnoPorCodigoBD($data['codigo']);
       
        if(isset($unPedido))
        {
            $unPedido->ModificarEstadoBD(Pedido::ESTADO_FINAL);
            $unPedido->ModificarTiempoDeFinalizacionBD(new DateTime("now"));
            $unPedido->EvaluarEstadoDelTiempo();
            $mensaje = ['OK'=>'Se finalizo Correctamente <br>'.$unPedido->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function CancelarUnPedido($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Pedidos'];  
       
        $unPedido = Pedido::ObtenerUnoPorCodigoBD($data['codigo']);
       
        if(isset($unPedido))
        {
           
            $unPedido->ModificarEstadoBD(Pedido::ESTADO_CANCELADO);
            $mensaje = ['OK'=>'Se modifico Correctamente <br>'.$unPedido->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarElPedidoMasVendido($request, $response, array $args)
    { 
        $data = $request->getQueryParams();
       
        $mensaje = ['Error'=>'Hubo un error al intentar obtener el mas vendido '];  
        $listaDePedidos = Pedido::ObtenerListaBD();
        // $listaDePedidos = Pedido::FiltrarPorFechaDePedidoBD($data['fechaIngresada']);
        $listaDeProducto = Producto::ObtenerListaBD();

        $cantidad = Pedido::BuscarElPedidoMasVendido($listaDeProducto, $listaDePedidos );
        $unPedido =  Pedido::BuscarPorCantidad($listaDePedidos,$cantidad);
        
        if(isset($unPedido))
        {
            $mensaje = ['OK'=> "El Pedido mas Vendido es ".
            $unPedido->ToString().
            "<br> Y La Cantidad es " .$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarElPedidoMenosVendido($request, $response, array $args)
    { 
        $data = $request->getQueryParams();
       
        $mensaje = ['Error'=>'Hubo un error al intentar obtener el mas vendido '];  
      
        $listaDePedidos = Pedido::ObtenerListaBD();
        $listaDeProducto = Producto::ObtenerListaBD();
        // $listaDePedidos = Pedido::FiltrarPorFechaDePedidoBD($data['fechaIngresada']);
        $cantidad = Pedido::BuscarElPedidoMenosVendido($listaDeProducto, $listaDePedidos );
        $unPedido =  Pedido::BuscarPorCantidad($listaDePedidos,$cantidad);

        if(isset($unPedido))
        {
           
            $mensaje = ['OK'=>"El Pedido mas Vendido es ".$unPedido->ToString(). "<br> y la cantidad es ".$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    

    // c- Los que no se entregaron en el tiempo estipulado.

    
}

?>
