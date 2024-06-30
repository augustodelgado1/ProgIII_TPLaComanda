

<?php

require_once './Clases/Producto.php';
require_once './Clases/TipoDeProducto.php';
require_once './Clases/Orden.php';


class ProductoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = ['Error' =>'Hubo un error con los parametros al intentar dar de alta un Producto'];
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']) ;   
      
     
        if(isset($data))
        {
            $mensaje =  ['Error' =>'no se pudo dar de alta'];
            $unProducto =  new Producto($data['nombre'],$unTipoDeProducto->GetId(),$data['precio']);
           
            if($unProducto->AgregarBD())
            {
                $mensaje = ['OK' =>'El Producto se dio de alta <br>'.$unProducto->ToString()];
            }
        }


          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']) ;   

         $mensaje = ['Error' => 'No se pudo modificar'];

        if(Producto::ModificarUnoBD($data['id'],$data['nombre'],$unTipoDeProducto->GetId(),$data['precio']))
        {
            $unProducto = Producto::ObtenerUnoPorIdBD($data['id']);
            $mensaje = ['OK' =>'El Producto se modifico correctamente: <br>'.$unProducto->ToString()];
        }
        
          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unProducto = Producto::ObtenerUnoPorIdBD($data['id']);
        $mensaje = ['Error' =>'no se pudo borrar'];

        if(Producto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = ['OK' =>'Este Producto se borro correctamente: <br>'.$unProducto->ToString()];
        }

          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Listar($request, $response, array $args)
    {
       
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los Productos'];      
        
        $listaDeProductos = Producto::ObtenerListaBD();


        if(isset($listaDeProductos))
        {
            $mensaje = ['Error' =>"La lista esta vacia"];
            if(count($listaDeProductos) > 0)
            {
                $mensaje = ['OK' => Producto::ToStringList($listaDeProductos)];
            }
        }
          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListarPorTipoDeProducto($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = ['Error'  => 'Hubo un error  al intentar listar los Productos'];
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipo']) ;         
        
        $listaDeProductos = Producto::FiltrarPorTipoDeProductoBD($unTipoDeProducto);


        if(isset($listaDeProductos))
        {
            $mensaje = ['Error' =>"La lista esta vacia"];
            if(count($listaDeProductos) > 0)
            {
                $mensaje = ['OK' => Producto::ToStringList($listaDeProductos)];
            }
        }

          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function GuardarListaEnCsv($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje =  ['Error' => 'Hubo un error al intentar guardar la lista '];  
       
        $listaAGuardar = Producto::ObtenerListaBD();
       
        if(Producto::EscribirCsv($data['nombreDelArchivo'],$listaAGuardar) )
        {
            $nombreDelArchivo = $data['nombreDelArchivo'];
            $mensaje = File::LeerArchivoCsv($nombreDelArchivo);
        }

        $response->getBody()->write(File::CovertListToFormatCsv($mensaje));


        return $response->withHeader('Content-Type', 'text/csv')
        ->withHeader('Content-Disposition', 'attachment; filename='.$data['nombreDelArchivo']);;
    }
    public static function CargarListaPorCsv($request, $response, array $args)
    { 
        $data = $request->getQueryParams();
       
        $mensaje = ['Error' => 'Hubo un error al intentar obtener la lista '];  
      
        $listaDeProductos = Producto::LeerCsv($data['nombreDelArchivo']);
        Producto::ModificarListaBD($listaDeProductos);
        Producto::AgregarListaBD($listaDeProductos);
        if(isset( $listaDeProductos ))
        {
            $mensaje = ['OK' => "El Archivo Se cargo correctamente"];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    // 19- Alguno de los socios pide un listado del producto ordenado del que más se vendió al que
    // menos se vendió

    public static function ListarOrdenadosPorCantidadVendido($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $fecha = new DateTime('now');
        $mensaje = ['Error'  => 'Hubo un error  al intentar listar los Productos'];       
        $listaDeProductos = Producto::OrdenarPorCantidadDeVecesVendidoPorMesBD($fecha->format('y-m-d'));


        if(isset($listaDeProductos))
        {
            $mensaje = ['Error' =>"La lista esta vacia"];
            if(count($listaDeProductos) > 0)
            {
                $mensaje = ['OK' => Producto::MostarProductosConCantidad($listaDeProductos,$fecha->format('y-m-d'))];
            }
        }

          $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
