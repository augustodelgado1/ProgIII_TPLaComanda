

<?php

require_once './Clases/Sector.php';
require_once './Clases/Cargo.php';

class CargoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un TipoDeProducto';
        $unSector = Sector::BuscarPorDescripcionBD($data['sector']) ;   
    
        if(isset($data) && isset($unSector))
        {
            $mensaje = 'no se pudo dar de alta';
            $unCargo = new Cargo($data['descripcion'],$unSector->GetId());
            
            if($unCargo->AgregarBD())
            {
                $mensaje = 'un Cargo se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo modificar';

        if(Cargo::ModificarUnoBD($data['id'],$data['descripcion'],$data['idDeSector']))
        {
            $mensaje = 'El Cargo se Modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo borrar';

        if(Cargo::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Cargo se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

   
}

?>
