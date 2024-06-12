

<?php

require_once './Clases/Empleado.php';
require_once './Clases/Cargo.php';
require_once './Clases/Pedido.php';

// 7- De los empleados:
// a- Los días y horarios que se ingresaron al sistema.
// b- Cantidad de operaciones de todos por sector.
// c- Cantidad de operaciones de todos por sector, listada por cada empleado.
// d- Cantidad de operaciones de cada uno por separado.
// e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos.


class EmpleadoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Empleado';
        $unCargo = Cargo::BuscarCargoPorDescripcionBD($data['cargo']) ;   
        if(isset($data))
        {
            $unEmpleado = new Empleado($data['email'],$data['clave'],$data['nombre'],$data['apellido'],$data['dni'],$unCargo->GetId());

            if($unEmpleado->AgregarBD())
            {
                $mensaje = 'El Empleado se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::BuscarCargoPorDescripcionBD($data['cargo']) ;  
        $mensaje = 'no se pudo dar modificar';

        if(Empleado::ModificarUnEmpleadoBD($data['id'],$data['email'],$data['clave'],$data['nombre'],
        $data['apellido'],$data['dni'],$unCargo))
        {
            $mensaje = 'El Socio se registro correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Empleado::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function SuspenderUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'no se pudo dar de alta';

        if(Empleado::SuspenderUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Usuario se registro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
     // d- Cantidad de operaciones de cada uno por separado.

    public static function Listar($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Clientes';
        
        $listaDeEmpleados = Empleado::ListarBD();


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
    public static function ListarSuspendidos($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Clientes';
        
        $listaDeEmpleados = Empleado::FiltrarPorEstadoBD(Usuario::ESTADO_SUSPENDIDO);

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
    public static function ListarBorrados($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Clientes';
        
        $listaDeEmpleados = Empleado::FiltrarPorEstadoBD(Usuario::ESTADO_BORRADO);


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
        $unEmpleado = Empleado::BuscarPorIdBD($data['idDeEmpeado']);
       
        if(isset($unEmpleado))
        {
            $listaDePedidos = $unEmpleado->GetSector()->ObtenerListaDePedidos();
            $listaDePedidosPendientes = Pedido::FiltrarPorEstado($listaDePedidos,Pedido::ESTADO_INICIAL);

            if(isset($listaDePedidosPendientes))
            {
                $mensaje = "No se encontraron pedidos pendientes";
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
        
        $listaDeEmpleados = Empleado::FiltrarPorCargoBD($unCargo->GetId());
        

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

  

    // e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos
}

?>
