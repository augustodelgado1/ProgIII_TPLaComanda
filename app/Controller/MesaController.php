

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
        $mensaje = ['Error'=> 'Hubo un error con los parametros al intentar dar de alta un Mesa'];

        $unaMesa = new Mesa();

        if($unaMesa->AgregarBD())
        {
            $mensaje = ['OK'=>'La Mesa Se Creo Perfectamente:'.$unaMesa->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
        $mensaje = ['Error'=> 'no se pudo borrar'];

        
        if(Mesa::BorrarUnoPorIdBD($unaMesa->GetId()))
        {
            $mensaje = ['OK'=>'La Mesa se borro correctamente:'. $unaMesa->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }


    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Mesas'];  
        $listaDeMesas = Mesa::ObtenerListaBD();

        if(isset($listaDeMesas))
        {
            $mensaje =['OK'=> Mesa::ToStringList($listaDeMesas)];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function SetEstadoCerrarMesa($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = ['Error'=>'No se pudo modificar'];  
       
        $unMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
        $unaOrden = Orden::ObtenerUnoPorCodigo($data['codigoDeOrden']);
       
        if(isset($unMesa) && isset($unaOrden))
        {
            $unMesa->ModificarEstadoBD(Mesa::ESTADO_CERRADO);
            $unaOrden->ModificarEstadoBD(Orden::ESTADO_INACTIVO);
            $unaOrden->ActualizarImporte();
            $mensaje = ['OK'=>'Se modifico correctamente:'.$unMesa->ToString()];  
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }
    public static function SetEstadoServirComida($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = ['Error'=>'No se pudo modificar'];  
       
        $unMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
        $unaOrden = $unMesa->GetOrdenActiva();
       
        if(isset($unMesa) && $unMesa->ModificarEstadoBD(Mesa::ESTADO_INTERMEDIO))
        {
            $unaOrden->ModificarTiempoDeFinalizacionBD(new DateTime('now'));
            $mensaje =  ['OK'=>'Se modifico correctamente'.$unMesa->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }
    public static function SetEstadoPagarOrden($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = ['Error'=> 'No se pudo modificar'];  
       
        $unMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);
       
        if(isset($unMesa) && $unMesa->ModificarEstadoBD(Mesa::ESTADO_FINAL))
        {
            $mensaje =  ['OK'=> 'Se modifico correctamente'.$unMesa->ToString()];  
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }

   

    
    // a- La más usada.
    public static function ListarMesaMasUsada($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = ['Error'=> "no se encontro la mesa mas usada"];
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes = Orden::FiltrarPorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $unaMesa = Mesa::BuscarMesaMasUsada($listaDeMesas,$listaDeOrdenes);
        $cantidad = Mesa::ObtenerCantidadDeUnaMesaPorFecha($unaMesa->GetId(),(new DateTime($data['fecha']))->format("y-m-d"));
        if(isset($unaMesa))
        {
            $mensaje = ['OK'=> "la Mesa mas usada es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    // b- La menos usada.
    public static function ListarMesaMenosUsada($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa menos usada";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes = Orden::FiltrarPorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $unaMesa = Mesa::BuscarMesaMenosUsada($listaDeMesas,$listaDeOrdenes);
        $cantidad = Mesa::ObtenerCantidadDeUnaMesaPorFecha($unaMesa->GetId(),(new DateTime($data['fecha']))->format("y-m-d"));
        if(isset($unaMesa))
        {
            $mensaje =  ['OK'=> "la Mesa menos usada es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    // c- La que más facturó.

    public static function ListarMesaMasFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa que mas facturo";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes = Orden::FiltrarPorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $unaMesa = Mesa::BuscarMesaMasFacturo($listaDeMesas,$listaDeOrdenes);
        
        if(isset($unaMesa) && ($facturacion = $unaMesa->ObtenerFacturacionTotal()) > 0)
        {
            $mensaje = "la Mesa que mas Facturo es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$facturacion;
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    // d- La que menos facturó.

    public static function ListarMesaMenosFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = "no se encontro la mesa que menos facturo";
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes = Orden::FiltrarPorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $unaMesa = Mesa::BuscarMesaMenosFacturo($listaDeMesas,$listaDeOrdenes);
        
        if(isset($unaMesa))
        {
            $mensaje = "la Mesa que menos Facturo es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$unaMesa->ObtenerFacturacionTotal();
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    // e- La/s que tuvo la factura con el mayor importe.

    public static function ListarMesasConMayorImpote($request, $response, array $args)
    {
        $data = $request->getQueryParams();
      
        // $listaDeOrdenes = Orden::FiltarPorEstado(Orden::ESTADO_INACTIVO);
        $importeMayor = Orden::BuscarMayorImportePorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $listaDeOrdenes = Orden::FiltrarPorImporteBD($importeMayor);
        $listafiltrada = Orden::FiltrarPorEstado($listaDeOrdenes,Orden::ESTADO_INACTIVO);
        $listafiltrada = Orden::FiltrarPorFecha($listafiltrada,(new DateTime($data['fecha']))->format("y-m-d"));
        $listaDeMesas = Mesa::FiltrarPorImporteDeOrden($importeMayor);
       
        
        if(isset($listaDeMesas))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeMesas) > 0)
            {
                $mensaje = "la factura con el mayor importe tiene el valor de $importeMayor <br>".
                Mesa::MostrarConOrdenes($listaDeMesas,$listafiltrada);
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    
// f- La/s que tuvo la factura con el menor importe.
    public static function ListarMesasConMenorImpote($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $importeMenor = Orden::BuscarMenorImportePorFechaBD((new DateTime($data['fecha']))->format("y-m-d"));
        $listaDeOrdenes = Orden::FiltrarPorImporteBD($importeMenor);
        $listafiltrada = Orden::FiltrarPorEstado($listaDeOrdenes,Orden::ESTADO_INACTIVO);
        $listafiltrada = Orden::FiltrarPorFecha($listafiltrada,(new DateTime($data['fecha']))->format("y-m-d"));
        $listaDeMesas = Mesa::FiltrarPorImporteDeOrden($importeMenor);
       
        
        if(isset($listaDeMesas))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeMesas) > 0)
            {
                $mensaje = "la factura con el menor importe tiene el valor de $importeMenor <br>".
                Mesa::MostrarConOrdenes($listaDeMesas,$listafiltrada);
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    public static function ListarFacturacionEntreDosFechas($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeMesa']);

        $listaFiltradaPorEstado = Orden::FiltrarPorEstado($unaMesa->ObtenerListaDeOrdenes(),Orden::ESTADO_INACTIVO);
        $listaFiltradaPorFecha = Orden::FiltrarEntreDosFechas($listaFiltradaPorEstado,new DateTime($data['fechaInicial']),new DateTime($data['fechaFinal']));
        $facturacionTotal = Orden::CalcularFacturacionTotal($listaFiltradaPorFecha);
        $mensaje = "La facturacion es ".$facturacionTotal;
        
        if($facturacionTotal > 0)
        {
            $mensaje = "lo que facturo la mesa ".$data['codigoDeMesa'].
            " desde ".$data['fechaInicial'].' hasta '.$data['fechaFinal'].' es '.$facturacionTotal;
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ListarComentariosPorUnaPuntuacion($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaEncuestadas();
        $listaDeEncuesta =  Encuesta::FiltrarPorPuntucionBD($data['descripcion'],$data['puntuacion']);
        // var_dump($listaDeEncuesta);
        
        if(isset($listaDeEncuesta))
        {
            $mensaje = ['Error'=>"No se encontraron comentarios en base al filtro elegido "];
            if(count($listaDeEncuesta) > 0)
            {
                $mensaje =['OK'=>  Mesa::MostarComentarios($listaDeMesas,$listaDeEncuesta)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ListarComentariosPorDosPuntuacion($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $listaDeMesas = Mesa::FiltarMesaEncuestadas();
        $listaDeEncuesta =  Encuesta::FiltrarPorDosPuntucionBD($data['descripcion'],$data['puntuacionMinima'],$data['puntuacionMaxima']);
        // var_dump($listaDeEncuesta);
        
        if(isset($listaDeEncuesta))
        {
            $mensaje = ['Error'=>"No se encontraron comentarios en base al filtro elegido "];
            if(count($listaDeEncuesta) > 0)
            {
                $mensaje = ['OK'=> Mesa::MostarComentarios($listaDeMesas,$listaDeEncuesta)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
   

   
}

?>
