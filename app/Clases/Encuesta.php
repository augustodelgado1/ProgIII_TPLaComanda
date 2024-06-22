

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/Puntuacion.php';
require_once './Clases/Orden.php';

class Encuesta 
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
    

    #BaseDeDatos
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Encuesta (nombreDelCliente,mensaje,idDeOrden,estado) 
            values (:nombreDelCliente,:mensaje,:idDeOrden,:estado)");
            $consulta->bindValue(':mensaje',$this->mensaje,PDO::PARAM_STR);
            $consulta->bindValue(':nombreDelCliente',$this->nombreDelCliente,PDO::PARAM_STR);
            $consulta->bindValue(':idDeOrden',$this->idDeOrden,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();
        }

        return $idDeEncuesta;
    }

    public static function ModificarUnoBD($id,$nombreDelCliente,$mensaje,$idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato) )
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Encuesta as e
            SET `nombreDelCliente`= :nombreDelCliente,
            `mensaje`= :mensaje,
            `idDeOrden`= :idDeOrden,
            Where e.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':nombreDelCliente',$nombreDelCliente,PDO::PARAM_STR);
            $consulta->bindValue(':mensaje',$mensaje,PDO::PARAM_STR);
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Encuesta as e where e.id = :id");
            $consulta->bindValue(':id',$idDeEncuesta,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    private static function BuscarUnoPorIdBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unEncuesta = false;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.id = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $unEncuesta = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $unEncuesta;
    }
    public static function ObtenerUnoPorIdBD($idDeEncuesta)
    {
        return  Encuesta::CrearUnEncuesta(Encuesta::BuscarUnoPorIdBD($idDeEncuesta));
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
    public static function FiltrarPorPuntucionBD($descripcion,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestas = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT e.id,e.nombreDelCliente,e.mensaje,e.idDeOrden,e.estado FROM Encuesta e
            JOIN Puntuacion p ON p.idDeEncuesta = e.id WHERE LOWER(p.descripcion) = LOWER(:descripcion) AND LOWER(p.estado) = LOWER(:estado)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
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

     public static function ObtenerIndicePorId($listaDeEncuestas,$id)
    {
        $index = -1;
       
      
        if(isset($listaDeEncuestas)  && isset($id))
        {
            
            $leght = count($listaDeEncuestas); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeEncuestas[$i]->id === $id)
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
        if(Encuesta::ValidadorDeMensaje($mensaje))
        {
            $this->mensaje = $mensaje;
            $estado = true;
        }

        return  $estado ;
    }

    public static function ValidadorAlta($data)
    {
        return  Encuesta::ValidadorModificacion($data)
        && isset($data['puntuacionDeLaMesa'])
        && isset($data['puntuacionDelRestaurante'])
        && isset($data['puntuacionDelCocinero'])
        && isset($data['puntuacionDelMozo'])
        && Mesa::VerificarUnoPorCodigo($data['codigoDeMesa']);
    }
    public static function ValidadorModificacion($data)
    {
        return  Encuesta::ValidadorDeMensaje($data['mensaje'])
        && Orden::VerificarCodigo($data['numeroDeOrden'])
        && Encuesta::ValidadorDeCliente($data['nombre']);
    }
    private static function ValidadorDeCliente($nombre)
    {
        return Util::ValidadorDeNombre($nombre);
    }


    private static function ValidadorDeMensaje($mensaje)
    {
        return isset($mensaje) && strlen($mensaje) <= 66;
    }
    public static function VerificarUnoPorId($id)
    {
        return Encuesta::BuscarUnoPorIdBD($id) !== false;
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
    
    public static function FiltrarPorIdDeOrdenes($listaDeEncuesta,$idDeOrden)
    {
       
        $listaDefiltrada = null;

        if(isset($listaDeEncuesta) && isset($idDeOrden))
        {
            $listaDefiltrada = [];

            foreach ($listaDeEncuesta as $unaEncuesta) 
            {
                if($unaEncuesta->idDeOrden === $idDeOrden)
                {
                    array_push($listaDefiltrada,$unaEncuesta);
                }
            }
        }

        return  $listaDefiltrada;
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

    // public static function FiltrarPorLista($listaDePizzas,$tipo)
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