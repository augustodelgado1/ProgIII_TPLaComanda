

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';
class TipoDeProducto 
{
    private $id;
    private $nombre;
    private $idDeSector;
   
    public function __construct($nombre,$sector) {
        $this->SetNombre($nombre);
        $this->idDeSector = $sector;
    }

   


    private function SetSector($idDeSector)
    {
        $estado  = false;
        if(isset( $idDeSector))
        {
            $this->idDeSector = $idDeSector;
        }

        return $estado;
    }
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into TipoDeProducto (nombre,idDeSector) values (:nombre,:idDeSector)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$this->idDeSector,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ModificarUnoBD($id,$descripcion,$idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE TipoDeProducto as t
            SET `descripcion`= :descripcion,
            `idDeSector`= :idDeSector,
            Where t.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeTipoDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM TipoDeProducto as t where t.id = :id");
            $consulta->bindValue(':id',$idDeTipoDeProducto,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BuscarTipoDeProductoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unTipoDeProducto = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where t.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unTipoDeProducto = TipoDeProducto::CrearUnTipoDeProducto($data);
        }

        return $unTipoDeProducto;
    }

    public static function FiltrarTipoDeProductoPorSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where t.idDeSector = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= TipoDeProducto::CrearLista($data);
        }

        return $listaDeTipos;
    }

    private static function CrearUnTipoDeProducto($unArrayAsosiativo)
    {
        $unTipoDeProducto = null;
   
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unTipoDeProducto = new TipoDeProducto($unArrayAsosiativo['nombre'],$unArrayAsosiativo['idDeSector']);
            $unTipoDeProducto->SetId($unArrayAsosiativo['id']);
            $unTipoDeProducto->SetSector($unArrayAsosiativo['idDeSector']);
        }
        
        return $unTipoDeProducto ;
    }

    private static function CrearLista($data)
    {
        $listaDeTipoDeProducto = null;
        if(isset($data))
        {
            $listaDeTipoDeProducto = [];

            foreach($data as $unArray)
            {
                $unTipoDeProducto = TipoDeProducto::CrearUnTipoDeProducto($unArray);
                
                
                if(isset($unTipoDeProducto))
                {
                    array_push($listaDeTipoDeProducto,$unTipoDeProducto);
                }
            }
        }

        return   $listaDeTipoDeProducto;
    }


    public static function BuscarPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unTipoDeProducto = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where LOWER(t.nombre) = LOWER(:nombre)");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unTipoDeProducto = TipoDeProducto::CrearUnTipoDeProducto($consulta->fetch(PDO::FETCH_ASSOC));
       
        }

        return  $unTipoDeProducto;
    }

     public static function ObtenerIndicePorId($listaDeTipoDeProductos,$id)
    {
        $index = -1;
       
        if(isset($listaDeTipoDeProductos)  && isset($id))
        {
            $leght = count($listaDeTipoDeProductos); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeTipoDeProductos[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unTipoDeProducto)
    {
        $estado = false;
 
        if(isset($unTipoDeProducto))
        {
            $estado =  $unTipoDeProducto->id === $this->id;
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
    public function GetDescripcion()
    {
        return  $this->nombre;
    }
    public function GetSector()
    {
        return  $this->idDeSector;
    }

    public function GetId()
    {
        return  $this->id;
    }

    

    #Mostrar
    public static function ToStringList($listaDeTipoDeProductos)
    {
        $strLista = null; 

        if(isset($listaDeTipoDeProductos) )
        {
            $strLista = "TipoDeProductos".'<br>';
            foreach($listaDeTipoDeProductos as $unSector)
            {
                $strLista .= $unSector->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "nombre: ".$this->nombre.'<br>';
    }
        

    //  public static function EscribirJson($listaDeTipoDeProducto,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeTipoDeProducto))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeTipoDeProducto,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return TipoDeProducto::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeTipoDeProducto = null; 
    //      $unTipoDeProducto = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeTipoDeProducto = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unTipoDeProducto = TipoDeProducto::DeserializarUnTipoDeProductoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unTipoDeProducto))
    //              {
    //                  array_push($listaDeTipoDeProducto,$unTipoDeProducto);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeTipoDeProducto ;
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

    
   


   

    // public static function CompararPorclave($unTipoDeProducto,$otroTipoDeProducto)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unTipoDeProducto->clave,$otroTipoDeProducto->clave);

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

    // public static function BuscarTipoDeProductoPorId($listaDeTipoDeProducto,$id)
    // {
    //     $unaTipoDeProductoABuscar = null; 

    //     if(isset($listaDeTipoDeProducto) )
    //     {
    //         foreach($listaDeTipoDeProducto as $unaTipoDeProducto)
    //         {
    //             if($unaTipoDeProducto->id == $id)
    //             {
    //                 $unaTipoDeProductoABuscar = $unaTipoDeProducto; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaTipoDeProductoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unTipoDeProducto,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unTipoDeProducto = $unTipoDeProducto;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(TipoDeProducto::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarTipoDeProductoPorId($listaDeTipoDeProductos,$id)
    // {
    //     $unaTipoDeProductoABuscar = null; 

    //     if(isset($listaDeTipoDeProductos)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeTipoDeProductos as $unaTipoDeProducto)
    //         {
    //             if($unaTipoDeProducto->id == $id)
    //             {
    //                 $unaTipoDeProductoABuscar = $unaTipoDeProducto; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaTipoDeProductoABuscar;
    // }
  
    // public static function ToStringList($listaDeTipoDeProductos)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeTipoDeProductos) )
    //     {
    //         foreach($listaDeTipoDeProductos as $unaTipoDeProducto)
    //         {
    //             $strLista = $unaTipoDeProducto->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeTipoDeProducto,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeTipoDeProducto) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeTipoDeProducto as $unaTipoDeProducto)
    //          {
    //              if($unaTipoDeProducto::$fechaDeTipoDeProducto == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>