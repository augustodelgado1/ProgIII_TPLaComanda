

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
        

        
        if(isset($data) && isset($unSector))
        {
            $mensaje = 'no se pudo dar de alta';
            $unTipoDeProducto = new TipoDeProducto($data['nombre'],$unSector->GetId());
            if($unTipoDeProducto->AgregarBD())
            {
                $mensaje = 'El Tipo De Producto se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo dar modificar';

        if(TipoDeProducto::ModificarUnoBD($data['id'],$data['descripcion'],$data['idDeSector']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(TipoDeProducto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

   
}

?>
