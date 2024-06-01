

<?php

require_once './db/AccesoDatos.php';
require_once 'Cargo.php';

class Empleado extends Usuario
{
    private $id;
    private $cargo;
    private $estado;
   
    public function __construct($mail,$clave,$nombre,$apellido,$cargo,$estado = "activo") {
        
        parent::__construct($mail,$clave,$nombre,$apellido,"Empleado");
        $this->cargo = $cargo;
        $this->estado = $estado;
    }

    public function ToString()
    {
      
        return 
        "<br>".parent::ToString().
        "Rol de trabajo: ".$this->cargo->GetDescripcion().'<br>'
        ."estado: ". $this->estado;
         ;
    }
    private function SetCargo($idDerol)
    {
        $unCargo =  Cargo::BuscarCargoPorIdBD($idDerol);
        $estado  = false;
        if(isset( $unCargo))
        {
            $this->cargo = $unCargo;
        }

        return $estado;
    }

    public static function DarDeAltaUnEmpleado($mail,$clave,$nombre,$apellido,$cargo)
    {
        $estado = false;
        $unEmpleado = new Empleado($mail,$clave,$nombre,$apellido,$cargo);

        if(empty($unEmpleado->cargo) == false)
        {
            $estado = $unEmpleado->AgregarBD();
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
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Empleado (idDeUsuario,idDeCargo,estado) values (:idDeUsuario,:cargo,:estado)");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $consulta->bindValue(':cargo',$this->cargo->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ObtenerListaPorCargoBD($unCargo)
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEmpleados = null;

 
        if(isset($objAccesoDatos) && isset($unCargo))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Select * From Empleado as e where e.idDeCargo = :idDeCargo");
            $consulta->bindValue(':idDeCargo',$unCargo->GetId(),PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
          
            $listaDeEmpleados = Empleado::CrearLista($data);
        }
        

        return $listaDeEmpleados;
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
    protected static function CrearUnoPorArrayAsosiativo($unArrayAsosiativo)
    {
        $unEmpleado = null;

        $dataUsuario = Usuario::ObtenerUnUsuarioPorIdBD($unArrayAsosiativo['idDeUsuario']);
     
     
        if(isset($unArrayAsosiativo) && isset($dataUsuario))
        {
            $unEmpleado = new Empleado($dataUsuario['email'],$dataUsuario['clave'],$dataUsuario['nombre'],
            $dataUsuario['apellido'],$unArrayAsosiativo['idDeCargo']);
            $unEmpleado->SetId($unArrayAsosiativo['id']);
            $unEmpleado->SetCargo($unArrayAsosiativo['idDeCargo']);
            $unEmpleado->SetFechaDeRegistro(new DateTime($dataUsuario['fechaDeRegistro']));
            $unEmpleado->SetEstado($unArrayAsosiativo['estado']);
        }
        
        return $unEmpleado ;
    }


    public static function BuscarEmpleadoPorId($listaDeEmpleados,$id)
    {
        $unaEmpleadoABuscar = null; 
        $index = Empleado::ObtenerIndicePorId($listaDeEmpleados,$id);
        if($index > 0 )
        {
            $unaEmpleadoABuscar = $listaDeEmpleados[$index];
        }

        return  $unaEmpleadoABuscar;
    }

     public static function ObtenerIndicePorId($listaDeEmpleados,$id)
    {
        $index = -1;
       
        if(isset($listaDeEmpleados)  && isset($id))
        {
            $leght = count($listaDeEmpleados); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeEmpleados[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
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

    private function SetEstado($estadoDelEmpleado)
    {
        $estado = false;
        if(isset($estado))
        {
            $this->estado = $estadoDelEmpleado;
            $estado = true;
        }

        return  $estado ;
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