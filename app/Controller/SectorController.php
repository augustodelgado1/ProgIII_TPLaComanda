

<?php
require_once './Clases/Pedido.php';

class SectorController
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje =  ['Error'=> 'Ocurrio un Error inesperado al intentar dar de alta un Sector'];

        if(isset($data) && isset($data['descripcion']))
        {
            $mensaje = ['Error' => 'no se pudo dar de alta'];
           
            $unSector = new Sector($data['descripcion']);

            if($unSector->AgregarBD())
            {
                $mensaje = ['OK' => 'El Sector se dio de alta <br>'.$unSector->ToString()];
            }
        }

        $response->getBody()->write($mensaje);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
      
        $mensaje = ['Error'=> 'no se pudo modificar'];

        if(Sector::ModificarUnoBD($data['id'],$data['descripcion']))
        {
            $unSector = Sector::ObtenerUnoPorIdBD($data['id']);
            $mensaje = ['Ok'=> 'el Sector se modifico correctamente:'.$unSector->ToString()];
        }
        
        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $unSector = Sector::ObtenerUnoPorIdBD($data['id']);
        $mensaje = ['Error' => 'no se pudo dar de Borrar'];

        if(Sector::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = ['Ok' =>'Este Sector se borro correctamente: <br>'.$unSector->ToString()];
        }

        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }

    // b- Cantidad de operaciones de todos por sector
    public static function Listar($request, $response, array $args)
    {
        $mensaje = ['Error'=> 'Hubo un error  al intentar listar los Sectores'];   
        $listaDeSectores = Sector::ObternerListaBD();

        if(isset($listaDeSectores))
        {
            $mensaje = ['Error'=> "la lista esta vacia"];
            if(count($listaDeSectores) > 0)
            {
                $mensaje = Sector::ToStringList($listaDeSectores);
            }
        }

        $response->getBody()->write($mensaje);


        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListarCantidadDeTareasRealizadasPorSector($request, $response, array $args)
    {
        $mensaje = ['Error'=> 'Hubo un error  al intentar listar la cantidad de Tareas Realizadas Por Sector'];      

        $listaDeSectores = Sector::ObternerListaBD();
        
        if(isset($listaDeSectores))
        {
            $mensaje = ['Error'=> "la lista esta vacia"];
            if(count($listaDeSectores) > 0)
            {
                $mensaje = ['OK'=> Sector::MostrarCantidadDeOperaciones($listaDeSectores)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function ListarCantidadDeTareasRealizadasPorSectorPorCadaEmpleado($request, $response, array $args)
    {
        $mensaje = ['Error'=> 'Hubo un error  al intentar listar la cantidad de Tareas Realizadas Por Sector'];      

        $listaDeSectores = Sector::ObternerListaBD();
        
        if(isset($listaDeSectores))
        {
            $mensaje = ['Error'=> "la lista esta vacia"];
            if(count($listaDeSectores) > 0)
            {
                $mensaje = ['OK'=> Sector::MostrarCantidadDeOperacionesPorCadaEmpleado($listaDeSectores)];
            }
        }

        $response->getBody()->write(json_encode($mensaje));


        return $response->withHeader('Content-Type', 'application/json');
    }

    // c- Cantidad de operaciones de todos por sector, listada por cada empleado
    // public static function ListarEmpleados($request, $response, array $args)
    // {
    //     $mensaje = 'Hubo un error  al intentar listar los Mesas';  
    //     $listaDeSectores = Sector::ObternerListaBD();

    //     if(isset($listaDeSectores))
    //     {
    //         $mensaje = ['Error' => "la lista esta vacia"];
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
                $mensaje = ['Error' => "la lista esta vacia"];
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
