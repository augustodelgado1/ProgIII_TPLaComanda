

<?php

require_once './Clases/TipoDeProducto.php';
require_once './Clases/Sector.php';

class TipoDeProductoController 
{
  
    
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = ['Error' => 'Hubo un error con los parametros al intentar dar de alta un TipoDeProducto'];
        $unSector = Sector::BuscarPorDescripcionBD($data['sector']) ;   
        
        if(isset($unSector))
        {
            $mensaje = 'no se pudo dar de alta';
            $unTipoDeProducto = new TipoDeProducto($data['descripcion'],$unSector->GetId());
            if($unTipoDeProducto->AgregarBD())
            {
                $mensaje = ['OK'=> 'El Tipo De Producto se dio de alta:'.PHP_EOL. $unTipoDeProducto->ToString()];
            }
        }


        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = ['Error' => 'No se pudo modificar'];

        if(TipoDeProducto::ModificarUnoBD($data['id'],$data['descripcion'],$data['idDeSector']))
        {
            $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorIdBD($data['id']);
            $mensaje =  ['OK'=> 'El Tipo De Producto se modifico correctamente:'.PHP_EOL.$unTipoDeProducto->ToString()];
           
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unTipoDeProducto = TipoDeProducto::ObtenerUnoPorIdBD($data['id']);
        $mensaje =  ['Error' => 'No se pudo borrar'];

        if(TipoDeProducto::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = ['OK'=>  'Este Tipo De Producto se borro correctamente:'.PHP_EOL.$unTipoDeProducto->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));

        return $response->withHeader('Content-Type', 'application/json');
    }

   
}

?>
