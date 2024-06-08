

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/Usuario.php';

class Socio extends Usuario
{
    private $id;

    public function __construct($mail,$clave,$nombre,$apellido,$dni) {
        parent::__construct($mail,$clave,$nombre,$apellido,$dni,"Socio");
    }

    public static function DarDeAltaUnSocio($mail,$clave,$nombre,$apellido,$dni)
    {
        $estado = false;
        $unSocio = new Socio($mail,$clave,$nombre,$apellido,$dni);
        $unSocio->AgregarBD();

        return $estado;
    }

    #BaseDeDatos
    protected function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeUsuario = parent::AgregarBD();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Socio (idDeUsuario) values (:idDeUsuario)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BorrarUnoPorIdBD($idDeSocio)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        $arrayDeSocio = Socio::ObtenerArrayDeSocioPorId($idDeSocio);
       
        if(isset($unObjetoAccesoDato) && isset($arrayDeSocio))
        {
            parent::BorrarUnoPorIdBD($arrayDeSocio['idDeUsuario']);
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Socio as s where s.id = :id");
            $consulta->bindValue(':id',$idDeSocio,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    
    public static function ModificarUnoBD($idDeSocio,$mail,$clave,$nombre,$apellido,$dni)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        $arrayDeSocio = Socio::ObtenerArrayDeSocioPorId($idDeSocio);
       
        if(isset($unObjetoAccesoDato) && isset($arrayDeSocio))
        {
            $estado = parent::ModificarUnoBD($arrayDeSocio['idDeUsuario'],$mail,$clave,$nombre,$apellido,$dni);
        }

        return  $estado;
    }

    private static function ObtenerArrayDeSocioPorId($idDeSocio)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Socio as s where s.id = :idDeSocio");
            $consulta->bindValue(':idDeSocio',$idDeSocio,PDO::PARAM_STR);
            $consulta->execute();
        }

        return  $consulta->fetch(PDO::FETCH_ASSOC);;
    }

    
    public static function BuscarSocioPorIdBD($idDeSocio)
    {
        $unSocio = null;
        $data = Socio::ObtenerArrayDeSocioPorId($idDeSocio);

        if(isset($data))
        {
            $unSocio = Socio::CrearUnSocio($data);
        }

        return  $unSocio;
    }
    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeSocioes= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Socio");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeSocioes = Socio::CrearLista($data);
        }

        return  $listaDeSocioes;
    }

    #end

    private static function CrearUnSocio($unArrayAsosiativo)
    {
        $unSocio = null;
        $dataUsuario = Usuario::ObtenerUnUsuarioPorIdBD($unArrayAsosiativo['idDeUsuario']);

        if(isset($unArrayAsosiativo) && isset($dataUsuario) && $unArrayAsosiativo !== false)
        {
            $unSocio = new Socio($dataUsuario['email'],
            $dataUsuario['clave'],
            $dataUsuario['nombre'],
            $dataUsuario['apellido'],
            $dataUsuario['dni']);
            $unSocio->SetId($unArrayAsosiativo['id']);
            $unSocio->SetFechaDeRegistro($dataUsuario['fechaDeRegistro']);
        }
        
        return $unSocio ;
    }

    protected static function CrearLista($data)
    {
        $listaDeSocioes = null;
        if(isset($data))
        {
            $listaDeSocioes = [];

            foreach($data as $unArray)
            {
                $unSocio = Socio::CrearUnSocio($unArray);
                
                
                if(isset($unSocio))
                {
                    array_push($listaDeSocioes,$unSocio);
                }
            }
        }

        return   $listaDeSocioes;
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

    #Mostrar
    public static function ToStringList($listaDeSocioes)
    {
        $strLista = null; 

        if(isset($listaDeSocioes) )
        {
            $strLista = "Socios".'<br>';
            foreach($listaDeSocioes as $unSocio)
            {
                $strLista .= $unSocio->ToString().'<br>';
            }
        }

        return   $strLista;
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