

<?php

require_once './Clases/Orden.php';
require_once './Clases/Mesa.php';
require_once './Clases/Cliente.php';
require_once './Clases/Usuario.php';
require_once './Clases/File.php';

class OrdenController extends Orden
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo error con los parametros al intentar dar de alta un Orden';
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        // $unCliente = Usuario::BuscarPorIdBD($data['idDeCLiente']);
        $unaOrden = new Orden();
        File::CrearUnDirectorio('Imagenes');
        File::CrearUnDirectorio('Imagenes/Mesa');
        $unCliente = null;
        if($unaOrden->SetMesa($unaMesa) &&
          $unaOrden->SetCliente($unCliente))
        {
            $unaOrden->GuardarImagen($_FILES['imagen']['tmp_name'],"Imagenes/Mesa/",$_FILES['imagen']['name']) ;
            $mensaje = 'la Orden no se pudo dar de alta';
            if($unaOrden->DarDeAlta())
            {
                $mensaje = 'la Orden se dio de alta <br>'.$unaOrden->ToString();
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Ordens';  
        $listaDeOrdens = Orden::ListarBD();

      

        if(isset($listaDeOrdens))
        {
            $mensaje = "la lista esta vacia";
            if(count($listaDeOrdens) > 0)
            {
                $mensaje = Orden::ToStringList($listaDeOrdens);
            }
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPedidos($request, $response, array $args)
    {
        $data = $request->getHeaders();
        $mensaje = 'el numero de orden es incorrecto';  
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);

    
        if(isset($unaOrden))
        {
            $listaDePedidos =  $unaOrden->ObtenerListaDePedidos();
            $mensaje = 'Hubo un error  al intentar listar pedidos'; 
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
