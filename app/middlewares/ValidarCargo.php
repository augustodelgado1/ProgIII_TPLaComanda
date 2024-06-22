<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidarCargo
{
    private $listaDeCargos;
    public function __construct($listaDeCargos) 
    {
        $this->listaDeCargos = $listaDeCargos;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    { 
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = (array) AutentificadorJWT::ObtenerData($token);
        
        // var_dump(  $data);
        $response = new Response();

        if(isset($data)  && in_array($data["cargo"], $this->listaDeCargos))
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