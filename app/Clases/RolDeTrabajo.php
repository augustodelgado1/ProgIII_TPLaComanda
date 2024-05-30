

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class RolDeTrabajo 
{
    private $id;
    private $trabajo;
    private $idDeSector;

   
    public function __construct($id,$trabajo,$idDeSector) {
        $this->id = $id;
        $this->trabajo = $trabajo;
        $this->idDeSector = $idDeSector;
    }
    private function SetSector($idDeSector)
    {
        $unSector =  Sector::BuscarSectorPorIdBD($idDeSector);
        $estado  = false;
        if(isset( $unSector))
        {
            $this->idDeSector = $idDeSector;
        }

        return $estado;
    }
   
    public static function BuscarRolDeTrabajoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $rolDeTrabajo = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM RolDeTrabajo as r where r.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $rolDeTrabajo = $consulta->fetch(PDo::FETCH_ASSOC);
            $rolDeTrabajo =  new RolDeTrabajo($rolDeTrabajo['id'],$rolDeTrabajo['trabajo'],$rolDeTrabajo['idDeSector']);
        }

        return $rolDeTrabajo;
    }

    public static function BuscarRolDeTrabajoPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM RolDeTrabajo as r where r.trabajo = :trabajo");
            $consulta->bindValue(':trabajo',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unSector = $consulta->fetch(PDO::FETCH_ASSOC);
            $unSector =  new RolDeTrabajo($unSector['id'],$unSector['trabajo'],$unSector['idDeSector']);
         
        }

        return  $unSector;
    }
  
     public static function ObtenerIndicePorId($listaDeRolDeTrabajos,$id)
    {
        $index = -1;
       
        if(isset($listaDeRolDeTrabajos)  && isset($id))
        {
            $leght = count($listaDeRolDeTrabajos); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeRolDeTrabajos[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function Equals($unRolDeTrabajo)
    {
        $estado = false;
 
        if(isset($unRolDeTrabajo))
        {
            $estado =  $unRolDeTrabajo->id === $this->id;
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
        return  $this->trabajo;
    }

   
    

    //  public static function EscribirJson($listaDeRolDeTrabajo,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeRolDeTrabajo))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeRolDeTrabajo,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return RolDeTrabajo::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeRolDeTrabajo = null; 
    //      $unRolDeTrabajo = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeRolDeTrabajo = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unRolDeTrabajo = RolDeTrabajo::DeserializarUnRolDeTrabajoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unRolDeTrabajo))
    //              {
    //                  array_push($listaDeRolDeTrabajo,$unRolDeTrabajo);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeRolDeTrabajo ;
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

    
   


   

    // public static function CompararPorclave($unRolDeTrabajo,$otroRolDeTrabajo)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unRolDeTrabajo->clave,$otroRolDeTrabajo->clave);

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

    // public static function BuscarRolDeTrabajoPorId($listaDeRolDeTrabajo,$id)
    // {
    //     $unaRolDeTrabajoABuscar = null; 

    //     if(isset($listaDeRolDeTrabajo) )
    //     {
    //         foreach($listaDeRolDeTrabajo as $unaRolDeTrabajo)
    //         {
    //             if($unaRolDeTrabajo->id == $id)
    //             {
    //                 $unaRolDeTrabajoABuscar = $unaRolDeTrabajo; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaRolDeTrabajoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unRolDeTrabajo,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unRolDeTrabajo = $unRolDeTrabajo;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(RolDeTrabajo::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarRolDeTrabajoPorId($listaDeRolDeTrabajos,$id)
    // {
    //     $unaRolDeTrabajoABuscar = null; 

    //     if(isset($listaDeRolDeTrabajos)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeRolDeTrabajos as $unaRolDeTrabajo)
    //         {
    //             if($unaRolDeTrabajo->id == $id)
    //             {
    //                 $unaRolDeTrabajoABuscar = $unaRolDeTrabajo; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaRolDeTrabajoABuscar;
    // }
  
    // public static function ToStringList($listaDeRolDeTrabajos)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeRolDeTrabajos) )
    //     {
    //         foreach($listaDeRolDeTrabajos as $unaRolDeTrabajo)
    //         {
    //             $strLista = $unaRolDeTrabajo->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeRolDeTrabajo,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeRolDeTrabajo) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeRolDeTrabajo as $unaRolDeTrabajo)
    //          {
    //              if($unaRolDeTrabajo::$fechaDeRolDeTrabajo == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>