

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
            $mensaje = 'El Producto se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
     
        $mensaje = 'no se pudo borrar';

        if(Producto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Pedido se borro correctamente';
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

    public static function EscribirListaEnCsv($request, $response, array $args)
    { 
        $data = $request->getParsedBody();
       
        $mensaje = 'Hubo un error al intentar guardar la lista ';  
       
        $listaAGuardar = Producto::ObtenerListaBD();
       
        if(Producto::EscribirCsv($data['nombreDelArchivo'],$listaAGuardar) )
        {
            $mensaje = 'Se guardo correctamente'; 
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function LeerListaEnCsv($request, $response, array $args)
    { 
        $data = $request->getQueryParams();
       
        $mensaje = 'Hubo un error al intentar obtener la lista ';  
      
        $listaDeProductos = Producto::LeerCsv($data['nombreDelArchivo']);
       
        if(isset($listaDeProductos))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeProductos) > 0)
            {
                $mensaje = Producto::ToStringList($listaDeProductos);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
