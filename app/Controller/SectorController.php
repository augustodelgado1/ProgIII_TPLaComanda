

<?php
require_once './Clases/Pedido.php';

class SectorController extends Sector
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Sector';

        if(isset($data) && isset($data['descripcion']))
        {
            $mensaje = 'no se pudo dar de alta';
           
            $unSector = new Sector();

            if($unSector->SetDescripcion($data['descripcion']) 
            && $unSector->AgregarBD())
            {
                $mensaje = 'El Sector se dio de alta';
            }
        }

        $response->getBody()->write($mensaje);
        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Mesas';  
        $listaDeSectores = Sector::ObternerListaBD();

        if(isset($listaDeSectores))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeSectores) > 0)
            {
                $mensaje = Sector::ToStringList($listaDeSectores);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPedidos($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Pedidos';  
     
        if(isset($data ) && isset($data['idDeSector']))
        {
            $listaDePedidos = Pedido::FiltrarPorIdDeSectorBD($data['idDeSector']);

            if(isset($listaDePedidos))
            {
                $mensaje = "la lista esta vacia";
                if(count($listaDePedidos) > 0)
                {
                    $mensaje = Pedido::ToStringList($listaDePedidos);
                }
            }
        }
       
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarEmpleados($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $unCargo = Cargo::BuscarCargoPorIdDeSectorBD($data['idDeSector']);
        $mensaje = 'el sector no existe';
        if(isset($unCargo))
        {
            $listaDeEmpleados = Empleado::FiltrarPorRolBD($unCargo);
            $mensaje = 'Hubo un error al intentar listar los Empleados';  

            if(isset($listaDeEmpleados))
            {
                $mensaje = "la lista esta vacia";
                if(count($listaDeEmpleados) > 0)
                {
                    $mensaje = Empleado::ToStringList($listaDeEmpleados);
                }
            }
            
        }

        $response->getBody()->write($mensaje);


        return $response;
    }



   
}

?>
