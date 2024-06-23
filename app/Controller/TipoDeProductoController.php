

<?php

require_once './Clases/TipoDeProducto.php';
require_once './Clases/Sector.php';

class TipoDeProductoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un TipoDeProducto';
        $unSector = Sector::BuscarPorDescripcionBD($data['sector']) ;   
        
        if(isset($unSector))
        {
            $mensaje = 'no se pudo dar de alta';
            $unTipoDeProducto = new TipoDeProducto($data['nombre'],$unSector->GetId());
            if($unTipoDeProducto->AgregarBD())
            {
                $mensaje = 'El Tipo De Producto se dio de alta <br>'. $unTipoDeProducto->ToString();
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo modificar';

        if(TipoDeProducto::ModificarUnoBD($data['id'],$data['descripcion'],$data['idDeSector']))
        {
            $mensaje = 'El Tipo De Producto se modifico correctamente';
           
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo borrar';

        if(TipoDeProducto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'el Tipo De Producto se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

   
}

?>
