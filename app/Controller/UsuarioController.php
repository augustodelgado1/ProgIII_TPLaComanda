

<?php

require_once './Clases/Usuario.php';
require_once "./Herramientas/AutentificadorJWT.php";


class UsuarioController
{
    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Usuario no exixtente';

        if(isset($data) )
        {
            $unUsuario = Usuario::ObtenerUnoPorLoggin($data['email'],$data['clave']);
             
            if(isset($unUsuario) && $unUsuario !== false &&
            $unUsuario['estado'] !==  Usuario::ESTADO_SUSPENDIDO 
            && $unUsuario['estado'] !==  Usuario::ESTADO_BORRADO)
            {
                AutentificadorJWT::CrearUnToken($unUsuario);
                $mensaje = 'el Usuario se logio perfectamente';
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }




}

?>
