

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class Menu 
{
    private $id;
    private $nombre;
    private $idDeCategoria;
    private $tiempoDePreparacion;
    private $precio;

   
    public function __construct($id,$nombre,$idDeCategoria) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->idDeCategoria = $idDeCategoria;
    }
    private function SetSector($idDeCategoria)
    {
        $unSector =  Sector::BuscarSectorPorIdBD($idDeCategoria);
        $estado  = false;
        if(isset( $unSector))
        {
            $this->idDeCategoria = $idDeCategoria;
        }

        return $estado;
    }
   
    public static function BuscarMenuPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $Menu = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Menu as r where r.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $Menu = $consulta->fetch(PDo::FETCH_ASSOC);
            $Menu =  new Menu($Menu['id'],$Menu['nombre'],$Menu['idDeCategoria']);
        }

        return $Menu;
    }

    public static function BuscarMenuPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Menu as r where r.nombre = :nombre");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unSector = $consulta->fetch(PDO::FETCH_ASSOC);
            $unSector =  new Menu($unSector['id'],$unSector['nombre'],$unSector['idDeCategoria']);
         
        }

        return  $unSector;
    }
  
     public static function ObtenerIndicePorId($listaDeMenus,$id)
    {
        $index = -1;
       
        if(isset($listaDeMenus)  && isset($id))
        {
            $leght = count($listaDeMenus); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeMenus[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function Equals($unMenu)
    {
        $estado = false;
 
        if(isset($unMenu))
        {
            $estado =  $unMenu->id === $this->id;
        }
        return  $estado ;
    }

    //Setters
    private function SetId($id)
    {
        $estado = false;
        if(isset($id))
        {
            $this->id = $id;
            $estado = true;
        }

        return  $estado ;
    }

   

    //Getters

    public function GetId()
    {
        return  $this->id;
    }
    public function GetNombre()
    {
        return  $this->nombre;
    }

   
    

    //  public static function EscribirJson($listaDeMenu,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeMenu))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeMenu,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Menu::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeMenu = null; 
    //      $unMenu = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeMenu = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unMenu = Menu::DeserializarUnMenuPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unMenu))
    //              {
    //                  array_push($listaDeMenu,$unMenu);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeMenu ;
    //  }

    


    // public function SetCuponDeDescuento($cuponDeDescuento)
    // {
    //     $estado = false;
    //     if(isset($cuponDeDescuento))
    //     {
    //         $this->cuponDeDescuento = $cuponDeDescuento;
    //         $estado = true;
    //     }

    //     return  $estado ;
    // }

    // public function GetCuponDeDescuento()
    // {
    //     return  $this->cuponDeDescuento;
    // }

    
   


   

    // public static function CompararPorclave($unMenu,$otroMenu)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unMenu->clave,$otroMenu->clave);

    //     if( $comparacion  > 0)
    //     {
    //         $retorno = 1;
    //     }else{

    //         if( $comparacion < 0)
    //         {
    //             $retorno = -1;
    //         }
    //     }

    //     return $retorno ;
    // }

    // public static function BuscarMenuPorId($listaDeMenu,$id)
    // {
    //     $unaMenuABuscar = null; 

    //     if(isset($listaDeMenu) )
    //     {
    //         foreach($listaDeMenu as $unaMenu)
    //         {
    //             if($unaMenu->id == $id)
    //             {
    //                 $unaMenuABuscar = $unaMenu; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaMenuABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unMenu,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unMenu = $unMenu;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Menu::ObtenerIdAutoIncremental());
    //     $this->SetImagen($ruta,$claveDeLaImagen);
    // }
    
   

   
    


    // public function CambiarRutaDeLaImagen($nuevaRuta)
    // {
    //     $estado = false;

    //     if(rename($this->rutaDeLaImagen.$this->claveDeLaImagen,$nuevaRuta.$this->claveDeLaImagen))
    //     {
    //         $this->rutaDeLaImagen = $nuevaRuta;
    //         $estado = true;
    //     }

    //     return $estado;
    // }

   

    // public static function BuscarMenuPorId($listaDeMenus,$id)
    // {
    //     $unaMenuABuscar = null; 

    //     if(isset($listaDeMenus)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeMenus as $unaMenu)
    //         {
    //             if($unaMenu->id == $id)
    //             {
    //                 $unaMenuABuscar = $unaMenu; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaMenuABuscar;
    // }
  
    // public static function ToStringList($listaDeMenus)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeMenus) )
    //     {
    //         foreach($listaDeMenus as $unaMenu)
    //         {
    //             $strLista = $unaMenu->ToString().'<br>';
    //         }
    //     }

    //     return   $strLista;
    // }

//Filtrar

    // public static function FiltrarPizzaPorTipo($listaDePizzas,$tipo)
    // {
    //     $listaDeTipoDePizza = null;

    //     if(isset($listaDePizzas) && isset($tipo) && count($listaDePizzas) > 0)
    //     {
    //         $listaDeTipoDePizza =  [];

    //         foreach($listaDePizzas as $unaPizza)
    //         {
    //             if($unaPizza->tipo == $tipo)
    //             {
    //                 array_push($listaDeTipoDePizza,$unaPizza);
    //             }
    //         }
    //     }

    //     return  $listaDeTipoDePizza;
    // }


     //  //Contar
 
    //  public static function ContarPorUnaFecha($listaDeMenu,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeMenu) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeMenu as $unaMenu)
    //          {
    //              if($unaMenu::$fechaDeMenu == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>