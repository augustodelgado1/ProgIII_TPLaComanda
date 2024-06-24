

<?php

require_once './Clases/Rol.php';

class RolController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = 'Hubo un error  al intentar listar los Rol';
        $listaDeRoles =  Rol::ObternerListaBD();

        if(isset($listaDeRoles))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeRoles) > 0)
            {
                $mensaje = Rol::ToStringList($listaDeRoles);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo dar de alta';

        $unRol = new Rol($data['descripcion']);

        if($unRol->AgregarBD())
        {
            $mensaje = 'El Rol se registro correctamente:<br>'.$unRol->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        
        $mensaje = 'no se pudo dar modificar';

        if(Rol::ModificarUnoBD($data['id'],$data['descripcion']))
        {
            $unRol = Rol::BuscarRolPorIdBD($data['id']);
            $mensaje = 'El Rol se modifico correctamente <br>'.$unRol->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unRol = Rol::BuscarRolPorIdBD($data['id']);
        $mensaje = 'no se pudo borrar';

        if(Rol::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'Este Rol se borro correctamente: <br>'.$unRol->ToString();
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

}

?>
