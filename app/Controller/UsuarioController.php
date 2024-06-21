

<?php

require_once './Clases/Usuario.php';
require_once "./Herramientas/AutentificadorJWT.php";


class UsuarioController
{
    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje =  json_encode(array('Error' => 'Usuario no exixtente'),JSON_PRETTY_PRINT);
        $unUsuario = Usuario::ObtenerUnoPorLoggin($data['email'],$data['clave']);
            
        if(isset($unUsuario) && $unUsuario !== false &&
        $unUsuario['estado'] !==  Usuario::ESTADO_SUSPENDIDO 
        && $unUsuario['estado'] !==  Usuario::ESTADO_BORRADO)
        {
            if($unUsuario['rol'] === 'Empleado')
            {
                $dataArray = Empleado::ObtenerUnoPorIdDeUsuario($unUsuario['id']);
             
            }
            else
            {
                $dataArray = Socio::ObtenerUnoPorIdDeUsuario($unUsuario['id']);
            }
            
            $token = AutentificadorJWT::CrearUnToken($dataArray);
            $mensaje = json_encode(array('JWT' =>  $token),JSON_PRETTY_PRINT);
        }
        

        $response->getBody()->write($mensaje);


        return $response;
    }
    




}

?>
