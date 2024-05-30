

<?php

require_once './Clases/Empleado.php';
require_once './Clases/RolDeTrabajo.php';

class EmpleadoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Empleado';
        $unRolDeTrabajo = RolDeTrabajo::BuscarRolDeTrabajoPorNombreBD($data['rolDeTrabajo']) ;   
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Empleado::DarDeAltaUnEmpleado($data['email'],$data['clave'],$data['nombre'],$unRolDeTrabajo))
            {
                $mensaje = 'El Empleado se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorRolDeTrabajo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los empleados';
        $unRolDeTrabajo = RolDeTrabajo::BuscarRolDeTrabajoPorNombreBD($data['rolDeTrabajo']) ;       
        
        $listaDeEmpleados = Empleado::ObtenerListaPorRolBD($unRolDeTrabajo);


        if(isset($listaDeEmpleados))
        {
            $mensaje = Empleado::ToStringList($listaDeEmpleados);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
