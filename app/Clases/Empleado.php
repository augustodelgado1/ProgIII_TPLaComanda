

<?php

require_once './db/AccesoDatos.php';
require_once 'Cargo.php';

class Empleado extends Usuario
{
   
    private $id;
    private $cargo;
   
    public function __construct($mail,$clave,$nombre,$apellido,$dni,$cargo) {
        
        parent::__construct($mail,$clave,$nombre,$apellido,$dni,"Empleado");
        $this->cargo = $cargo;
        
    }

    public function ObtenerListaDePedidos()
    {
        return  Pedido::FiltrarPorIdDeEmpleadoBD($this->id);
    }

    // d- Cantidad de operaciones de cada uno por separado.
    public function CantidadDePedidos()
    {
        $cantidad = -1;
        $listaDePedidos = $this->ObtenerListaDePedidos();
        if(isset( $listaDePedidos))
        {
            $cantidad = count($listaDePedidos);
        }

        return $cantidad;
    }
   
    public static function BorrarUnoPorIdBD($idDeEmpleado)
    {
        $estado = false;
        $arrayDeEmpleado = Empleado::ObtenerArrayDeEmpleadoPorId($idDeEmpleado);
       
        if(isset($arrayDeEmpleado))
        {
            $estado = parent::BorrarUnoPorIdBD($arrayDeEmpleado['idDeUsuario']);
        }

        return  $estado;
    }
    public static function SuspenderUnoPorIdBD($idDeEmpleado)
    {
        $estado = false;
        $arrayDeEmpleado = Empleado::ObtenerArrayDeEmpleadoPorId($idDeEmpleado);

        if(isset($arrayDeEmpleado))
        {
            $estado =  Usuario::ModificarEstadoBD($arrayDeEmpleado['idDeUsuario'],Usuario::ESTADO_SUSPENDIDO);
        }

        return  $estado;
    }
   
   
    
    public static function ModificarUnEmpleadoBD($idDeEmpleado,$mail,$clave,$nombre,$apellido,$dni,$cargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `usuario` 
            JOIN Empleado e ON e.id = u.id
            SET `email`= :mail,`clave`= :clave,`nombre`= :nombre,`apellido`= :apellido,`dni`= :dni 
            Where e.id=:id");
            $consulta->bindValue(':id',$idDeEmpleado,PDO::PARAM_INT);
            $consulta->bindValue(':mail',$mail,PDO::PARAM_STR);
            $consulta->bindValue(':clave',$clave,PDO::PARAM_STR);
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->bindValue(':apellido',$apellido,PDO::PARAM_STR);
            $consulta->bindValue(':dni',$dni,PDO::PARAM_STR);
            $estado = $consulta->execute() === true 
            && Empleado::ModificarCargoBD($idDeEmpleado,$cargo) === true;
        }

