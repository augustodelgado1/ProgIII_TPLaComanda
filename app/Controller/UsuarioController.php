

<?php
use Slim\Psr7\Response;

require_once './Clases/Usuario.php';
require_once "./Herramientas/AutentificadorJWT.php";


class UsuarioController
{
    public static function Login($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje =  json_encode(array('Error' => 'Usuario no exixtente'),JSON_PRETTY_PRINT);
        $unUsuario = Usuario::BuscarPorLoggin($data['email'],$data['clave']);
    
        if(isset($unUsuario) && $unUsuario !== false)
        {
            $dataUsuario = Usuario::ObtenerUnoCompletoBD($unUsuario['id']);
            $token = AutentificadorJWT::CrearUnToken($dataUsuario);
            $mensaje = json_encode(array('JWT' =>  $token),JSON_PRETTY_PRINT);
        }
        

        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }

}

?>
