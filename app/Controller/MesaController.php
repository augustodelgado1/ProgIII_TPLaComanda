

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

class MesaController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Mesa';

        $unaMesa = new Mesa();

        if($unaMesa->AgregarBD())
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

    public static function SetEstadoCerrarMesa($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);
       
        if(isset($unMesa))
        {
            $unMesa->ModificarEstadoBD(Mesa::ESTADO_CERRADO);
            $unaOrden->ModificarEstadoBD(Orden::ESTADO_INACTIVO);
            $unaOrden->ActualizarImporte();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function SetEstadoInicial($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
       
        if(isset($unMesa))
        {
            $unMesa->ModificarEstadoBD(Mesa::ESTADO_INICIAL);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function SetEstadoServirComida($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
       
        if(isset($unMesa))
        {
            $unMesa->ModificarEstadoBD(Mesa::ESTADO_INTERMEDIO);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function SetEstadoPagarOrden($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
       
        $unMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
       
        if(isset($unMesa))
        {
            $unMesa->ModificarEstadoBD(Mesa::ESTADO_FINAL);
            
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

   

    public static function ListarComentariosPositivosDeLasMesas($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaEncuestadas();
        $listaDeEncuesta = Encuesta::FiltrarPorPuntucionBD("Mesa",Puntuacion::ESTADO_POSITIVO);

        $mensaje = "Hubo error en la funcion";
        if(isset($listaDeEncuesta))
        {
            $mensaje = "No se encontraron comentarios ".Puntuacion::ESTADO_POSITIVO.'s';
            if(count($listaDeEncuesta) > 0)
            {
                $mensaje = Mesa::MostarComentarios($listaDeMesas,$listaDeEncuesta);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function ListarComentariosNegativosDeLasMesas($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaEncuestadas();
        $listaDeEncuesta = Encuesta::FiltrarPorPuntucionBD("Mesa",Puntuacion::ESTADO_NEGATIVO);
        
        if(isset($listaDeMesas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeMesas) > 0)
            {
                $mensaje = Mesa::MostarComentarios($listaDeMesas,$listaDeEncuesta);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    // a- La más usada.
    public static function ListarMesaMasUsada($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa mas usada";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $unaMesa = Mesa::BuscarMesaMasUsada($listaDeMesas);
        
        if(isset($unaMesa))
        {
            $mensaje = "la Mesa mas usada es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$unaMesa->ObtenerCantidadDeOrdenes();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // b- La menos usada.
    public static function ListarMesaMenosUsada($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa menos usada";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $unaMesa = Mesa::BuscarMesaMenosUsada($listaDeMesas);
        
        if(isset($unaMesa))
        {
            $mensaje = "la Mesa menos usada es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$unaMesa->ObtenerCantidadDeOrdenes();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // c- La que más facturó.

    public static function ListarMesaMasFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa que mas facturo";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $unaMesa = Mesa::BuscarMesaMasFacturo($listaDeMesas);
        
        if(isset($unaMesa))
        {
            $mensaje = "la Mesa que mas Facturo es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$unaMesa->ObtenerFacturacionTotal();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    // d- La que menos facturó.

    public static function ListarMesaMenosFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa que menos facturo";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $unaMesa = Mesa::BuscarMesaMenosFacturo($listaDeMesas);
        
        if(isset($unaMesa))
        {
            $mensaje = "la Mesa que menos Facturo es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$unaMesa->ObtenerFacturacionTotal();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // e- La/s que tuvo la factura con el mayor importe.

    public static function ListarMesasConMayorImpote($request, $response, array $args)
    {
        $data = $request->getQueryParams();
      
        // $listaDeOrdenes = Orden::FiltarPorEstado(Orden::ESTADO_INACTIVO);
        $importeMayor = Orden::BusacarMayorImporteBD();
        $listaDeMesas = Mesa::FiltrarPorImporteDeOrden($importeMayor);
       
        
        if(isset($listaDeMesas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeMesas) > 0)
            {
                $mensaje = "la factura con el mayor importe tiene el valor de $importeMayor <br>".
                Mesa::ToStringList($listaDeMesas);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    
// f- La/s que tuvo la factura con el menor importe.
    public static function ListarMesasConMenorImpote($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $importeMenor = Orden::BuscarMenorImporteBD();
        $listaDeMesas = Mesa::FiltrarPorImporteDeOrden($importeMenor);
       
        
        if(isset($listaDeMesas))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeMesas) > 0)
            {
                $mensaje = "la factura con el menor importe tiene el valor de $importeMenor <br>".
                Mesa::ToStringList($listaDeMesas);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // g- Lo que facturó entre dos fechas dadas.

    public static function ListarFacturacionEntreDosFechas($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);

        $listaFiltrada = Orden::FiltrarEntreDosFechas($unaMesa->ObtenerListaDeOrdenes(),$data['fechaInicial'],$data['fechaFinal']);
        $facturacionTotal = Orden::CalcularFacturacionTotal($listaFiltrada);
        
        if($facturacionTotal > 0)
        {
            $mensaje = "lo que facturo la mesa ".$data['codigoDeMesa']." desde ".$data['fechaInicial'].' hasta '.$data['fechaFinal'].' es '.$facturacionTotal;
            
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
   

   
}

?>
