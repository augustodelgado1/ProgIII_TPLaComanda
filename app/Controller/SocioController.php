

<?php

require_once './Clases/Socio.php';

class SocioController
{

    public static function Listar($request, $response, array $args)
    {
        $mensaje = 'Hubo un error  al intentar listar los Socio';
        $listaDeSocios = Socio::ListarBD();
       
        if(isset($listaDeSocios))
        {
            $mensaje = "La lista esta vacia";
            if(count($listaDeSocios) > 0)
            {
                $mensaje = Socio::ToStringList($listaDeSocios);
            }
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
     
        $mensaje = 'no se pudo dar de alta';

        $unSocio = new Socio($data['email'],$data['clave'],
        $data['nombre'],
        $data['apellido'],$data['dni']);

        if($unSocio->AgregarBD())
        {
            $mensaje = 'El Socio se registro correctamente:<br>'.$unSocio->ToString();
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ModificarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        
        $mensaje = 'no se pudo dar modificar';

        if(Socio::ModificarUnoBD($data['id'],$data['email'],$data['clave'],$data['nombre'] ,
        $data['apellido'],$data['dni']))
        {
            $mensaje = 'El Socio se registro correctamente';
        }
        
        $response->getBody()->write($mensaje);


        return $response;
    }
    public static function BorrarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
       
        $mensaje = 'no se pudo borrar';

        if(Socio::BorrarUnoPorIdBD($data['id']))
        {
            $mensaje = 'El Socio se borro correctamente';
        }

        $response->getBody()->write($mensaje);


        return $response;
    }

}

?>
