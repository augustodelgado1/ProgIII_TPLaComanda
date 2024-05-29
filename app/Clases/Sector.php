

<?php

require_once './db/AccesoDatos.php';

class Sector 
{
    private $id;
    private $nombre;
   
    public function __construct($nombre) {
        $this->SetNombre($nombre);
    }

    public static function DarDeAltaUnSector($nombre)
    {
        $estado = false;
        $unSector = new Sector($nombre);

        if(empty($unSector->nombre) == false )
        {
            $estado = $unSector->AgregarBD();
        }

        return $estado;
    }

    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos) && isset($idDeUsuario))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Sector (nombre) values (:nombre)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BuscarSectorPorId($listaDeSectors,$id)
    {
        $unaSectorABuscar = null; 
        $index = Sector::ObtenerIndicePorId($listaDeSectors,$id);
        if($index > 0 )
        {
            $unaSectorABuscar = $listaDeSectors[$index];
        }

        return  $unaSectorABuscar;
    }
    public static function BuscarSectorPorIdBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM sector as s where s.id == $idDeSector");
            $consulta->execute();
            $unSector = $consulta->fetchObject(__CLASS__,array('nombre'));
        }

        return  $unSector;
    }

     public static function ObtenerIndicePorId($listaDeSectors,$id)
    {
        $index = -1;
       
        if(isset($listaDeSectors)  && isset($id))
        {
            $leght = count($listaDeSectors); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeSectors[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unSector)
    {
        $estado = false;
 
        if(isset($unSector))
        {
            $estado =  $unSector->id === $this->id;
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

    public function SetNombre($nombre)
    {
        $estado = false;
        if(isset($nombre) )
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }

    //Getters
    public function GetNombre()
    {
        return  $this->nombre;
    }

    private static function ObtenerIdAutoIncremental()
    {
        return rand(1,10000);
    }

    //  public static function EscribirJson($listaDeSector,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeSector))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeSector,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Sector::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeSector = null; 
    //      $unSector = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeSector = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unSector = Sector::DeserializarUnSectorPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unSector))
    //              {
    //                  array_push($listaDeSector,$unSector);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeSector ;
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

    
   


   

    // public static function CompararPorclave($unSector,$otroSector)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unSector->clave,$otroSector->clave);

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

    // public static function BuscarSectorPorId($listaDeSector,$id)
    // {
    //     $unaSectorABuscar = null; 

    //     if(isset($listaDeSector) )
    //     {
    //         foreach($listaDeSector as $unaSector)
    //         {
    //             if($unaSector->id == $id)
    //             {
    //                 $unaSectorABuscar = $unaSector; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaSectorABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unSector,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unSector = $unSector;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Sector::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarSectorPorId($listaDeSectors,$id)
    // {
    //     $unaSectorABuscar = null; 

    //     if(isset($listaDeSectors)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeSectors as $unaSector)
    //         {
    //             if($unaSector->id == $id)
    //             {
    //                 $unaSectorABuscar = $unaSector; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaSectorABuscar;
    // }
  
    // public static function ToStringList($listaDeSectors)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeSectors) )
    //     {
    //         foreach($listaDeSectors as $unaSector)
    //         {
    //             $strLista = $unaSector->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeSector,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeSector) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeSector as $unaSector)
    //          {
    //              if($unaSector::$fechaDeSector == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>