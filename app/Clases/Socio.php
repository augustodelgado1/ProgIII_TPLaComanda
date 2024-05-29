

<?php

require_once './db/AccesoDatos.php';

class Socio extends Usuario
{
    private $id;
    private $nombre;

   
    public function __construct($mail,$clave,$nombre) {
        
        parent::__construct($mail,$clave,"Socio");
        $this->nombre = $nombre;
    
    }

    public static function DarDeAltaUnSocio($mail,$clave,$nombre)
    {
        $estado = false;
        $unSocio = new Socio($mail,$clave,$nombre);

        if(empty($unSocio->nombre) == false )
        {
            $estado = $unSocio->AgregarBD();
        }

        return $estado;
    }

    protected function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeUsuario = parent::AgregarBD();
        if(isset($objAccesoDatos) && isset($idDeUsuario))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Socio (idDeUsuario,nombre,idDeSector,estado) values (:idDeUsuario,:nombre,:idDeSector,:estado)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BuscarSocioPorId($listaDeSocios,$id)
    {
        $unaSocioABuscar = null; 
        $index = Socio::ObtenerIndicePorId($listaDeSocios,$id);
        if($index > 0 )
        {
            $unaSocioABuscar = $listaDeSocios[$index];
        }

        return  $unaSocioABuscar;
    }

     public static function ObtenerIndicePorId($listaDeSocios,$id)
    {
        $index = -1;
       
        if(isset($listaDeSocios)  && isset($id))
        {
            $leght = count($listaDeSocios); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeSocios[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unSocio)
    {
        $estado = false;
 
        if(isset($unSocio))
        {
            $estado =  $unSocio->id === $this->id;
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

    //  public static function EscribirJson($listaDeSocio,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeSocio))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeSocio,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Socio::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeSocio = null; 
    //      $unSocio = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeSocio = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unSocio = Socio::DeserializarUnSocioPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unSocio))
    //              {
    //                  array_push($listaDeSocio,$unSocio);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeSocio ;
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

    
   


   

    // public static function CompararPorclave($unSocio,$otroSocio)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unSocio->clave,$otroSocio->clave);

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

    // public static function BuscarSocioPorId($listaDeSocio,$id)
    // {
    //     $unaSocioABuscar = null; 

    //     if(isset($listaDeSocio) )
    //     {
    //         foreach($listaDeSocio as $unaSocio)
    //         {
    //             if($unaSocio->id == $id)
    //             {
    //                 $unaSocioABuscar = $unaSocio; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaSocioABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unSocio,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unSocio = $unSocio;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Socio::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarSocioPorId($listaDeSocios,$id)
    // {
    //     $unaSocioABuscar = null; 

    //     if(isset($listaDeSocios)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeSocios as $unaSocio)
    //         {
    //             if($unaSocio->id == $id)
    //             {
    //                 $unaSocioABuscar = $unaSocio; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaSocioABuscar;
    // }
  
    // public static function ToStringList($listaDeSocios)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeSocios) )
    //     {
    //         foreach($listaDeSocios as $unaSocio)
    //         {
    //             $strLista = $unaSocio->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeSocio,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeSocio) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeSocio as $unaSocio)
    //          {
    //              if($unaSocio::$fechaDeSocio == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>