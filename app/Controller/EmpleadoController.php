

<?php

require_once './Clases/Usuario.php';
require_once './Clases/Cargo.php';
require_once './Clases/Pedido.php';

// 7- De los empleados:
// a- Los días y horarios que se ingresaron al sistema.
// b- Cantidad de operaciones de todos por sector.
// c- Cantidad de operaciones de todos por sector, listada por cada empleado.
// d- Cantidad de operaciones de cada uno por separado.
// e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos.

// ❏ 3- Cada empleado responsable de cada producto del pedido , debe:
// ❏ Listar todos los productos pendientes de este tipo de empleado.
// ❏ Debe cambiar el estado a “en preparación” y agregarle el tiempo de preparación.


class EmpleadoController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = ['Error' => 'Hubo un error con los parametros al intentar dar de alta un Empleado'];
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;   
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        if(isset($data))
        {
            $unEmpleado = new Usuario($data['email'],$data['clave'],$data['nombre'],$data['apellido'],
            $data['dni'], $unCargo->GetId(),$unRol->GetId());
           
            if($unEmpleado->AgregarBD())
            {
                $mensaje = ['OK'=> 'El Empleado se dio de alta: <br>'.
                $unEmpleado->ToString()];
            }
        }


        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unCargo = Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;  
        $mensaje =  ['Error' =>'no se pudo dar modificar'];

        if(Usuario::ModificarUnoBD($data['id'],$data['email'],$data['clave'],$data['nombre'],
        $data['apellido'],$data['dni'],$unCargo->GetId()))
        {
            $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
            $mensaje = ['OK'=> 'El Empleado se modifico correctamente: <br>'. $unUsuario->ToString()];
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
     
        $mensaje = 'no se pudo dar de alta';

        if(Usuario::BorrarUnoPorIdBD($data['id']))
        {
            $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'Este Empleado se borro correctamente: <br>'. $unUsuario->ToString();
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    public static function SuspenderUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'no se pudo suspender';

        if(Usuario::SuspenderUnoPorIdBD($data['id']))
        {
            $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
            $mensaje = 'El Empleado se suspendio correctamente <br>'.$unUsuario->ToString();
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
     // d- Cantidad de operaciones de cada uno por separado.

    public static function Listar($request, $response, array $args)
    {
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los Socio'];
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        $listaDeEmpleados = Usuario::FiltrarPorRolBD( $unRol->GetId());
        $listaFiltrada = Usuario::FiltrarPorEstado($listaDeEmpleados,Usuario::ESTADO_ACTIVO);
 
        if(isset($listaFiltrada))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaFiltrada) > 0)
            {
                $mensaje = 'Empleados:'.'<br>'.
                Usuario::ToStringList($listaFiltrada);
            }
        }
        
        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ListarSuspendidos($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los Empleados Suspendidos'];
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        $listaDeEmpleados = Usuario::FiltrarPorRolBD( $unRol->GetId());
        $listaFiltrada = Usuario::FiltrarPorEstado($listaDeEmpleados,Usuario::ESTADO_SUSPENDIDO);

        if(isset($listaFiltrada))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaFiltrada) > 0)
            {
                $mensaje = 'Empleados:'.'<br>'.
                Usuario::ToStringList($listaFiltrada);
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ListarBorrados($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los Empleados Borrados'];
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        $listaDeEmpleados = Usuario::FiltrarPorRolBD( $unRol->GetId());
        $listaFiltrada = Usuario::FiltrarPorEstado($listaDeEmpleados,Usuario::ESTADO_BORRADO);

        if(isset($listaFiltrada))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaFiltrada) > 0)
            {
                $mensaje = 'Empleados:'.'<br>'.
                Usuario::ToStringList($listaFiltrada);
            }
        }

        $response->getBody()->write(json_encode($mensaje));
    }

   
    // ❏ Listar todos los productos pendientes de este tipo de empleado.
    public static function ListarPedidosPendientes($request, $response, array $args)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = (array) AutentificadorJWT::ObtenerData($token);

        $mensaje = ['Error' => 'Hubo un error al intentar listar los Pedidos'];
        
        $unUsuario = Usuario::ObtenerUnoPorIdBD($data['id']);
       
       
        if(isset($unUsuario))
        {
            $listaDePedidos = $unUsuario->GetSector()->ObtenerListaDePedidos();
          
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

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    public static function ListarCantidadDeTareasRealizadas($request, $response, array $args)
    {
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los empleados'];      
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        $listaDeEmpleados = Usuario::FiltrarPorRolBD( $unRol->GetId());
        
        if(isset($listaDeEmpleados))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeEmpleados) > 0)
            {
                $mensaje = ['OK' => 'Empleados:'.'<br>'.
                Usuario::MostarCantidadDeOperaciones($listaDeEmpleados)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

    // c- Cantidad de operaciones de todos por sector, listada por cada empleado.
    public static function ListarCantidadDeTareasRealizadasPorSector($request, $response, array $args)
    {
        $mensaje = ['Error' =>  'Hubo un error  al intentar listar los empleados'];      

        $listaDeSectores = Sector::ObternerListaBD();
        
        if(isset($listaDeSectores))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaDeSectores) > 0)
            {
                $mensaje = ['OK' => Sector::MostrarCantidadDeOperaciones($listaDeSectores)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }
    public static function ListarPorCargo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = ['Error' => 'Hubo un error  al intentar listar los empleados'];
        $unCargo= Cargo::ObtenerUnoPorDescripcionBD($data['cargo']) ;       
        $unRol = Rol::ObtenerUnoPorDescripcionBD('Empleado');

        $listaDeEmpleados = Usuario::FiltrarPorRolBD( $unRol->GetId());
        
        $listaFiltrada = Usuario::FiltrarPorCargo($listaDeEmpleados ,$unCargo->GetId());
        

        if(isset($listaFiltrada))
        {
            $mensaje = ['Error' => "la lista esta vacia"];
            if(count($listaFiltrada) > 0)
            {
                $mensaje = ['OK' => 'Empleados:'.'<br>'.
                Usuario::ToStringList($listaFiltrada)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response;
    }

  

    // e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos
}

?>
