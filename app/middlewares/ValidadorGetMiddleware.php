<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidadorGetMiddleware
{
    private $funcValidador;
    private $mensajeDeError;
    public function __construct($funcValidador,$mensajeDeError = null) 
    {
        $this->funcValidador = $funcValidador;
        $this->mensajeDeError = $mensajeDeError;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    {    
        $parametros = $request->getQueryParams();

        $response = new Response();

        
        
        if(is_callable($this->funcValidador))
        {
            if (call_user_func($this->funcValidador,$parametros) == true) {
                $response = $handler->handle($request);
            } else {
                $payload = $this->mensajeDeError;
                $response->getBody()->write($payload);
            }
        }
        

        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>