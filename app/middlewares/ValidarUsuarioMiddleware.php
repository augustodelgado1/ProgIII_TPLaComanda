<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidarUsuarioMiddleware
{
    private $unUsuario;
    private $funcValidadora;
    private $mensajeDeError;
    public function __construct($unUsuario,$funcValidadora,$mensajeDeError = null) 
    {
        $this->unUsuario = $unUsuario;
        $this->funcValidadora = $funcValidadora;
        $this->mensajeDeError = $mensajeDeError;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    {   
        $response = new Response();
      
        if(is_callable($this->funcValidadora))
        {
           
            
            if (call_user_func($this->funcValidadora,$this->unUsuario) == true) {
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