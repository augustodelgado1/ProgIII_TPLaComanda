

<?php

require_once './Clases/Cliente.php';

class ClienteController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Cliente';
   
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Cliente::DarDeAlta($data['email'],$data['clave'],$data['nombre'],$data['apellido']))
            {
                $mensaje = 'El Cliente se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorRolDeTrabajo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Clientes';
        
        $listaDeClientes = Cliente::ObternerListaBD();


        if(isset($listaDeClientes))
        {
            $mensaje = Cliente::ToStringList($listaDeClientes);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
