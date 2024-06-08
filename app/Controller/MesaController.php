

<?php

require_once './Clases/Mesa.php';
require_once './Clases/Puntuacion.php';

// 9- De las mesas:
// a- La más usada.
// b- La menos usada.
// c- La que más facturó.
// d- La que menos facturó.
// e- La/s que tuvo la factura con el mayor importe.
// f- La/s que tuvo la factura con el menor importe.
// g- Lo que facturó entre dos fechas dadas.
// h- Mejores comentarios.
// i- Peores comentarios

class MesaController extends Mesa
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Mesa';

        $unaMesa = new Mesa();

        if($unaMesa->SetCodigo(Usuario::CrearUnCodigoAlfaNumerico(5)) 
        && $unaMesa->SetEstado('cerrada') &&  $unaMesa->AgregarBD())
        {
            $mensaje = 'La Mesa Se Creo Perfectamente: <br>'.$unaMesa->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Mesas';  
        $listaDeMesas = Mesa::ObtenerListaBD();

        if(isset($listaDeMesas))
        {
            $mensaje = Mesa::ToStringList($listaDeMesas);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPedidos($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);
    
        if(in_array($unaOrden,$unaMesa->ObtenerListaDeOrdenes()))
        {
            $listaDePedidos =  $unaOrden->ObtenerListaDePedidos();
            $mensaje = 'Hubo un error  al intentar listar pedidos'; 
            if(isset($listaDePedidos))
            {
                $mensaje = "la lista esta vacia";
                if(count($listaDePedidos) > 0)
                {
                    $mensaje = Pedido::ToStringList($listaDePedidos);
                }
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarComentariosPositivosDeLasMesas($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaPuntuadas();
        


        $mensaje = "Hubo error en la funcion";
        if(isset($listaDeMesas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeMesas) > 0)
            {
                $mensaje = Mesa::MostarComentarios($listaDeMesas);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function ListarComentariosNegativosDeLasMesas($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaPuntuadas();
        
        if(isset($listaDeMesas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeMesas) > 0)
            {
                $mensaje = Mesa::MostarComentarios($listaDeMesas);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
   

   
}

?>
