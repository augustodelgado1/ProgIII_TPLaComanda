

<?php

require_once './Clases/Rol.php';

class RolController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = ['Error' =>'Hubo un error  al intentar listar los Rol'];
        $listaDeRoles =  Rol::ObternerListaBD();

        if(isset($listaDeRoles))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeRoles) > 0)
            {
                $mensaje = ['Ok' =>Rol::ToStringList($listaDeRoles)];
            }
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = ['Error' =>'no se pudo dar de alta'];

        $unRol = new Rol($data['descripcion']);

        if($unRol->AgregarBD())
        {
            $mensaje = ['Ok' =>'El Rol se registro correctamente:<br>'.$unRol->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        
        $mensaje = ['Error' =>'no se pudo dar modificar'];

        if(Rol::ModificarUnoBD($data['id'],$data['descripcion']))
        {
            $unRol = Rol::BuscarRolPorIdBD($data['id']);
            $mensaje = ['Ok' =>'El Rol se modifico correctamente <br>'.$unRol->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unRol = Rol::BuscarRolPorIdBD($data['id']);
         $mensaje = ['Error' =>'no se pudo borrar'];

        if(Rol::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = ['Ok' =>'Este Rol se borro correctamente: <br>'.$unRol->ToString()];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

}

?>
