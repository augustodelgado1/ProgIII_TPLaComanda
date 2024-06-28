

<?php

require_once './Clases/LogAudutoria.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthLogMiddleware
{
    private $descripcion;

    public function __construct($descripcion) {
        $this->descripcion = $descripcion;
    }
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $fechaDeEntrada = new DateTime('now');
        $response = new Response();
        $response = $handler->handle($request);
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = (array) AutentificadorJWT::ObtenerData($token);
       
        if(isset($data) && isset($data['id']))
        {
            $logDeAuditoria = new LogDeAuditoria($data['id'],$this->descripcion,$fechaDeEntrada);
            $logDeAuditoria->AgregarBD();
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>