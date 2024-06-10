

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/Puntuacion.php';
require_once './Clases/Orden.php';
require_once './Clases/File.php';
require_once './interfaces/IFileManejadorCSV';

class Encuesta implements IFileManejadorCSV
{
    private $id;
    private $nombreDelCliente;
    private $idDeOrden;
    private $mensaje;
    private $estado;
   
    public function __construct($idDeOrden,$nombreDelCliente,$mensaje) 
    {
        $this->SetMensaje($mensaje);
        $this->idDeOrden = $idDeOrden;
        $this->nombreDelCliente = $nombreDelCliente;
    }

    public function ObtenerListaDePuntuaciones()
    {
       return Puntuacion::FiltrarPorIdDeEncuestaBD($this->id);
    }
    public static function DarDeAlta($idDeOrden,$nombreDelCliente,$mensaje)
    {
        $unaEncuesta = new Encuesta($idDeOrden,$nombreDelCliente,$mensaje);
        $idDeEncuesta = $unaEncuesta->AgregarBD();

       return $idDeEncuesta;
    }

    #BaseDeDatos
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Encuesta (nombreDelCliente,mensaje,idDeOrden,estado) 
            values (:nombreDelCliente,:mensaje,:idDeOrden,:estado)");
            $consulta->bindValue(':mensaje',$this->mensaje,PDO::PARAM_STR);
            $consulta->bindValue(':nombreDelCliente',$this->mensaje,PDO::PARAM_STR);
            $consulta->bindValue(':idDeOrden',$this->idDeOrden,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();
        }

