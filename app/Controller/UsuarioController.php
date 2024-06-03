

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
        $mensaje = 'no se recibieron parametros';
        // $listaDeFunciones = array('email' => [$unUsuario ,'SetEmail'], 'clave' =>[$unUsuario ,'SetClave'],
            // 'nombre' => [$unUsuario ,'SetNombre']
            // ,'apellido' =>  [$unUsuario ,'SetApellido']);
        // $mensajesError = [
        //     'email' => 'El email no es válido',
        //     'clave' => 'La clave no es válida',
        //     'nombre' => 'El nombre no es válido',
        //     'apellido' => 'El apellido es inválido'
        // ];
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

        //     $unUsuario = new Usuario();
        
        //     $mensaje = 'el email no es valido';
        //    if($unUsuario->SetEmail($data['email']))
        //    {
        //         $mensaje = 'la clave no es valida';

        //         if($unUsuario->SetClave($data['clave']))
        //         {
        //             $mensaje = 'el nombre no es valido';
        //             if($unUsuario->SetNombre($data['apellido']))
        //             {
        //                 $mensaje = "el apillido es invalido";
        //                 if($unUsuario->SetApellido($data['apellido']))
        //                 {
        //                     if($unUsuario->AgregarBD())
        //                     {
        //                         $mensaje = 'El Usuario se registro correctamente';
        //                     }
        //                 }
        //             }
        //         }
        //    }
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
