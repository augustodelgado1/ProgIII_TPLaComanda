<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once "./Clases/Usuario.php";

class ValidarRolesMiddleware
{
    private $listaDeRoles;
    private $idDeUsuario;
    private $mensajeDeError;
    public function __construct($listaDeRoles,$idDeUsuario,$mensajeDeError = null) 
    {
        $this->listaDeRoles = $listaDeRoles;
        $this->idDeUsuario = $idDeUsuario;
        $this->mensajeDeError = $mensajeDeError;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    {   
        $response = new Response();
        $unUsuario = Usuario::BuscarPorIdBD($this->idDeUsuario);
    
        if (in_array($unUsuario->GetRolDeUsuario(),$this->listaDeRoles)) 
        {
            $response = $handler->handle($request);
        } else 
        {
            $payload = $this->mensajeDeError;
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>