        return $idDeEncuesta;
    }
    public static function BuscarEncuestaPorIdBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unEncuesta = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.id = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $unEncuesta = Encuesta::CrearUnEncuesta($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unEncuesta;
    }
    public static function FiltrarPorIdDeOrdenBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestas = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $listaDeEncuestas = Encuesta::CrearLista($consulta->fetchAll(PDO::FETCH_ASSOC));
          
        }

        return  $listaDeEncuestas;
    }

    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestaes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeEncuestaes = Encuesta::CrearLista($data);
        }

        return  $listaDeEncuestaes;
    }

    public static function EscribirCsv($nombreDeArchivo,$listaDeEncuesta)
    {
        $estado = false;
        
        if(isset($nombreDeArchivo) && isset($listaDeEncuesta))
        {
            $estado = File::EscribirGenerico($listaDeEncuesta,$nombreDeArchivo,array(__CLASS__,'EscribirUnoCsv'));
        }

        return   $estado;
    }
    
    public static function EscribirUnoCsv($unaEncuesta,$unArchivo)
    {
        $estado = false;
        
        if(isset($unaEncuesta))
        {
            $unArray = array($unaEncuesta->id,$unaEncuesta->nombreDelCliente,$unaEncuesta->idDeOrden,
            $unaEncuesta->mensaje,$unaEncuesta->estado);
            $estado  = fputcsv($unArchivo,$unArray);
        }

        return   $estado;
    }

    public static function LeerCsv($rutaArchivo)
    {
        $listaDeEncuesta = null;
        $data = File::LeerArchivoCsv($rutaArchivo);
        
        if(isset($data))
        {
            $listaDeEncuesta = Encuesta::CrearLista($data);
        }

        return   $listaDeEncuesta;
    }

    #end

    private static function CrearUnEncuesta($unArrayAsosiativo)
    {
        $unEncuesta = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unEncuesta = new Encuesta($unArrayAsosiativo['idDeOrden'],$unArrayAsosiativo['nombreDelCliente'],
            $unArrayAsosiativo['mensaje']);
            $unEncuesta->SetId($unArrayAsosiativo['id']);
            $unEncuesta->SetEstado($unArrayAsosiativo['estado']);
        }
        
        return $unEncuesta ;
    }

   

    private static function CrearLista($data)
    {
        $listaDeEncuestaes = null;
        if(isset($data))
        {
            $listaDeEncuestaes = [];

            foreach($data as $unArray)
            {
                $unEncuesta = Encuesta::CrearUnEncuesta($unArray);
                
                
                if(isset($unEncuesta))
                {
                    array_push($listaDeEncuestaes,$unEncuesta);
                }
            }
        }

        return   $listaDeEncuestaes;
    }

    public static function BuscarEncuestaPorId($listaDeEncuestas,$id)
    {
        $unaEncuestaABuscar = null; 
        $index = Encuesta::ObtenerIndicePorId($listaDeEncuestas,$id);
        if($index > 0 )
        {
            $unaEncuestaABuscar = $listaDeEncuestas[$index];
        }

        return  $unaEncuestaABuscar;
    }

     public static function ObtenerIndicePorId($listaDeEncuestas,$id)
    {
        $index = -1;
       
        if(isset($listaDeEncuestas)  && isset($id))
        {
            $leght = count($listaDeEncuestas); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeEncuestas[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function Equals($unEncuesta)
    {
        $estado = false;
 
        if(isset($unEncuesta))
        {
            $estado =  $unEncuesta->id === $this->id;
        }
        return  $estado ;
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

    private function SetEstado($estadoDelaEncuesta)
    {
        $estado = false;
        if(isset($estado))
        {
            $this->estado = $estadoDelaEncuesta;
            $estado = true;
        }

        return  $estado ;
    }

    public function SetMensaje($mensaje)
    {
        $estado = false;
        if(isset($mensaje) )
        {
            $this->mensaje = $mensaje;
            $estado = true;
        }

        return  $estado ;
    }

    #Getters
    public function GetMensaje()
    {
        return  $this->mensaje;
    }
    public function GetNombreDelCliente()
    {
        return  $this->nombreDelCliente;
    }
    public function GetStrPuntuacion()
    {
        $listaDePuntuaciones = $this->ObtenerListaDePuntuaciones();

        $mensaje = "no se encontraron puntuaciones";
        if(isset($listaDePuntuaciones) && count($listaDePuntuaciones) > 0)
        {
            $mensaje = Puntuacion::ToStringList($listaDePuntuaciones);
        }

        return  $mensaje ;
    }

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
     public static function ToStringList($listaDeEncuestaes)
    {
        $strLista = null; 

        if(isset($listaDeEncuestaes) )
        {
            $strLista = "Encuestas".'<br>';
            foreach($listaDeEncuestaes as $unEncuesta)
            {
              
                $strLista .= $unEncuesta->ToString().'<br>';
            }
        }

        return   $strLista;
    }
     public static function MostrarSoloComentariosPorCategoria($listaDeEncuestaes,$categoria)
    {
        $strLista = null; 

        if(isset($listaDeEncuestaes) )
        {
            $strLista = "Comentarios".'<br>';
            foreach($listaDeEncuestaes as $unEncuesta)
            {
                $unaPuntuacion = Puntuacion::BuscarPorDescripcionBD($unEncuesta->ObtenerListaDePuntuaciones(),$categoria);

                if(isset($unaPuntuacion))
                {
                    $strLista .= "Nombre del Cliente: ".$unEncuesta->nombreDelCliente.'<br>'.
                    "Puntuacion".'<br>'.
                    $unaPuntuacion->ToString().
                    "Comentario: ".$unEncuesta->mensaje.'<br>'.'<br>';
                }
                
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return
        "Nombre del Cliente: ".$this->nombreDelCliente.'<br>'.
        $this->GetStrPuntuacion(). 
        "Comentario: ".$this->mensaje.'<br>';
       
    }

    //  public static function EscribirJson($listaDeEncuesta,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeEncuesta))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeEncuesta,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Encuesta::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeEncuesta = null; 
    //      $unEncuesta = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeEncuesta = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unEncuesta = Encuesta::DeserializarUnEncuestaPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unEncuesta))
    //              {
    //                  array_push($listaDeEncuesta,$unEncuesta);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeEncuesta ;
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

    
   


   

    // public static function CompararPorclave($unEncuesta,$otroEncuesta)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unEncuesta->clave,$otroEncuesta->clave);

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

    // public static function BuscarEncuestaPorId($listaDeEncuesta,$id)
    // {
    //     $unaEncuestaABuscar = null; 

    //     if(isset($listaDeEncuesta) )
    //     {
    //         foreach($listaDeEncuesta as $unaEncuesta)
    //         {
    //             if($unaEncuesta->id == $id)
    //             {
    //                 $unaEncuestaABuscar = $unaEncuesta; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaEncuestaABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unEncuesta,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unEncuesta = $unEncuesta;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Encuesta::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarEncuestaPorId($listaDeEncuestas,$id)
    // {
    //     $unaEncuestaABuscar = null; 

    //     if(isset($listaDeEncuestas)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeEncuestas as $unaEncuesta)
    //         {
    //             if($unaEncuesta->id == $id)
    //             {
    //                 $unaEncuestaABuscar = $unaEncuesta; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaEncuestaABuscar;
    // }
  
    // public static function ToStringList($listaDeEncuestas)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeEncuestas) )
    //     {
    //         foreach($listaDeEncuestas as $unaEncuesta)
    //         {
    //             $strLista = $unaEncuesta->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeEncuesta,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeEncuesta) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeEncuesta as $unaEncuesta)
    //          {
    //              if($unaEncuesta::$fechaDeEncuesta == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>