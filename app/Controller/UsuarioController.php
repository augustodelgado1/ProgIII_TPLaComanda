

<?php

require_once './Clases/Usuario.php';

class UsuarioController
{

    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        if(isset($data) )
        {
            $unUsuario = Usuario::BuscarEmailUnUsuarioBD($data['email']);
             $otroUsuario = Usuario::BuscarClaveUnUsuarioBD($data['clave']);

            if(isset($unUsuario) && isset($otroUsuario) &&
             $otroUsuario['email'] === $unUsuario['email'] 
            && $otroUsuario['clave'] === $unUsuario['clave']
            && $otroUsuario['id'] === $unUsuario['id']
            && $otroUsuario['estado'] !==  Usuario::ESTADO_SUSPENDIDO 
            && $otroUsuario['estado'] !==  Usuario::ESTADO_BORRADO)
            {
                
               
            }
            

        }


        $response->getBody()->write("no");


        return $response;
    }




}

?>
