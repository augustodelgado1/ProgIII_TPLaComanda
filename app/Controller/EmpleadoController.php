

<?php

require_once './Clases/Usuario.php';

class EmpleadoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Empleado';

        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Empleado::DarDeAltaUnEmpleado($data['email'],$data['clave'],$data['nombre'],$data['sector']))
            {
                $mensaje = 'El Empleado se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorSector($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los usuario';
	
      
        $listaDeEmpleados = Empleado::ObtenerListaPorSectorBD($data['sector']);


        if(isset($listaDeEmpleados))
        {
            $mensaje = json_encode(array("Usuarios" => $listaDeEmpleados),JSON_PRETTY_PRINT);
        }
            
        


        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
