

<?php

require_once './Clases/Usuario.php';

class UsuarioController
{

    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unaVariable  = false;
        echo "en el controller";
        // if(isset($data) && $unaVariable == true)
        // {
        //     $unUsuario = Usuario::BuscarEmailUnUsuarioBD($data['email']);

           
        //     $mensaje = 'el mail no existe';
        //     if(isset($unUsuario))
        //     {
        //         $otroUsuario = Usuario::BuscarClaveUnUsuarioBD($data['clave']);
        //         $mensaje = 'la clave es incorrecta';
                
        //         if(isset($otroUsuario) && $unUsuario->Equals($otroUsuario))
        //         {
        //             $mensaje = 'Se logio Perfectamente';
        //         }
        //     }

        // }


        $response->getBody()->write("no");


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
        $mensaje = 'no se recibieron parametros';
        if(isset($data))
        {
            $unUsuario = new Usuario($data['email'],$data['clave'],$data['nombre'] ,$data['apellido'],$data['dni']);
            $mensaje = 'no se pudo dar de alta';

            // if($unUsuario->AgregarBD())
            // {
            //     $mensaje = 'El Usuario se registro correctamente';
            // }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'no se recibieron parametros';
        if(isset($data))
        {
            $unUsuario = new Usuario($data['email'],$data['clave'],$data['nombre'] ,$data['apellido'],$data['dni']);
            $mensaje = 'no se pudo dar de alta';

            // if($unUsuario->AgregarBD())
            // {
            //     $mensaje = 'El Usuario se registro correctamente';
            // }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Usuario::BorrarUnUsuarioPorDniBD($data['dni']))
        {
            $mensaje = 'El Usuario se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function SuspenderUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Usuario::SuspenderUnUsuarioPorDniBD($data['dni']))
        {
            $mensaje = 'El Usuario se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // public static function ControlDeFunciones($listaDeFunciones,$parametos)
    // {
    //     $retorno = false;
    //     if(isset($listaDeFunciones) && isset($parametos) )
    //     {
    //         $retorno = true;
    //         foreach($listaDeFunciones as $unaClave => $unaFuncion)
    //         {
    //             if(isset($unaFuncion) === false || key_exists($unaClave,$parametos) === false
    //             && $unaFuncion($parametos[$unaClave]) === false)
    //             {
    //                 $retorno = [$unaClave => $unaFuncion];
    //                 break;
    //             }
    //         }
    //     }
       


    //     return $retorno;
    // }
}

?>
