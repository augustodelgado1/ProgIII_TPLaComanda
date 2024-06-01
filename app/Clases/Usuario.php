

<?php

require_once './db/AccesoDatos.php';
require_once 'Rol.php';

 class Usuario 
{
    private $id;
    private $mail;
    private $clave;
    private $rol;
    private $nombre;
    private $apellido;
    private $fechaDeRegistro;

   
    public function __construct($mail,$clave,$nombre,$apellido,$rol = null) {
        $this->SetEmail($mail);
        $this->SetClave($clave);
        $this->SetNombre($nombre);
        $this->SetApellido($apellido);
        $this->SetRol($rol);
        $this->fechaDeRegistro = new DateTime('now') ;
    }

    public static function DarDeAlta($mail,$clave,$nombre,$apellido)
    {
        $estado = false;
        $unUsuario = new Usuario($mail,$clave,$nombre,$apellido);

        if(empty($unUsuario->nombre) == false && empty($unUsuario->apellido) == false)
        {
            $estado = $unUsuario->AgregarBD();
        }

        return $estado;
    }

    protected static function CrearUnoPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unUsuario  = null;
        if(isset($unArrayAsosiativo))
        {
            $unUsuario = new Usuario($unArrayAsosiativo['mail'],$unArrayAsosiativo['clave'],$unArrayAsosiativo['nombre'],
            $unArrayAsosiativo['apellido'],  $unArrayAsosiativo['rol']);
            $unUsuario->SetId($unArrayAsosiativo['id']);
            $unUsuario->SetFechaDeRegistro($unArrayAsosiativo['fechaDeRegistro']);
        }
       
        return $unUsuario;
    }

    protected static function CrearLista($data)
    {
        $listaDeUsuarios = null;
        if(isset($data))
        {
            $listaDeUsuarios = [];

            foreach($data as $unArray)
            {
               
                $unUsuario = Usuario::CrearUnoPorArrayAsosiativo($unArray);
              
                if(isset($unUsuario))
                {
                    array_push($listaDeUsuarios,$unUsuario);
                }
            }
        }

        return   $listaDeUsuarios;
    }
    protected function AgregarBD()
    {
        $idDeUsuario = null;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Usuario (email,clave,fechaDeRegistro,nombre,apellido,rol) values (:email,:clave,:fechaDeRegistro,:nombre,:apellido,:rol)");
            $consulta->bindValue(':email',$this->mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(':rol',$this->rol,PDO::PARAM_STR);
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':apellido',$this->apellido,PDO::PARAM_STR);
            $consulta->bindValue(':fechaDeRegistro',$this->fechaDeRegistro->format('y-m-d H:i:s'),PDO::PARAM_STR);
            $consulta->execute();
            $idDeUsuario =  $objAccesoDatos->ObtenerUltimoID();
        }
        

        return $idDeUsuario;
    }
    public static function ObtenerListaDeUsuarios()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuario = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Usuario");
            $consulta->execute();
            $listaDeUsuario = Usuario::CrearLista($consulta->fetchAll(Pdo::FETCH_ASSOC));
        }

        return $listaDeUsuario;
    }

    protected static function ObtenerUnUsuarioPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }

    
    public static function BuscarEmailUnUsuarioBD($mail)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.mail = :mail");
            $consulta->bindValue(':mail',$mail,PDO::PARAM_STR);
            $consulta->execute();
            Usuario::CrearUnoPorArrayAsosiativo($consulta->fetch(PDO::FETCH_ASSOC));
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
            Usuario::CrearUnoPorArrayAsosiativo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $data;
    }

    public static function CrearUnCodigoAlfaNumerico($cantidadDeCaracteres)
    {
        $codigoAlfaNumerico = null;
       
        if($cantidadDeCaracteres > 0)
        {
            $caraceteres =  array_merge(range('a','z'),range(0,9));
            $len = count($caraceteres);

            $codigoAlfaNumerico = "";

            for ($i=0; $i < $cantidadDeCaracteres; $i++) { 

                $codigoAlfaNumerico .= $caraceteres[rand(0,$len-1)];
            }
        }

        return  $codigoAlfaNumerico ;
       
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
        return  "mail: ".$this->mail.'<br>'.
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

    protected function SetNombre($nombre)
    {
        $estado = false;
        if(isset($nombre) && Usuario::VerificarQueContengaSoloLetras($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }
    protected function SetApellido($apellido)
    {
        $estado = false;
        if(isset($apellido) && Usuario::VerificarQueContengaSoloLetras($apellido))
        {
            $this->apellido = $apellido;
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

    //Getters
    public function GetMail()
    {
        return  $this->mail;
    }
    protected function GetFechaDeRegistro()
    {
        return  $this->fechaDeRegistro;
    }
    //Getters
    protected function GetNombre()
    {
        return  $this->nombre;
    }

    protected function GetApellido()
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

    public static function VerificarQueContengaSoloLetras($string)
    {
        $estado = false;
        $caracteresInvalidos = range('0','9');

        if(isset($string) && strlen($string) > 0)
        {
            $estado = true;
           foreach($caracteresInvalidos  as $unCaracter)
           {
                if(str_contains($string,$unCaracter))
                {
                    $estado = false;
                    break;
                }
           }
        }

        return $estado;
    }
    
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'claveDeUsuario' => $this->clave,
            'mail' => $this->mail,
        );
    }

    public static function ToStringList($listaDeUsuarios)
    {
        $strLista = null; 

        if(isset($listaDeUsuarios) )
        {
            $strLista = "Usuarios".'<br>';
            foreach($listaDeUsuarios as $unUsuario)
            {
                $strLista .= $unUsuario->ToString().'<br>';
            }
        }

        return   $strLista;
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