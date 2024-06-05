

<?php

require_once './db/AccesoDatos.php';

class Cliente extends Usuario
{
    private $id;
    
    public function __construct($mail,$clave,$nombre,$apellido) {
        
        parent::__construct($mail,$clave,$nombre,$apellido,"Cliente");
    }

    public static function DarDeAltaCliente($mail,$clave,$nombre,$apellido)
    {
        $unCliente = new Cliente($mail,$clave,$nombre,$apellido);
        $estado = false;
        if(isset($unCliente))
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
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Cliente (idDeUsuario) values (:idDeUsuario)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function BuscarPorIdBD($idDeCliente)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCliente = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cliente as c where c.id = :idDeCliente");
            $consulta->bindValue(':idDeCliente',$idDeCliente,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unCliente =  Cliente::CrearUnoPorArrayAsosiativo($data);
        }

        return  $unCliente;
    }

    public static function ObternerListaBD()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeClientes = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Cliente");
            $consulta->execute();
            $listaDeClientes = Cliente::CrearLista($consulta->fetchAll(Pdo::FETCH_ASSOC));
        }
        

        return $listaDeClientes;
    }

    protected static function CrearLista($data)
    {
        $listaDeClientes = null;
        if(isset($data))
        {
            $listaDeClientes = [];

            foreach($data as $unArray)
            {
                $unCliente = Cliente::CrearUnoPorArrayAsosiativo($unArray);
              
                if(isset($unCliente))
                {
                    array_push($listaDeClientes,$unCliente);
                }
            }
        }

        return   $listaDeClientes;
    }
    protected static function CrearUnoPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unEmpleado = null;

        $dataUsuario = Usuario::ObtenerUnUsuarioPorIdBD($unArrayAsosiativo['idDeUsuario']);
     
        if(isset($unArrayAsosiativo) && isset($dataUsuario))
        {
            $unCliente = new Cliente($dataUsuario['email'],$dataUsuario['clave'],$dataUsuario['nombre'],
            $dataUsuario['apellido']);
            $unCliente->SetId($unArrayAsosiativo['id']);
            $unCliente->SetFechaDeRegistro($dataUsuario['fechaDeRegistro']);
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
            $estado =  $unCliente->id === $this->id 
                       && parent::Equals($unCliente);
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
   
    #Mostrar
    public static function ToStringList($listaDeClientes)
    {
        $strLista = null; 

        if(isset($listaDeClientes) )
        {
            $strLista = "Clientes".'<br>';
            foreach($listaDeClientes as $unCliente)
            {
                $strLista .= $unCliente->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return parent::ToString();
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