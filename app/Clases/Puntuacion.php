

<?php

require_once './db/AccesoDatos.php';

class Puntuacion 
{
    public const ESTADO_POSITIVO = "positivo";
    public const ESTADO_NEGATIVO = "negativo";
    private $id;
    private $idDeEncuesta;
    private $descripcion;
    private $puntuacion;
    private $estado;
   
    public function __construct($idDeEncuesta,$descripcion,$puntuacion) 
    {
        $this->SetPuntuacion($puntuacion);
        $this->descripcion = $descripcion;
        $this->idDeEncuesta = $idDeEncuesta;
        $this->ObtenerEstado();
    }
    
    public static function DarDeAltaUnPuntuacion($idDeEncuesta,$descripcion,$puntuacion)
    {
        $estado = false;
        $unPuntuacion = new Puntuacion($idDeEncuesta,$descripcion,$puntuacion);
        $estado = $unPuntuacion->AgregarBD();

        return $estado;
    }

    #BaseDeDatos
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
     
       
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Puntuacion (descripcion,puntuacion,idDeEncuesta,estado) 
            values (:descripcion,:puntuacion,:idDeEncuesta,:estado)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':puntuacion',$this->puntuacion,PDO::PARAM_INT);
            $consulta->bindValue(':idDeEncuesta',$this->idDeEncuesta,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }
    public static function FiltrarPorIdDeEncuestaBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePuntuaciones= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Puntuacion 
            as p where p.idDeEncuesta = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePuntuaciones = Puntuacion::CrearLista($data);
        }

        return  $listaDePuntuaciones;
    }

    public static function BuscarPordescripcionBD($listaDePuntuaciones,$descripcion)
    {
        $unaPuntuacionBuscar = null;

        if(isset($listaDePuntuaciones) && isset($descripcion) && count($listaDePuntuaciones) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDePuntuaciones as $unaPuntuacion)
            {
                if(strcasecmp($unaPuntuacion->descripcion,$descripcion) === 0)
                {
                    $unaPuntuacionBuscar = $unaPuntuacion;
                }
            }
        }

        return  $unaPuntuacionBuscar;
    }
    public static function FiltrarPordescripcionBD($listaDePuntuaciones,$descripcion)
    {
        $listaFiltrada = null;

        if(isset($listaDePuntuaciones) && isset($descripcion) && count($listaDePuntuaciones) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDePuntuaciones as $unaPuntuacion)
            {
                if(strcasecmp($unaPuntuacion->descripcion,$descripcion) == 0)
                {
                    array_push($listaFiltrada,$unaPuntuacion);
                }
            }
        }

        return  $listaFiltrada;
    }

    public static function FiltrarPorEstado($listaDePuntuaciones,$estado)
    {
        $listaFiltrada = null;

        if(isset($listaDePuntuaciones) && isset($estado) && count($listaDePuntuaciones) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDePuntuaciones as $unaPuntuacion)
            {
                if(strcasecmp($unaPuntuacion->estado,$estado) === 0)
                {
                    array_push($listaFiltrada,$unaPuntuacion);
                }
            }
        }

        return  $listaFiltrada;
    }

    #end


    private static function CrearUnPuntuacion($unArrayAsosiativo)
    {
        $unPuntuacion = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unPuntuacion = new Puntuacion($unArrayAsosiativo['idDeEncuesta'],
            $unArrayAsosiativo['descripcion'],
            $unArrayAsosiativo['puntuacion']);
            $unPuntuacion->SetId($unArrayAsosiativo['id']);
        }
        
        return $unPuntuacion ;
    }

    private static function CrearLista($data)
    {
        $listaDePuntuaciones = null;
        if(isset($data))
        {
            $listaDePuntuaciones = [];

            foreach($data as $unArray)
            {
                $unPuntuacion = Puntuacion::CrearUnPuntuacion($unArray);
                
                if(isset($unPuntuacion))
                {
                    array_push($listaDePuntuaciones,$unPuntuacion);
                }
            }
        }

        return   $listaDePuntuaciones;
    }

    #Setters
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
    private function SetPuntuacion($puntuacion)
    {
        $estado = false;
        if(isset($puntuacion) 
        && $puntuacion >= 0 
        && $puntuacion <= 10 )
        {
            $this->puntuacion = $puntuacion;
            $estado = true;
        }

        return  $estado ;
    }

    private function ObtenerEstado()
    {
        $this->estado = "negativo";

        if( $this->puntuacion >= 6)
        {
            $this->estado = "positivo";
        }
    }


    #Getters

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
    public static function ToStringList($listaDePuntuaciones)
    {
        $strLista = null; 

        if(isset($listaDePuntuaciones) )
        {
            $strLista = "Puntuaciones".'<br>';
            foreach($listaDePuntuaciones as $unPuntuacion)
            {
                $strLista .= $unPuntuacion->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "Descripcion: ".$this->descripcion.'<br>'. 
               "Puntaje: ".$this->puntuacion.'<br>';
    }

    //  public static function EscribirJson($listaDePuntuacion,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDePuntuacion))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDePuntuacion,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Puntuacion::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDePuntuacion = null; 
    //      $unPuntuacion = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDePuntuacion = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unPuntuacion = Puntuacion::DeserializarUnPuntuacionPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unPuntuacion))
    //              {
    //                  array_push($listaDePuntuacion,$unPuntuacion);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDePuntuacion ;
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

    
   


   

    // public static function CompararPorclave($unPuntuacion,$otroPuntuacion)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unPuntuacion->clave,$otroPuntuacion->clave);

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

    // public static function BuscarPuntuacionPorId($listaDePuntuacion,$id)
    // {
    //     $unaPuntuacionABuscar = null; 

    //     if(isset($listaDePuntuacion) )
    //     {
    //         foreach($listaDePuntuacion as $unaPuntuacion)
    //         {
    //             if($unaPuntuacion->id == $id)
    //             {
    //                 $unaPuntuacionABuscar = $unaPuntuacion; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaPuntuacionABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unPuntuacion,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unPuntuacion = $unPuntuacion;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Puntuacion::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarPuntuacionPorId($listaDePuntuacions,$id)
    // {
    //     $unaPuntuacionABuscar = null; 

    //     if(isset($listaDePuntuacions)  
    //     && isset($id) )
    //     {
    //         foreach($listaDePuntuacions as $unaPuntuacion)
    //         {
    //             if($unaPuntuacion->id == $id)
    //             {
    //                 $unaPuntuacionABuscar = $unaPuntuacion; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaPuntuacionABuscar;
    // }
  
    // public static function ToStringList($listaDePuntuacions)
    // {
    //     $strLista = null; 

    //     if(isset($listaDePuntuacions) )
    //     {
    //         foreach($listaDePuntuacions as $unaPuntuacion)
    //         {
    //             $strLista = $unaPuntuacion->ToString().'<br>';
    //         }
    //     }

    //     return   $strLista;
    // }

//Filtrar

    


     //  //Contar
 
    //  public static function ContarPorUnaFecha($listaDePuntuacion,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDePuntuacion) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDePuntuacion as $unaPuntuacion)
    //          {
    //              if($unaPuntuacion::$fechaDePuntuacion == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>