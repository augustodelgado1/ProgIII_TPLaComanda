

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class Cargo 
{
    private $id;
    private $descripcion;
    private $idDeSector;


    public function __construct($descripcion,$unSector) {
        $this->descripcion = $descripcion;
        $this->idDeSector = $unSector;
    }
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Cargo (descripcion,idDeSector) 
            values (:descripcion,:idDeSector)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$this->idDeSector->GetId(),PDO::PARAM_INT);
            $consulta->execute();
            $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();
        }

        return $idDeEncuesta;
    }

    public static function ModificarUnoBD($id,$descripcion,$idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Cargo as c
            SET `descripcion`= :descripcion,
            `idDeSector`= :idDeSector,
            Where c.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Cargo as c where c.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
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
   
    public static function BuscarCargoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Cargo::CrearUnCargo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return $unRol;
    }
    public static function BuscarCargoPorIdDeSectorBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.idDeSector = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Cargo::CrearUnCargo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return $unRol;
    }

    public static function BuscarCargoPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where LOWER(c.descripcion) = LOWER(:descripcion)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $unRol = Cargo::CrearUnCargo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unRol;
    }

    public static function FiltrarPorSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.idDeSector = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= Cargo::CrearLista($data);
        }

        return $listaDeTipos;
    }

    public function ObtenerListaDeEmpleados()
    {
        return Empleado::FiltrarPorCargoBD($this->id);
    }


    private static function CrearUnCargo($unArrayAsosiativo)
    {
        $unCargo = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unCargo = new Cargo($unArrayAsosiativo['descripcion'],$unArrayAsosiativo['idDeSector']);
            $unCargo->SetId($unArrayAsosiativo['id']);
            $unCargo->SetSector($unArrayAsosiativo['idDeSector']);
            $unCargo->SetDescripcion( $unArrayAsosiativo['descripcion']);
        }
        
        return $unCargo ;
    }

    private static function CrearLista($data)
    {
        $listaDeRoles = null;
        if(isset($data))
        {
            $listaDeRoles = [];

            foreach($data as $unArray)
            {
                $unCargo = Cargo::CrearUnCargo($unArray);
                
                if(isset($unCargo))
                {
                    array_push($listaDeRoles,$unCargo);
                }
            }
        }

        return   $listaDeRoles;
    }

    public function Equals($unCargo)
    {
        $estado = false;
 
        if(isset($unCargo))
        {
            $estado =  $unCargo->id === $this->id;
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
    private function SetDescripcion($descripcion)
    {
        $estado = false;
        if(isset($descripcion))
        {
            $this->descripcion = $descripcion;
            $estado = true;
        }

        return  $estado ;
    }

   

    //Getters

    public function GetId()
    {
        return  $this->id;
    }
    public function GetDescripcion()
    {
        return  $this->descripcion;
    }
    public function GetSector()
    {
        return  $this->idDeSector;
    }

   
    

    //  public static function EscribirJson($listaDeCargo,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeCargo))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeCargo,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Cargo::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeCargo = null; 
    //      $unCargo = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeCargo = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unCargo = Cargo::DeserializarUnCargoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unCargo))
    //              {
    //                  array_push($listaDeCargo,$unCargo);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeCargo ;
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

    
   


   

    // public static function CompararPorclave($unCargo,$otroCargo)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unCargo->clave,$otroCargo->clave);

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

    // public static function BuscarCargoPorId($listaDeCargo,$id)
    // {
    //     $unaCargoABuscar = null; 

    //     if(isset($listaDeCargo) )
    //     {
    //         foreach($listaDeCargo as $unaCargo)
    //         {
    //             if($unaCargo->id == $id)
    //             {
    //                 $unaCargoABuscar = $unaCargo; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCargoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unCargo,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unCargo = $unCargo;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Cargo::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarCargoPorId($listaDeCargos,$id)
    // {
    //     $unaCargoABuscar = null; 

    //     if(isset($listaDeCargos)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeCargos as $unaCargo)
    //         {
    //             if($unaCargo->id == $id)
    //             {
    //                 $unaCargoABuscar = $unaCargo; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCargoABuscar;
    // }
  
    // public static function ToStringList($listaDeCargos)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeCargos) )
    //     {
    //         foreach($listaDeCargos as $unaCargo)
    //         {
    //             $strLista = $unaCargo->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeCargo,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeCargo) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeCargo as $unaCargo)
    //          {
    //              if($unaCargo::$fechaDeCargo == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>