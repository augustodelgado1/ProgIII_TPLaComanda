

<?php

require_once './Clases/Usuario.php';

class UsuarioController 
{

    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Ingrese el mail y la clave';
        
        if(isset($data))
        {
            $unUsuario = Usuario::BuscarEmailUnUsuarioBD($data['email']);
           
            $mensaje = 'el mail no existe';
            if(isset($unUsuario))
            {
                $otroUsuario = Usuario::BuscarClaveUnUsuarioBD($data['clave']);
                $mensaje = 'la clave es incorrecta';
                
                if(isset($otroUsuario) && $unUsuario->Equals($otroUsuario))
                {
                    $mensaje = 'Se logio Perfectamente';
                }
            }

        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        
        $mensaje = 'Hubo un error  al intentar listar los usuario';
        $listaDeUsuarios = Usuario::ObtenerListaDeUsuarios();

        
        if(isset($listaDeUsuarios))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeUsuarios) > 0)
            {
                $mensaje = Usuario::ToStringList($listaDeUsuarios);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorRol($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        $mensaje = 'Hubo un error  al intentar listar los usuario';
        $listaDeUsuarios = Usuario::FiltrarPorRolBD($data['rol']);

        
        if(isset($listaDeUsuarios))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeUsuarios) > 0)
            {
                $mensaje = Usuario::ToStringList($listaDeUsuarios);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Usuario';
   
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Usuario::DarDeAlta($data['email'],$data['clave'],$data['nombre'],$data['apellido'],$data['rol']))
            {
                $mensaje = 'El Usuario se registro correctamente';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
