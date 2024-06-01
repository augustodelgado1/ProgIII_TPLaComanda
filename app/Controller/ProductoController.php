

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
        $unTipoDeProducto = TipoDeProducto::BuscarPorNombreBD($data['tipoDeProducto']) ;   

     
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Producto::DarDeAltaUnProducto($data['nombre'],$data['precio'],$unTipoDeProducto))
            {
                $mensaje = 'El Producto se dio de alta';
            }
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
        $unTipoDeProducto = TipoDeProducto::BuscarPorNombreBD($data['unTipoDeProducto']) ;         
        
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
}

?>
