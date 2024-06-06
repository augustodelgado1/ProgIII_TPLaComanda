

<?php

require_once './db/AccesoDatos.php';

 class Usuario 
{
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
        $this->estado = "activo"; 
    }

    public static function GetListaDeEmpleados()
    {
        return Usuario::FiltrarPorRolBD("Empleado");
    }
    public static function GetListaDeSocios()
    {
        return Usuario::FiltrarPorRolBD("Socio");
    }
    public static function GetListaDeClientes()
    {
        return Usuario::FiltrarPorRolBD("Cliente");
    }

 

    protected static function CrearUnoPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unUsuario  = null;
       
        if(isset($unArrayAsosiativo))
        {
            $unUsuario = new Usuario($unArrayAsosiativo['email'],$unArrayAsosiativo['clave'],$unArrayAsosiativo['nombre'],
            $unArrayAsosiativo['apellido'],  $unArrayAsosiativo['rol']);
            $unUsuario->SetId($unArrayAsosiativo['id']);
           if(isset($unArrayAsosiativo['fechaDeRegistro']))
           {
             $unUsuario->SetFechaDeRegistro(new DateTime($unArrayAsosiativo['fechaDeRegistro']));
           }
            $unUsuario->SetEstado($unArrayAsosiativo['estado']);
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
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Usuario (email,clave,fechaDeRegistro,nombre,apellido,dni,estado,rol) 
            values (:email,:clave,:fechaDeRegistro,:nombre,:apellido,:dni,:estado,:rol)");
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
    public static function ObtenerListaDeUsuarios()
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
    public static function FiltrarPorRolBD($rol)
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeUsuario = null;

        if(isset($objAccesoDatos) && isset($rol) )
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Usuario as u where LOWER(u.rol) = LOWER(:rol)");
            $consulta->bindValue(':rol',$rol,PDO::PARAM_STR);
            $consulta->execute();
            $listaDeUsuario = $consulta->fetchAll(Pdo::FETCH_ASSOC);
        }

        return $listaDeUsuario;
    }

    public static function ObtenerUnUsuarioPorIdBD($id)
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
   
    public static function ObtenerUnUsuarioPorDniBD($dni)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;

        if(isset($unObjetoAccesoDato) && isset($dni))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where u.dni = :dni");
            $consulta->bindValue(':dni',$dni,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }
   
    public static function BorrarUnUsuarioPorDniBD($dni)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato) && isset($dni))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Usuario as u where u.dni = :dni");
            $consulta->bindValue(':dni',$dni,PDO::PARAM_STR);
            $estado= $consulta->execute();
        }

        return  $estado;
    }
    public static function SuspenderUnUsuarioPorDniBD($dni)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado= false;
        if(isset($unObjetoAccesoDato) && isset($dni))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u SET u.estado = :estado
            where u.dni = :dni");
            $consulta->bindValue(':dni',$dni,PDO::PARAM_STR);
            $consulta->bindValue(':estado',"Suspendido",PDO::PARAM_STR);
            $estado = $consulta->execute();  
        }

        return  $estado;
    }
   
 

    
    public static function BuscarEmailUnUsuarioBD($mail)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Usuario as u where LOWER(u.email) = LOWER(:email)");
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

    //Modificar

    public function ModificarDniPorDniBD($dniAnterior,$dniNuevo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
     
        if(isset($unObjetoAccesoDato) && isset($dniAnterior) && Usuario::ValidadorDni($dniNuevo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u 
            SET u.dni = :dniNuevo 
            where u.dni = :dni");
            $consulta->bindValue(':dniNuevo',$dniNuevo,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dniAnterior,PDO::PARAM_INT);
            $estado =  $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarNombrePorDniBD($dni,$nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
     
        if(isset($unObjetoAccesoDato) && isset($dni) && Usuario::ValidadorStr($nombre))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u 
            SET u.nombre = :nombre 
            where u.dni = :dni");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_INT);
            $estado =  $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarApellidoPorDniBD($dni,$apellido)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
     
        if(isset($unObjetoAccesoDato) && isset($dni) && Usuario::ValidadorStr($apellido))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u 
            SET u.apellido = :apellido 
            where u.dni = :dni");
            $consulta->bindValue(':apellido',$apellido,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_INT);
            $estado =  $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarClavePorDniBD($dni,$clave)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
     
        if(isset($unObjetoAccesoDato) && isset($dni) && Usuario::ValidadorClave(array("clave" => $clave)))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u 
            SET u.clave = :clave 
            where u.dni = :dni");
            $consulta->bindValue(':clave',$clave,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarEmailPorDniBD($dni,$email)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
     
        if(isset($unObjetoAccesoDato) && isset($dni) && Usuario::ValidadorEmail(array("email" => $email)))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Usuario as u 
            SET u.email = :email 
            where u.dni = :dni");
            $consulta->bindValue(':email',$email,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
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
        return      "Email: ".$this->mail.'<br>'.
          "Nombre Completo: ".$this->GetNombreCompleto().'<br>'.
        "fecha De Registro: ".$this->fechaDeRegistro->format('y-m-d H:i:s').'<br>'.
        "Estado: ".$this->estado.'<br>';
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

    protected function SetEmail($email)
    {
        $estado = false;
        if(Usuario::ValidadorEmail(array("email" => $email)))
        {
            $this->mail = $email;
            $estado = true;
        }

        return  $estado ;
    }

    protected function SetClave($clave)
    {
        $estado = false;
        if(Usuario::ValidadorClave(array("clave" => $clave)))
        {
            $this->clave = $clave;
            $estado = true;
        }

        return  $estado ;
    }

    public function SetNombre($nombre)
    {
        $estado = false;
        if(Usuario::ValidadorStr($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }
    protected function SetApellido($apellido)
    {
        $estado = false;
        if(Usuario::ValidadorStr($apellido))
        {
            $this->apellido = $apellido;
            $estado = true;
        }

        return  $estado ;
    }
    protected function SetDni($dni)
    {
        $estado = false;
        if(isset($dni) && Usuario::VerificarQueContengaSoloNumeros($dni))
        {
            $this->dni = $dni;
            $estado = true;
        }

        return  $estado ;
    }

    protected function SetRol($descripcion)
    {
        $estado  = false;
        if(isset( $descripcion) )
        {
            $this->rol = $descripcion;
        }

        return $estado;
    }
    private function SetEstado($estadoDelUsuario)
    {
        $estado = false;
        if(isset($estado))
        {
            $this->estado = $estadoDelUsuario;
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

    public static function VerificarQueContengaSoloLetras($string)
    {
        $estado = false;
        $caracteresInvalidos = range('A','Z');

        if(isset($string) && strlen($string) > 0)
        {
            $estado = true;
           foreach($caracteresInvalidos  as $unCaracter)
           {
                if(!str_contains($string,$unCaracter))
                {
                    $estado = false;
                    break;
                }
           }
        }

        return $estado;
    }
    public static function VerificarQueContengaSoloNumeros($string)
    {
        $estado = false;
        $caracteresInvalidos = range('0','9');

        if(isset($string) && strlen($string) > 0)
        {
            $estado = true;
           foreach($caracteresInvalidos  as $unCaracter)
           {
                if(!str_contains($string,$unCaracter))
                {
                    $estado = false;
                    break;
                }
           }
        }

        return $estado;
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
    public static function ValidadorDni($data)
    {
        $estado = false; 
        
        if(isset($data) && isset($data['dni'])
        && strlen($data['dni']) == 7 && Usuario::VerificarQueContengaSoloNumeros($data['dni']))
        {
            $estado = true; 
        }
        return $estado;
    }
    private static function ValidadorStr($unString)
    {
        $estado = false; 
        if(isset($unString) && Usuario::VerificarQueContengaSoloLetras($unString))
        {
            $estado = true; 
        }
        return $estado;
    }
    public static function ValidadorApellido($data)
    {
        $estado = false; 
        if(isset($data) && Usuario::ValidadorStr($data['apellido']))
        {
            $estado = true; 
        }
        return $estado;
    }
    public static function ValidadorNombre($data)
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