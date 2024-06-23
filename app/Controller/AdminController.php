

<?php

require_once './Clases/Usuario.php';

class AdminController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = 'Hubo un error  al intentar listar los Admin';
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Admin');

        $listaDeAdmins = Usuario::FiltrarPorRolBD( $unRol->GetId());

        if(isset($listaDeAdmins))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeAdmins) > 0)
            {
                $mensaje = "Admins".'<br>'.Usuario::ToStringList($listaDeAdmins);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;   
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Admin');
        $mensaje = 'no se pudo dar de alta';

        $unAdmin = new Usuario($data['email'],$data['clave'],$data['nombre'],
        $data['apellido'],$data['dni'],$unCargo->GetId(),$unRol->GetId());

        if($unAdmin->AgregarBD())
        {
            $mensaje = 'El Admin se registro correctamente:<br>'.$unAdmin->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        
        $mensaje = 'no se pudo dar modificar';

        if(Usuario::ModificarUnoBD($data['id'],$data['email'],$data['clave'],$data['nombre'] ,
        $data['apellido'],$data['dni'],$data['cargo']))
        {
            $mensaje = 'El Admin se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo borrar';

        if(Usuario::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Admin se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

}

?>
