

<?php

require_once './db/AccesoDatos.php';

 class Usuario 
{
    private $id;
    private $mail;
    private $clave;
    private $rol;
    private $fechaDeRegistro;

   
    public function __construct($mail,$clave,$rol = null) {
        $this->SetEmail($mail);
        $this->SetClave($clave);
        $this->rol = $rol;
        $this->fechaDeRegistro = new DateTime('now') ;
    }

    protected function GetClave()
    {
        return $this->clave;
    }

    public static function DarDeAltaUnUsuario($mail,$clave)
    {
        $estado = false;
        $unUsuario = new Usuario($mail,$clave);

        if(empty($unUsuario->mail) == false && empty($unUsuario->clave) == false )
        {
            $estado = $unUsuario->AgregarBD();
        }

        return $estado;
    }

    protected function AgregarBD()
    {
        $idDeUsuario = null;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Usuario (email,clave,fechaDeRegistro,rol) values (:email,:clave,:fechaDeRegistro,:rol)");
            $consulta->bindValue(':email',$this->mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(':rol',$this->rol,PDO::PARAM_STR);
            $consulta->bindValue(':fechaDeRegistro',$this->fechaDeRegistro->format('y-m-d H:i:s'),PDO::PARAM_STR);
            $consulta->execute();
            
            $idDeUsuario =  $objAccesoDatos->ObtenerUltimoID();
        }
        

        return $idDeUsuario;
    }
    public static function ObtenerListaDeUsuarios()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuarios = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Usuario");
            $consulta->execute();
            $listaDeUsuarios = $consulta->fetchAll(Pdo::FETCH_CLASS,__CLASS__,array('mail','clave'));
        }
        

        return $listaDeUsuarios;
    }

    protected static function ObtenerUnUsuarioPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unUsuario = null;

        if(isset($unObjetoAccesoDato))
        {
            
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
           
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
           
            $unUsuario = Usuario::CrearUnUsuarioPorArrayAsosiativo($data);
        }

        return  $unUsuario;
    }

    private static function CrearUnUsuarioPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unUsuario = null; 
        
        if(isset($unArrayAsosiativo))
        {
            $unUsuario = new Usuario($unArrayAsosiativo['email'],$unArrayAsosiativo['clave'],
            $unArrayAsosiativo['rol']);
            $unUsuario->SetId($unArrayAsosiativo['id']);
            $unUsuario->SetFechaDeRegistro(new DateTime($unArrayAsosiativo['fechaDeRegistro']));
        }

        return  $unUsuario;
    }
    protected function SetFechaDeRegistro($fechaDeRegistro)
    {
        $estado = false;
        if(isset($fechaDeRegistro))
        {
            $this->fechaDeRegistro = $fechaDeRegistro;
            $estado = true;
        }

        return  $estado ;
    }
    

    public static function BuscarUsuarioPorId($listaDeUsuarios,$id)
    {
        $unaUsuarioABuscar = null; 
        $index = Usuario::ObtenerIndicePorId($listaDeUsuarios,$id);
        if($index > 0 )
        {
            $unaUsuarioABuscar = $listaDeUsuarios[$index];
        }

        return  $unaUsuarioABuscar;
    }

     public static function ObtenerIndicePorId($listaDeUsuarios,$id)
    {
        $index = -1;
       
        if(isset($listaDeUsuarios)  && isset($id))
        {
            $leght = count($listaDeUsuarios); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeUsuarios[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function ToString()
    {
        
        return  "mail: ".$this->mail.'<br>'.
        "fecha De Registro: ".$this->fechaDeRegistro->format('y-m-d H:i:s').'<br>';
    }

    public function Equals($unUsuario)
    {
        $estado = false;
 
        if(isset($unUsuario))
        {
            $estado =  strcasecmp($unUsuario->mail,$this->mail) == 0
                       && $unUsuario->clave === $this->clave;
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

    public function SetEmail($email)
    {
        $estado = false;
        if(isset($email))
        {
            $this->mail = $email;
            $estado = true;
        }

        return  $estado ;
    }

    public function SetClave($clave)
    {
        $estado = false;
        if(isset($clave) && strlen($clave) >= 8)
        {
            $this->clave = $clave;
            $estado = true;
        }

        return  $estado ;
    }

    //Getters
    public function GetMail()
    {
        return  $this->mail;
    }
    protected function GetId()
    {
        return  $this->id;
    }
    protected function GetFechaDeRegistro()
    {
        return  $this->fechaDeRegistro;
    }

    private static function ObtenerIdAutoIncremental()
    {
        return rand(1,10000);
    }

     public static function FiltrarPorRol($listaDeUsuarios,$rol)
    {
        $filtro = null;
       
        if(isset($listaDeUsuarios) && isset($rol) && count($listaDeUsuarios) > 0)
        {
            $filtro =  [];

        
            foreach($listaDeUsuarios as $unUsuario)
            {
                if(strcasecmp($unUsuario->rol,$rol) == 0)
                {
                    array_push($filtro,$unUsuario);
                }
            }
        }

        return  $filtro;
    }
    
    
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'claveDeUsuario' => $this->clave,
            'mail' => $this->mail,
        );
    }
    //  public static function EscribirJson($listaDeUsuario,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeUsuario))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeUsuario,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Usuario::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeUsuario = null; 
    //      $unUsuario = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeUsuario = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unUsuario = Usuario::DeserializarUnUsuarioPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unUsuario))
    //              {
    //                  array_push($listaDeUsuario,$unUsuario);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeUsuario ;
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

    
   


   

    // public static function CompararPorclave($unUsuario,$otroUsuario)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unUsuario->clave,$otroUsuario->clave);

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

    // public static function BuscarUsuarioPorId($listaDeUsuario,$id)
    // {
    //     $unaUsuarioABuscar = null; 

    //     if(isset($listaDeUsuario) )
    //     {
    //         foreach($listaDeUsuario as $unaUsuario)
    //         {
    //             if($unaUsuario->id == $id)
    //             {
    //                 $unaUsuarioABuscar = $unaUsuario; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaUsuarioABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unUsuario,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unUsuario = $unUsuario;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Usuario::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarUsuarioPorId($listaDeUsuarios,$id)
    // {
    //     $unaUsuarioABuscar = null; 

    //     if(isset($listaDeUsuarios)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeUsuarios as $unaUsuario)
    //         {
    //             if($unaUsuario->id == $id)
    //             {
    //                 $unaUsuarioABuscar = $unaUsuario; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaUsuarioABuscar;
    // }
  
    // public static function ToStringList($listaDeUsuarios)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeUsuarios) )
    //     {
    //         foreach($listaDeUsuarios as $unaUsuario)
    //         {
    //             $strLista = $unaUsuario->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeUsuario,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeUsuario) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeUsuario as $unaUsuario)
    //          {
    //              if($unaUsuario::$fechaDeUsuario == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>