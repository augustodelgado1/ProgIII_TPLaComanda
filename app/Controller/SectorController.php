

<?php
require_once './Clases/Pedido.php';

class SectorController
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Sector';

        if(isset($data) && isset($data['descripcion']))
        {
            $mensaje = 'no se pudo dar de alta';
           
            $unSector = new Sector($data['descripcion']);

            if($unSector->AgregarBD())
            {
                $mensaje = 'El Sector se dio de alta <br>'.$unSector->ToString();
            }
        }

        $response->getBody()->write($mensaje);
        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo modificar';

        if(Sector::ModificarUnoBD($data['id'],$data['descripcion']))
        {
            $mensaje = 'el Sector se modifico correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();

        $mensaje = 'no se pudo dar de alta';

        if(Sector::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'el Sector se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    // b- Cantidad de operaciones de todos por sector
    public static function Listar($request, $response, array $args)
    {
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

    // c- Cantidad de operaciones de todos por sector, listada por cada empleado
    // public static function ListarEmpleados($request, $response, array $args)
    // {
    //     $mensaje = 'Hubo un error  al intentar listar los Mesas';  
    //     $listaDeSectores = Sector::ObternerListaBD();

    //     if(isset($listaDeSectores))
    //     {
    //         $mensaje = "La lista esta vacia";
    //         if(count($listaDeSectores) > 0)
    //         {
    //             $mensaje = Sector::MostrarListaDeEmpleados($listaDeSectores);
    //         }
    //     }

    //     $response->getBody()->write($mensaje);


    //     return $response;
    // }

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



   
}

?>
