

<?php

require_once './Clases/Sector.php';
require_once './Clases/Cargo.php';

class CargoController 
{
    
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = ['Error'=> 'Hubo un error con los parametros al intentar dar de alta un Cargo'];
        $unSector = Sector::BuscarPorDescripcionBD($data['sector']) ;   
    
        if(isset($unSector))
        {
            $mensaje = ['Error'=> 'no se pudo dar de alta'];
            $unCargo = new Cargo($data['descripcion'],$unSector->GetId());
            
            if($unCargo->AgregarBD())
            {
                $mensaje =  ['OK'=>'un Cargo se dio de alta:'.
                $unCargo->ToString()];
            }
        }


        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje =  ['Error'=> 'no se pudo modificar'];

        if(Cargo::ModificarUnoBD($data['id'],$data['descripcion'],$data['idDeSector']))
        {
            $mensaje =  ['OK'=> 'El Cargo se Modifico correctamente'];
        }
        
        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');;
    }
    
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = ['Error'=> 'no se pudo borrar'];

        if(Cargo::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = ['OK'=>'El Cargo se borro correctamente'];
        }

        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');;
    }

   
}

?>
