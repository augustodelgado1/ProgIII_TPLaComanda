

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


        $response->getBody()->write("no");


        return $response;
    }




}

?>
