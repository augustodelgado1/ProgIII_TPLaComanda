

<?php

require_once './db/AccesoDatos.php';

require_once 'Sector.php';

class Rol 
{
    private $id;
    private $descripcion;


    public function __construct($descripcion) {
        $this->descripcion = $descripcion;
    }
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Rol (descripcion) values (:descripcion)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
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
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Rol as r
            SET `descripcion`= :descripcion,
            Where r.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
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
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Rol as r where r.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
   
    public static function BuscarRolPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Rol as c where c.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Rol::CrearUnRol($consulta->fetch(PDO::FETCH_ASSOC));
            
        }

        return $unRol;
    }

    public static function BuscarRolPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM rol as r
             where LOWER(r.descripcion) = LOWER(:descripcion)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $unRol = Rol::CrearUnRol($consulta->fetch(PDO::FETCH_ASSOC));
            
        }

        return  $unRol;
    }

    public static function ObtenerUnoPorDescripcionBD($descripcion)
    {
        return  Rol::CrearUnRol(Rol::BuscarRolPorDescripcionBD($descripcion));
    }
    public static function VerificarUnoPorDescripcionBD($descripcion)
    {
        return Rol::BuscarRolPorDescripcionBD($descripcion) !== false;
    }

    private static function CrearUnRol($unArrayAsosiativo)
    {
        $unRol = null;
      
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unRol = new Rol($unArrayAsosiativo['descripcion']);
            $unRol->SetId($unArrayAsosiativo['id']);
            $unRol->SetDescripcion( $unArrayAsosiativo['descripcion']);
        }
        
        return $unRol ;
    }

    private static function CrearLista($data)
    {
        $listaDeRoles = null;
        if(isset($data))
        {
            $listaDeRoles = [];

            foreach($data as $unArray)
            {
                $unRol = Rol::CrearUnRol($unArray);
                
                if(isset($unRol))
                {
                    array_push($listaDeRoles,$unRol);
                }
            }
        }

        return   $listaDeRoles;
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
   
    

    //  public static function EscribirJson($listaDeRol,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeRol))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeRol,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Rol::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeRol = null; 
    //      $unRol = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeRol = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unRol = Rol::DeserializarUnRolPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unRol))
    //              {
    //                  array_push($listaDeRol,$unRol);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeRol ;
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

    
   


   

    // public static function CompararPorclave($unRol,$otroRol)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unRol->clave,$otroRol->clave);

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

    // public static function BuscarRolPorId($listaDeRol,$id)
    // {
    //     $unaRolABuscar = null; 

    //     if(isset($listaDeRol) )
    //     {
    //         foreach($listaDeRol as $unaRol)
    //         {
    //             if($unaRol->id == $id)
    //             {
    //                 $unaRolABuscar = $unaRol; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaRolABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unRol,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unRol = $unRol;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Rol::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarRolPorId($listaDeRols,$id)
    // {
    //     $unaRolABuscar = null; 

    //     if(isset($listaDeRols)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeRols as $unaRol)
    //         {
    //             if($unaRol->id == $id)
    //             {
    //                 $unaRolABuscar = $unaRol; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaRolABuscar;
    // }
  
    // public static function ToStringList($listaDeRols)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeRols) )
    //     {
    //         foreach($listaDeRols as $unaRol)
    //         {
    //             $strLista = $unaRol->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeRol,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeRol) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeRol as $unaRol)
    //          {
    //              if($unaRol::$fechaDeRol == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>