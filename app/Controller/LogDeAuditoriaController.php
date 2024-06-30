

<?php

require_once './Clases/LogAudutoria.php';



class LogDeAuditoriaController 
{
    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = ['Error'=>'Hubo un error  al intentar listar los Mesas'];  
        $listaDeLogDeAuditoria = LogDeAuditoria::ListarBD();

        if(isset($listaDeLogDeAuditoria))
        {
            $mensaje =['OK'=> LogDeAuditoria::ToStringList($listaDeLogDeAuditoria)];
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');;
    }

   

    
   

   
}

?>
