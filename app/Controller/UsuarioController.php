

<?php

require_once './Clases/Usuario.php';

class UsuarioController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un usario';
	
        if(isset( $data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Usuario::DarDeAltaUnUsuario($data['email'],$data['clave']))
            {
                $mensaje = 'El usuario se dio de alta';
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
            $mensaje = json_encode(array("Usuarios" => $listaDeUsuarios),JSON_PRETTY_PRINT);
        }
            
        


        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
