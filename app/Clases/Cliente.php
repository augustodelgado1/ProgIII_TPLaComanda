

<?php

require_once './db/AccesoDatos.php';

class Cliente extends Usuario
{
    private $id;
    private $nombre;

   
    public function __construct($mail,$clave,$nombre) {
        
        parent::__construct($mail,$clave,"Cliente");
        $this->nombre = $nombre;
    }

    public static function DarDeAltaUnCliente($mail,$clave,$nombre)
    {
        $estado = false;
        $unCliente = new Cliente($mail,$clave,$nombre);

        if(empty($unCliente->nombre) == false )
        {
            $estado = $unCliente->AgregarBD();
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
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Cliente (idDeUsuario,nombre) values (:idDeUsuario,:nombre)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BuscarClientePorIdBD($idDeCliente)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCliente = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cliente as c where c.id = :idDeCliente");
            $consulta->bindValue(':idDeCliente',$idDeCliente,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unCliente =  Cliente::CrearUnCliente($data);
        }

        return  $unCliente;
    }

    private static function CrearLista($data)
    {
        $listaDeClientes = null;
        if(isset($data))
        {
            $listaDeClientes = [];

            foreach($data as $unArray)
            {
               
                $unCliente = Cliente::CrearUnCliente($unArray);
              
                if(isset($unCliente))
                {
                    array_push($listaDeClientes,$unCliente);
                }
            }
        }

        return   $listaDeClientes;
    }
    private static function CrearUnCliente($unArrayAsosiativo)
    {
        $unEmpleado = null;

        $unUsuario = Usuario::ObtenerUnUsuarioPorIdBD($unArrayAsosiativo['idDeUsuario']);
     
        if(isset($unArrayAsosiativo) && isset($unUsuario))
        {
            $unCliente = new Cliente($unUsuario->GetMail(),$unUsuario->GetClave(),$unArrayAsosiativo['nombre']);
            $unCliente->SetId($unArrayAsosiativo['id']);
            $unCliente->SetFechaDeRegistro($unUsuario->GetFechaDeRegistro());
        }
        
        return $unEmpleado ;
    }

    public static function BuscarClientePorId($listaDeClientes,$id)
    {
        $unaClienteABuscar = null; 
        $index = Cliente::ObtenerIndicePorId($listaDeClientes,$id);
        if($index > 0 )
        {
            $unaClienteABuscar = $listaDeClientes[$index];
        }

        return  $unaClienteABuscar;
    }

     public static function ObtenerIndicePorId($listaDeClientes,$id)
    {
        $index = -1;
       
        if(isset($listaDeClientes)  && isset($id))
        {
            $leght = count($listaDeClientes); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeClientes[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unCliente)
    {
        $estado = false;
 
        if(isset($unCliente))
        {
            $estado =  $unCliente->id === $this->id;
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

    //  public static function EscribirJson($listaDeCliente,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeCliente))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeCliente,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Cliente::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeCliente = null; 
    //      $unCliente = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeCliente = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unCliente = Cliente::DeserializarUnClientePorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unCliente))
    //              {
    //                  array_push($listaDeCliente,$unCliente);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeCliente ;
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

    
   


   

    // public static function CompararPorclave($unCliente,$otroCliente)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unCliente->clave,$otroCliente->clave);

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

    // public static function BuscarClientePorId($listaDeCliente,$id)
    // {
    //     $unaClienteABuscar = null; 

    //     if(isset($listaDeCliente) )
    //     {
    //         foreach($listaDeCliente as $unaCliente)
    //         {
    //             if($unaCliente->id == $id)
    //             {
    //                 $unaClienteABuscar = $unaCliente; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaClienteABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unCliente,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unCliente = $unCliente;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Cliente::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarClientePorId($listaDeClientes,$id)
    // {
    //     $unaClienteABuscar = null; 

    //     if(isset($listaDeClientes)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeClientes as $unaCliente)
    //         {
    //             if($unaCliente->id == $id)
    //             {
    //                 $unaClienteABuscar = $unaCliente; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaClienteABuscar;
    // }
  
    // public static function ToStringList($listaDeClientes)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeClientes) )
    //     {
    //         foreach($listaDeClientes as $unaCliente)
    //         {
    //             $strLista = $unaCliente->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeCliente,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeCliente) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeCliente as $unaCliente)
    //          {
    //              if($unaCliente::$fechaDeCliente == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>