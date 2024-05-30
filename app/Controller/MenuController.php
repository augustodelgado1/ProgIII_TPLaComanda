

<?php

require_once './Clases/Menu.php';
require_once './Clases/RolDeTrabajo.php';

class MenuController 
{
  
    public static function CargarUno($request, $response, array $args)
    {
        $data = $request->getParsedBody();
        $mensaje = 'Hubo un error con los parametros al intentar dar de alta un Menu';
        $categotia = Categoria::BuscarPorNombreBD($data['categotia']) ;   
        if(isset($data))
        {
            $mensaje = 'no se pudo dar de alta';

            if(Menu::DarDeAltaUnMenu($data['email'],$data['clave'],$data['nombre'],$unRolDeTrabajo))
            {
                $mensaje = 'El Menu se dio de alta';
            }
        }


        $response->getBody()->write($mensaje);


        return $response;
    }

    public static function ListarPorRolDeTrabajo($request, $response, array $args)
    {
        $data = $request->getQueryParams();
        
        $mensaje = 'Hubo un error  al intentar listar los Menus';
        $unRolDeTrabajo = RolDeTrabajo::BuscarRolDeTrabajoPorNombreBD($data['rolDeTrabajo']) ;       
        
        $listaDeMenus = Menu::ObtenerListaPorRolBD($unRolDeTrabajo);


        if(isset($listaDeMenus))
        {
            $mensaje = Menu::ToStringList($listaDeMenus);
        }

        $response->getBody()->write($mensaje);


        return $response;
    }
}

?>
