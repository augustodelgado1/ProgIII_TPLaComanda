

<?php

require_once './db/AccesoDatos.php';

abstract class Usuario 
{
    public const ESTADO_ACTIVO = "activo";
    public const ESTADO_SUSPENDIDO = "suspendido";
    public const ESTADO_BORRADO = "borrado";
    private $id;
    private $mail;
    private $clave;
    private $rol;
    private $nombre;
    private $apellido;
    private $fechaDeRegistro;
    private $dni;

    private $estado;

   
    public function __construct($mail,$clave,$nombre,$apellido,$dni,$rol = null) {
        $this->SetEmail($mail);
        $this->SetClave($clave);
        $this->SetNombre($nombre);
        $this->SetApellido($apellido);
        $this->SetRol($rol);
        $this->SetDni($dni);
        $this->fechaDeRegistro = new DateTime('now') ;
        $this->estado = Usuario::ESTADO_ACTIVO;
    }
    public function AgregarBD()
    {
        $idDeUsuario = null;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Usuario (email,clave,fechaDeRegistro,nombre,apellido,dni,rol,estado) 
            values (:email,:clave,:fechaDeRegistro,:nombre,:apellido,:dni,:rol,:estado)");
            $consulta->bindValue(':email',$this->mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(':rol',$this->rol,PDO::PARAM_STR);
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':apellido',$this->apellido,PDO::PARAM_STR);
            $consulta->bindValue(':fechaDeRegistro',$this->fechaDeRegistro->format('y-m-d H:i:s'),PDO::PARAM_STR);
            $consulta->bindValue(':dni',$this->dni,PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            $idDeUsuario =  $objAccesoDatos->ObtenerUltimoID();
        }
        

        return $idDeUsuario;
    }

    protected static function ModificarEstadoBD($id,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato) )
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `Usuario` SET estado = :estado Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $estado= $consulta->execute();
            
        }

        return  $estado;
    }

    public static function ListarBD()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuario = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Usuario");
            $consulta->execute();
            $listaDeUsuario = $consulta->fetchAll(Pdo::FETCH_ASSOC);
        }

        return $listaDeUsuario;
    }
    public static function BuscarPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;

        if(isset($unObjetoAccesoDato) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }
    public static function BorrarUnoPorIdBD($id)
    {
        $estado = false;

        if(isset($id))
        {
            $estado =  Usuario::ModificarEstadoBD($id,Usuario::ESTADO_BORRADO);
        }

        return  $estado;
    }

   

    public static function BuscarEmailUnUsuarioBD($mail)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u 
            where LOWER(u.email) = LOWER(:email)");
            $consulta->bindValue(':email',$mail,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }

    public static function BuscarClaveUnUsuarioBD($clave)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.clave = :clave");
            $consulta->bindValue(':clave',$clave,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }

    public static function FiltrarPorEstadoBD($estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.estado = :estado");
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
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
    

    public function ToString()
    {
        return      "Email: ".$this->mail.'<br>'.
          "Nombre Completo: ".$this->GetNombreCompleto().'<br>'.
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



    private function SetEmail($email)
    {
        $estado = false;
        if(Usuario::ValidadorEmail(array("email" => $email)))
        {
            $this->mail = $email;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetClave($clave)
    {
        $estado = false;
        if(Usuario::ValidadorClave(array("clave" => $clave)))
        {
            $this->clave = $clave;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetNombre($nombre)
    {
        $estado = false;
        if(Usuario::ValidadorStr($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetApellido($apellido)
    {
        $estado = false;
        if(Usuario::ValidadorStr($apellido))
        {
            $this->apellido = $apellido;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetDni($dni)
    {
        $estado = false;
        if(isset($dni) && Util::VerificarQueContengaSoloNumeros($dni))
        {
            $this->dni = $dni;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetRol($descripcion)
    {
        $estado  = false;
        if(isset( $descripcion) )
        {
            $this->rol = $descripcion;
        }

        return $estado;
    }

    protected function SetEstado($estadoDelaPedido)
    {
        $estado = false;
        $array = array(Usuario::ESTADO_ACTIVO,Usuario::ESTADO_BORRADO,Usuario::ESTADO_SUSPENDIDO);

        if(isset($estado) && in_array($estadoDelaPedido,$array))
        {
            $this->estado = $estadoDelaPedido;
            $estado = true;
        }

        return  $estado ;
    }
    

    //Getters
    public function GetMail()
    {
        return  $this->mail;
    }
    public function GetId()
    {
        return  $this->id;
    }
    public function GetFechaDeRegistro()
    {
        return  $this->fechaDeRegistro;
    }
    //Getters
    public function GetNombre()
    {
        return  $this->nombre;
    }

    public function GetApellido()
    {
        return  $this->apellido;
    }

    public function GetRolDeUsuario()
    {
        return  $this->rol;
    }
    public function GetNombreCompleto()
    {
        return  $this->nombre." ".$this->apellido;
    }

    
    public static function ValidadorEmail($data)
    {
        $estado = false; 

        if(isset($data) && isset($data['email'])
        && strlen($data['email']) >= 8)
        {
            $estado = true; 
        }

        
        return $estado;
    }

    public static function ValidadorClave($data)
    {
        $estado = false; 
  
        if(isset($data) && isset($data['clave'])
        && strlen($data['clave']) >= 8)
        {
            $estado = true; 
        }
        return $estado;
    }
    private static function ValidadorDni($data)
    {
        $estado = false; 
        
        if(isset($data) && isset($data['dni'])
        && strlen($data['dni']) == 7 && Util::VerificarQueContengaSoloNumeros($data['dni']))
        {
            $estado = true; 
        }
        return $estado;
    }
    private static function ValidadorStr($unString)
    {
        $estado = false; 
        if(isset($unString) && Util::VerificarQueContengaSoloLetras($unString))
        {
            $estado = true; 
        }
        return $estado;
    }
    private static function ValidadorApellido($data)
    {
        $estado = false; 
        if(isset($data) && Usuario::ValidadorStr($data['apellido']))
        {
            $estado = true; 
        }
        return $estado;
    }
    private static function ValidadorNombre($data)
    {
        $estado = false; 
        if(isset($data) && Usuario::ValidadorStr($data['nombre']))
        {
            $estado = true; 
        }
        return $estado;
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