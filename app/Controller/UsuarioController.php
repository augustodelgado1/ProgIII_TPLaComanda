

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
            && $otroUsuario['id'] === $unUsuario['id'])
            {
                
                if($otroUsuario['rol'] !== 'Socio')
                {
                    $unUsuario = new Empleado($otroUsuario['email'],$otroUsuario['clave'],$otroUsuario['nombre'],
                    $otroUsuario['apellido'],$otroUsuario['dni'],$otroUsuario['cargo']);
                }
                else
                {
                    $unUsuario = new Socio($otroUsuario['email'],$otroUsuario['clave'],$otroUsuario['nombre'],
                    $otroUsuario['apellido'],$otroUsuario['dni']);
                }
            }
            

        }


        $response->getBody()->write("no");


        return $response;
    }




}

?>
