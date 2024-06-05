

<?php

require_once './Clases/Empleado.php';
require_once './Clases/Cargo.php';
require_once './Clases/Pedido.php';

class EmpleadoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Empleado';
        $unCargo = Cargo::BuscarCargoPorDescripcionBD($data['cargo']) ;   
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Empleado::DarDeAltaUnEmpleado($data['email'],$data['clave'],$data['nombre'],$data['apellido'],$unCargo))
            {
                $mensaje = 'El Empleado se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Clientes';
        
        $listaDeEmpleados = Empleado::ObternerListaBD();


        if(isset($listaDeEmpleados))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeEmpleados) > 0)
            {
                $mensaje = Empleado::ToStringList($listaDeEmpleados);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPedidosPendientes($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $mensaje = 'Hubo un error al intentar listar los Pedidos';
        $unEmpleado = Empleado::ObtenerUnoPorIdBD($data['idDeEmpeado']);
       
        if(isset($unEmpleado))
        {
            $listaDePedidos = $unEmpleado->ObtenerListaDePedidos();
            $listaDePedidosPendientes = Pedido::FiltrarPorEstado($listaDePedidos,"pendiente");

            if(isset($listaDePedidosPendientes))
            {
                $mensaje = "la lista esta vacia";
                if(count($listaDePedidosPendientes) > 0)
                {
                    $mensaje = Pedido::ToStringList($listaDePedidosPendientes);
                }
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorRolDeTrabajo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los empleados';
        $unCargo= Cargo::BuscarCargoPorDescripcionBD($data['cargo']) ;       
        
        $listaDeEmpleados = Empleado::ObtenerListaPorCargoBD($unCargo);
        

        if(isset($listaDeEmpleados))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeEmpleados) > 0)
            {
                $mensaje = Empleado::ToStringList($listaDeEmpleados);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    
}

?>
