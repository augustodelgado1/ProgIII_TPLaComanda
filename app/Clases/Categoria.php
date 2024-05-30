

<?php

require_once './db/AccesoDatos.php';

class Categoria 
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
        $unSector =  Sector::BuscarSectorPorIdBD($idDeSector);
        $estado  = false;
        if(isset( $unSector))
        {
            $this->idDeSector = $unSector;
        }

        return $estado;
    }
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Categoria (nombre) values (:nombre)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BuscarCategoriaPorId($listaDeCategorias,$id)
    {
        $unaCategoriaABuscar = null; 
        $index = Categoria::ObtenerIndicePorId($listaDeCategorias,$id);
        if($index > 0 )
        {
            $unaCategoriaABuscar = $listaDeCategorias[$index];
        }

        return  $unaCategoriaABuscar;
    }
    public static function BuscarCategoriaPorIdBD($idDeCategoria)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCategoria = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Categoria as s where s.id == $idDeCategoria");
            $consulta->execute();
            $unCategoria = $consulta->fetchObject(__CLASS__,array('nombre'));
        }

        return  $unCategoria;
    }
    private static function CrearUnaCategoria($unArrayAsosiativo)
    {
        $unCategoria = null;
        
        if(isset($unArrayAsosiativo) && isset($unUsuario))
        {
            $unCategoria = new Categoria($unArrayAsosiativo['nombre'],$unArrayAsosiativo['idDeSector']);
            $unCategoria->SetId($unArrayAsosiativo['id']);
            $unCategoria->SetSector($unArrayAsosiativo['idDeSector']);
        }
        
        return $unCategoria ;
    }
    public static function BuscarPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCategoria = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Categoria as c where c.nombre = :nombre");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unCategoria = Categoria::CrearUnaCategoria($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unCategoria;
    }

     public static function ObtenerIndicePorId($listaDeCategorias,$id)
    {
        $index = -1;
       
        if(isset($listaDeCategorias)  && isset($id))
        {
            $leght = count($listaDeCategorias); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeCategorias[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unCategoria)
    {
        $estado = false;
 
        if(isset($unCategoria))
        {
            $estado =  $unCategoria->id === $this->id;
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

    //  public static function EscribirJson($listaDeCategoria,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeCategoria))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeCategoria,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Categoria::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeCategoria = null; 
    //      $unCategoria = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeCategoria = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unCategoria = Categoria::DeserializarUnCategoriaPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unCategoria))
    //              {
    //                  array_push($listaDeCategoria,$unCategoria);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeCategoria ;
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

    
   


   

    // public static function CompararPorclave($unCategoria,$otroCategoria)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unCategoria->clave,$otroCategoria->clave);

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

    // public static function BuscarCategoriaPorId($listaDeCategoria,$id)
    // {
    //     $unaCategoriaABuscar = null; 

    //     if(isset($listaDeCategoria) )
    //     {
    //         foreach($listaDeCategoria as $unaCategoria)
    //         {
    //             if($unaCategoria->id == $id)
    //             {
    //                 $unaCategoriaABuscar = $unaCategoria; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCategoriaABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unCategoria,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unCategoria = $unCategoria;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Categoria::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarCategoriaPorId($listaDeCategorias,$id)
    // {
    //     $unaCategoriaABuscar = null; 

    //     if(isset($listaDeCategorias)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeCategorias as $unaCategoria)
    //         {
    //             if($unaCategoria->id == $id)
    //             {
    //                 $unaCategoriaABuscar = $unaCategoria; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCategoriaABuscar;
    // }
  
    // public static function ToStringList($listaDeCategorias)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeCategorias) )
    //     {
    //         foreach($listaDeCategorias as $unaCategoria)
    //         {
    //             $strLista = $unaCategoria->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeCategoria,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeCategoria) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeCategoria as $unaCategoria)
    //          {
    //              if($unaCategoria::$fechaDeCategoria == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>