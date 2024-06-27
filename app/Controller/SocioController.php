

<?php

require_once './Clases/Usuario.php';

class SocioController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los Socio'];
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Socio');

        $listaDeSocios = Usuario::FiltrarPorRolBD( $unRol->GetId());

        if(isset($listaDeSocios))
        {
            $mensaje = ['Error' => "La lista esta vacia"];
            if(count($listaDeSocios) > 0)
            {
                $mensaje = ['OK' => "Socios".'<br>'.Usuario::ToStringList($listaDeSocios)];
            }
        }
        
        $response->getBody()->write(json_encode($mensaje));
        

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;   
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Socio');
        $mensaje = ['Error' => 'No se pudo dar de alta'];

        $unSocio = new Usuario($data['email'],$data['clave'],$data['nombre'],
        $data['apellido'],$data['dni'],$unCargo->GetId(),$unRol->GetId());

        if($unSocio->AgregarBD())
        {
            $mensaje = ['OK' => 'El Socio se registro correctamente: <br>'.$unSocio->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;  
        $mensaje = 'no se pudo dar modificar';

        if(Usuario::ModificarUnoBD($data['id'],$data['email'],$data['clave'],$data['nombre'] ,
        $data['apellido'],$data['dni'],$unCargo->GetId()))
        {
            $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'El Socio se modifico correctamente: <br>'.$unUsuario->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo borrar';

        if(Usuario::BorrarUnoPorIdBD($data['id']))
        {
            $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'Este Socio se borro correctamente: <br>'.$unUsuario->ToString();
        }

        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }

}

?>