        return  $estado;
    }
    public static function ModificarCargoBD($id,$cargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `Empleado` SET cargo = :cargo Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':cargo',$cargo,PDO::PARAM_STR);
            $estado= $consulta->execute();
        }

        return  $estado;
    }
  
    private static function ObtenerArrayDeEmpleadoPorId($idDeEmpleado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado as s where s.id = :idDeEmpleado");
            $consulta->bindValue(':idDeEmpleado',$idDeEmpleado,PDO::PARAM_INT);
            $consulta->execute();
        }

        return  $consulta->fetch(PDO::FETCH_ASSOC);;
    }

    public function ToString()
    {
        return 
        "<br>".parent::ToString().
        "Cargo: ".$this->GetCargo()->GetDescripcion().'<br>'
        .$this->GetStrCantidadDeOpereaciones();
    }
    private function SetIdCargo($idDecargo)
    {
        $estado  = false;
        if(isset($idDecargo))
        {
            $this->cargo = $idDecargo;
        }

        return $estado;
    }

    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeUsuario = parent::AgregarBD();
        if(isset($objAccesoDatos) && isset($idDeUsuario))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Empleado (idDeUsuario,idDeCargo) 
            values (:idDeUsuario,:idDeCargo)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $consulta->bindValue(':idDeCargo',$this->cargo,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ListarBD()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEmpleados = null;

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Empleado");
            $consulta->execute();
            $listaDeEmpleados = Empleado::CrearLista($consulta->fetchAll(Pdo::FETCH_ASSOC));
        }
        

        return $listaDeEmpleados;
    }

    public static function FiltrarPorCargoBD($idDeCargo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado 
            as e where e.idDeCargo = :idDeCargo");
            $consulta->bindValue(':idDeCargo',$idDeCargo,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= Empleado::CrearLista($data);
        }

        return $listaDeTipos;
    }
    public static function FiltrarPorFechaDeRegistroBD($fechaDeRegistro)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado 
            JOIN Usuario u ON e.idDeUsuario = u.id
            as e where u.fechaDeRegistro = :fechaDeRegistro");
            $consulta->bindValue(':fechaDeRegistro',$fechaDeRegistro->format('d-m-y'),PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= Empleado::CrearLista($data);
        }

        return $listaDeTipos;
    }
    public static function FiltrarPorEstadoBD($estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            parent::FiltrarPorEstadoBD($estado);


        }

        return $listaDeTipos;
    }

    
    
    protected static function CrearLista($data)
    {
        $listaDeEmpleados = null;
        if(isset($data))
        {
            $listaDeEmpleados = [];

            foreach($data as $unArray)
            {

                $unEmpleado = Empleado::CrearUnoPorArrayAsosiativo($unArray);
               
                if(isset($unEmpleado))
                {
                    array_push($listaDeEmpleados,$unEmpleado);
                }
            }
        }

        return   $listaDeEmpleados;
    }

    public static function BuscarPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data= null;

        if(isset($unObjetoAccesoDato) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Empleado as e where e.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            Empleado::CrearUnoPorArrayAsosiativo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $data;
    }
    protected static function CrearUnoPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unEmpleado = null;
       
        $dataUsuario = Usuario::BuscarPorIdBD($unArrayAsosiativo['idDeUsuario']);
     
       
        if(isset($unArrayAsosiativo) && isset($dataUsuario) && $dataUsuario !== false)
        {
            $unEmpleado = new Empleado($dataUsuario['email'],$dataUsuario['clave'],$dataUsuario['nombre'],
            $dataUsuario['apellido'],$unArrayAsosiativo['dni'],$unArrayAsosiativo['idDeCargo']);
            $unEmpleado->SetId($unArrayAsosiativo['id']);
            $unEmpleado->SetIdCargo($unArrayAsosiativo['idDeCargo']);
            $unEmpleado->SetFechaDeRegistro(new DateTime($dataUsuario['fechaDeRegistro']));
            $unEmpleado->SetEstado(['estado']);
          
        }
        
        return $unEmpleado ;
    }

    public static function ToStringList($listaDeEmpleados)
    {
        $strLista = null; 

        if(isset($listaDeEmpleados) )
        {
            $strLista  = "Empleados".'<br>';
         
            foreach($listaDeEmpleados as $unEmpleado)
            {
                $strLista .= $unEmpleado->ToString().'<br><br>';
            }
        }

        return   $strLista;
    }


    public function Equals($unEmpleado)
    {
        $estado = false;
 
        if(isset($unEmpleado))
        {
            $estado =  $unEmpleado->id === $this->id;
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

    public function GetCargo()
    {
        return Cargo::BuscarCargoPorIdBD($this->cargo);
    }

    public function GetSector()
    {
        return $this->GetCargo()->GetSector();
    }
    public function GetStrCantidadDeOpereaciones()
    {
        $mensaje = "";
        if(strcasecmp($this->GetCargo()->GetDescripcion(),'Mozo') !== 0)
        {
            $mensaje = "No realizo operaciones";
            $cantidad = $this->CantidadDePedidos();
    
            if($cantidad > 0)
            {
                $mensaje = "Cantidad: ".$cantidad;
            }
        }
       

        return  $mensaje;
    }

   

    
    //  public static function EscribirJson($listaDeEmpleado,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeEmpleado))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeEmpleado,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Empleado::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeEmpleado = null; 
    //      $unEmpleado = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeEmpleado = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unEmpleado = Empleado::DeserializarUnEmpleadoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unEmpleado))
    //              {
    //                  array_push($listaDeEmpleado,$unEmpleado);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeEmpleado ;
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

    
   


   

    // public static function CompararPorclave($unEmpleado,$otroEmpleado)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unEmpleado->clave,$otroEmpleado->clave);

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

    // public static function BuscarEmpleadoPorId($listaDeEmpleado,$id)
    // {
    //     $unaEmpleadoABuscar = null; 

    //     if(isset($listaDeEmpleado) )
    //     {
    //         foreach($listaDeEmpleado as $unaEmpleado)
    //         {
    //             if($unaEmpleado->id == $id)
    //             {
    //                 $unaEmpleadoABuscar = $unaEmpleado; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaEmpleadoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unEmpleado,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unEmpleado = $unEmpleado;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Empleado::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarEmpleadoPorId($listaDeEmpleados,$id)
    // {
    //     $unaEmpleadoABuscar = null; 

    //     if(isset($listaDeEmpleados)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeEmpleados as $unaEmpleado)
    //         {
    //             if($unaEmpleado->id == $id)
    //             {
    //                 $unaEmpleadoABuscar = $unaEmpleado; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaEmpleadoABuscar;
    // }
  
    // public static function ToStringList($listaDeEmpleados)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeEmpleados) )
    //     {
    //         foreach($listaDeEmpleados as $unaEmpleado)
    //         {
    //             $strLista = $unaEmpleado->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeEmpleado,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeEmpleado) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeEmpleado as $unaEmpleado)
    //          {
    //              if($unaEmpleado::$fechaDeEmpleado == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>