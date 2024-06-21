

<?php

require_once './Clases/Usuario.php';

class SocioController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = 'Hubo un error  al intentar listar los Socio';
        $unRol = Rol::BuscarRolPorDescripcionBD('Socio');

        $listaDeSocios = Usuario::FiltrarPorRolBD( $unRol->GetId());

        if(isset($listaDeSocios))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeSocios) > 0)
            {
                $mensaje = Usuario::ToStringList($listaDeSocios);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;   
        $unRol = Rol::BuscarRolPorDescripcionBD('Socio');
        $mensaje = 'no se pudo dar de alta';

        $unSocio = new Usuario($data['email'],$data['clave'],$data['nombre'],
        $data['apellido'],$data['dni'],$unCargo->GetId(),$unRol->GetId());

        if($unSocio->AgregarBD())
        {
            $mensaje = 'El Socio se registro correctamente:<br>'.$unSocio->ToString();
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
            $mensaje = 'El Socio se registro correctamente';
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
            $mensaje = 'El Socio se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

}

?>
