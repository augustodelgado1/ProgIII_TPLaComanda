<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarRoles
{
    private $listaDeRoles;
    public function __construct($listaDeRoles) 
    {
        $this->listaDeRoles = $listaDeRoles;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    { 
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = (array) AutentificadorJWT::ObtenerData($token);
        
       
        $response = new Response();

        if(isset($data)  && in_array($data["rol"], $this->listaDeRoles))
        {
            $response = $handler->handle($request);
        }else{
            $mensajeDeError = json_encode(array('Error','Error Usted No tiene Permisos para acceder a esta seccion'));
            $response->getBody()->write($mensajeDeError);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
   
}


?>