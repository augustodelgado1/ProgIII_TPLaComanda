

<?php

require_once './Clases/Mesa.php';

class MesaController extends Mesa
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Mesa';
         $codigoDeMesa =  Mesa::Alta();
        if(isset($codigoDeMesa ) &&  $codigoDeMesa !== false)
        {
            $unaMesa = Mesa::BuscarMesaPorCodigoBD($codigoDeMesa);
            $mensaje = 'la Mesa se dio de alta:'.'<br>'. $unaMesa->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function Listar($request, $response, array $args)
    {
        // $data = $request->getHeaders();
        $mensaje = 'Hubo un error  al intentar listar los Mesas';  
        $listaDeMesas = Mesa::ObtenerListaBD();

      

        if(isset($listaDeMesas))
        {
            $mensaje = Mesa::ToStringList($listaDeMesas);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
