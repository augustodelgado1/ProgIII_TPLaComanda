

<?php

require_once './Clases/Producto.php';
require_once './Clases/TipoDeProducto.php';
require_once './Clases/Orden.php';


class ProductoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Producto';
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']) ;   
      
     
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';
            $unProducto =  new Producto($data['nombre'],$unTipoDeProducto->GetId(),$data['precio']);
           
            if($unProducto->AgregarBD())
            {
                $mensaje = 'El Producto se dio de alta <br>'.$unProducto->ToString();
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipoDeProducto']) ;   

        $mensaje = 'no se pudo dar modificar';

        if(Producto::ModificarUnoBD($data['id'],$data['nombre'],$unTipoDeProducto->GetId(),$data['precio']))
        {
            $unProducto = Producto::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'El Producto se modifico correctamente: <br>'.$unProducto->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unProducto = Producto::ObtenerUnoPorIdBD($data['id']);
        $mensaje = 'no se pudo borrar';

        if(Producto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'Este Producto se borro correctamente: <br>'.$unProducto->ToString();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
       
        $mensaje = 'Hubo un error  al intentar listar los Productos';      
        
        $listaDeProductos = Producto::ObtenerListaBD();


        if(isset($listaDeProductos))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeProductos) > 0)
            {
                $mensaje = Producto::ToStringList($listaDeProductos);
            }
        }
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorTipoDeProducto($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Productos';
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorNombreBD($data['tipo']) ;         
        
        $listaDeProductos = Producto::FiltrarPorTipoDeProductoBD($unTipoDeProducto);


        if(isset($listaDeProductos))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeProductos) > 0)
            {
                $mensaje = Producto::ToStringList($listaDeProductos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function GuardarListaEnCsv($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje =  'Hubo un error al intentar guardar la lista ';  
       
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


        return $response;
    }
}

?>
