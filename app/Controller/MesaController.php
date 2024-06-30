

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
            $unaOrden->ModificarTiempoTotalEstimadoBD($unaOrden->GetTiempoEstimado());
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
            $unaOrden->ModificarTiempoDeInicioBD($unaOrden->ObtenerTiempoDeInicio());
            $unaOrden->EvaluarEstadoDelTiempo();
            $unaOrden->ModificarTiempoDeFinalizacionBD(new DateTime('now'));
            $unaOrden->ModificarTiempoTotalEstimadoBD($unaOrden->GetTiempoEstimado());
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
        $fechaIngresada = new DateTime('now');
       
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes =  Orden::FiltrarPorMesBD($fechaIngresada);;
        $unaMesa = Mesa::BuscarMesaMasUsada($listaDeMesas,$listaDeOrdenes);
        $cantidad = Orden::ContarPorIdDeMesa($listaDeOrdenes,$unaMesa->GetId());


        if(isset($unaMesa))
        {
            $mensaje = ['OK'=> "la Mesa mas usada Del Mes De ".$fechaIngresada->format("M")." es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    // b- La menos usada.
    public static function ListarMesaMenosUsada($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = ['Error'=>"no se encontro la mesa menos usada"];
        $fechaIngresada = new DateTime('now');
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes =  Orden::FiltrarPorMesBD($fechaIngresada);
        $unaMesa = Mesa::BuscarMesaMenosUsada($listaDeMesas,$listaDeOrdenes);
        $cantidad = Orden::ContarPorIdDeMesa($listaDeOrdenes,$unaMesa->GetId());

        if(isset($unaMesa))
        {
            $mensaje =  ['OK'=> "la Mesa menos usada Del Mes De ".$fechaIngresada->format("M")." es <br>".$unaMesa->ToString()
            .'<br>'.'y la cantidad de veces usada es '.$cantidad];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    // c- La que más facturó.

    public static function ListarMesaMasFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = ['Error'=>"no se encontro la mesa que mas facturo"];
        $fechaIngresada = new DateTime('now');
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes =  Orden::FiltrarPorMesBD($fechaIngresada);;
        $unaMesa = Mesa::BuscarMesaMasFacturo($listaDeMesas,$listaDeOrdenes);
        $facturacion = $unaMesa->CalcularFacturacionPorMes($fechaIngresada->format("m"));
        
        if(isset($unaMesa))
        {
            $mensaje =  ['OK'=>"la Mesa que mas Facturo En El mes De ".$fechaIngresada->format("M")." es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$facturacion];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    // d- La que menos facturó.

    public static function ListarMesaMenosFacturo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje =  ['Error'=>"no se encontro la mesa que menos facturo"];
        $fechaIngresada = new DateTime('now');
        $listaDeMesas = Mesa::ObtenerListaBD();
        $listaDeOrdenes =  Orden::FiltrarPorMesBD($fechaIngresada);;
        $unaMesa = Mesa::BuscarMesaMenosFacturo($listaDeMesas,$listaDeOrdenes);
        $facturacion = $unaMesa->CalcularFacturacionPorMes($fechaIngresada->format("m"));

        if(isset($unaMesa) && $facturacion > 0)
        {
            $mensaje = ['Ok'=>"la Mesa que menos Facturo En El mes De ".$fechaIngresada->format("M")." es <br>".$unaMesa->ToString()
            .'<br>'.'y la Facturacion total fue '.$facturacion];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }



    // f- 21- Alguno de los socios pide un listado de las mesas ordenadas de la que hizo la factura más
// barata a la más cara.

    public static function ListarMesasOrdenadasPorFacturaASC($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $fechaIngresada = new DateTime($data['fecha']);
        $importeMenor = Orden::BuscarMenorImportePorMesBD($fechaIngresada);
        $listaDeMesas = Mesa::OrdenarPorImporteDeFacturaPorMesBD($importeMenor,$fechaIngresada);
        $listaDeOrdenes = Orden::OrdenarPorImporteDeFacturaPorMesBD($importeMenor,$fechaIngresada);
        
        if(isset($listaDeMesas))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeMesas) > 0)
            {
                $mensaje =  ['ok' => Mesa::MostrarConOrdenes($listaDeMesas,$listaDeOrdenes)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

   
    public static function ListarFacturacionEntreDosFechas($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $fechaInicial = new DateTime($data['fechaInicial']);
        $fechaFinal = new DateTime($data['fechaFinal']);
        $unaMesa = Mesa::ObtenerUnoPorCodigo($data['codigoDeFecha']);
        $facturacionTotal = Orden::CalcularFacturacionDeUnaMesaEntreDosFechas($unaMesa->GetId(),$fechaInicial->format('y-m-d'),$fechaFinal->format('y-m-d'));
        
        if($facturacionTotal > 0)
        {
            $mensaje = ['ok' => "lo que se facturo ".$data['codigoDeMesa'].
            " desde ".$fechaInicial->format('y-m-d').' hasta '.$fechaFinal->format('y-m-d').' es '.$facturacionTotal];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
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
                $mensaje =['OK'=>  Encuesta::ToStringList($listaDeEncuesta)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarComentariosPorDosPuntuaciones($request, $response, array $args)
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
                $mensaje = ['OK'=> Encuesta::ToStringList($listaDeEncuesta)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
   

   
}

?>
