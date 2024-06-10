

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';
require_once 'Usuario.php';

class Mesa 
{
    public const ESTADO_INICIAL = "con cliente esperando pedido";
    public const ESTADO_INTERMEDIO = "con cliente comiendo";
    public const ESTADO_FINAL = "con cliente pagando";
    public const ESTADO_CERRADO = "cerrada";
 
    private $id;
    private $codigo;
    private $estado;

    

    

    #BaseDeDatos
    protected function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Mesa (codigo,estado) values (:codigo,:estado)");
            $consulta->bindValue(':codigo',$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }
   

    public static function BuscarMesaPorCodigoBD($codigo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unMesa = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Mesa as m where LOWER(m.codigo) = LOWER(:codigo)");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unMesa =  Mesa::CrearUnaMesa($data);
        }

        return  $unMesa;
    }
    public static function FiltarMesaPuntuadas()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeMesas = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT m.id , m.codigo , m.estado 
            FROM `mesa` as m 
            JOIN `orden` as o ON o.idDeMesa = m.id 
            JOIN `encuesta` as e ON e.idDeOrden = o.id 
            JOIN `puntuacion` as p ON p.idDeEncuesta = e.id 
            WHERE p.descripcion = :descripcion");
            $consulta->bindValue(':descripcion',"Mesa",PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeMesas = Mesa::CrearLista($data);
        }

        return  $listaDeMesas;
    }
    public static function BuscarMesaPorIdBD($idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unMesa = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Mesa as m where m.id = :idDeMesa");
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unMesa =  Mesa::CrearUnaMesa($data);
        }

        return  $unMesa;
    }

    
    public function ModificarEstadoBD($estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato) && $this->SetEstado($estado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Mesa 
            as m SET estado = :estado where m.id = :idDeMesa");
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public function ObtenerListaDeOrdenes()
    {
        return  Orden::FiltrarPorIdDeMesaBD($this->GetId());
    }
    public function ObtenerListaDeEncuestas()
    {
        $listaDeOrdenes = $this->ObtenerListaDeOrdenes();
        $listaDeEncuesta = null;
        if(isset( $listaDeOrdenes) && count($listaDeOrdenes) > 0)
        {
            $listaDeEncuesta = [];

            foreach ($listaDeOrdenes as $unaOrden) 
            {
                $unaLista = $unaOrden->ObtenerListaDeEncuestas();
                if(isset($unaLista))
                {
                    array_push( $listaDeEncuesta, $unaLista);
                }

            }
        }
        return  $listaDeEncuesta;
    }

    public static function ObtenerListaBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeMesas = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Mesa");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeMesas = Mesa::CrearLista($data);
        }

        return  $listaDeMesas;
    }

   

    #end

    private static function CrearUnaMesa($data)
    {
        $unMesa = null;

   
        if(isset($data))
        {
            $unaMesa = new Mesa();
            $unaMesa->SetId($data['id']);
            $unaMesa->SetCodigo($data['codigo']);
            $unaMesa->SetEstado($data['estado']);
        }

        return  $unaMesa;
    }
    private static function CrearLista($data)
    {
        $listaDeEmpleados = null;
        if(isset($data) && $data !== false)
        {
            $listaDeEmpleados = [];

            foreach($data as $unArray)
            {
                $unEmpleado = Mesa::CrearUnaMesa($unArray);
                if(isset($unEmpleado))
                {
                    array_push($listaDeEmpleados,$unEmpleado);
                }
            }
        }

        return   $listaDeEmpleados;
    }

    public function Equals($unMesa)
    {
        $estado = false;
 
        if(isset($unMesa))
        {
            $estado =  $unMesa->codigo === $this->codigo;
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
    protected function SetCodigo($codigo)
    {
        $estado = false;
        if(isset($codigo))
        {
            $this->codigo = $codigo;
            $estado = true;
        }

        return  $estado ;
    }
    protected function SetEstado($estadoDelaMesa)
    {
        $estado = false;
        $array = array(Mesa::ESTADO_INICIAL,Mesa::ESTADO_INTERMEDIO,Mesa::ESTADO_FINAL,Mesa::ESTADO_CERRADO);

        if(isset($estado) && in_array($estadoDelaMesa,$array))
        {
            $this->estado = $estadoDelaMesa;
            $estado = true;
        }

        return  $estado ;
    }

    //Getters

    public function GetId()
    {
        return  $this->id;
    }
    public function GetCodigoAlfaNumerico()
    {
        return  $this->codigo;
    }

    public static function ToStringList($listaDeMesas)
    {
        $strLista = null; 

        if(isset($listaDeMesas) )
        {
            $strLista  = "Mesas".'<br>';
            foreach($listaDeMesas as $unaMesa)
            {
                $strLista .= $unaMesa->ToString().'<br>';
            }
        }

        return   $strLista;
    }
    public static function MostarComentarios($listaDeMesas)
    {
        $strLista = null; 

        if(isset($listaDeMesas) )
        {
            $strLista  = "Mesas".'<br>';
            foreach($listaDeMesas as $unaMesa)
            {
                $strLista .= $unaMesa->ToString().'<br>'.
                Orden::MostarComentariosPorCategoria($unaMesa->ObtenerListaDeOrdenes(),"Mesa");
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return 
        "Mesa: ".strtoupper($this->codigo).'<br>'
        ."Estado: ".$this->estado.'<br>';
    }

    public static function ValidarMesaYOrden($data)
    {
        $estado = false;
        $unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']);
        $unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden']);

        if(in_array($unaOrden,$unaMesa->ObtenerListaDeOrdenes()))
        {
            $estado = true;
        }

        return $estado;
    }
   
    public static function ValidarMesaIngresada($data)
    {
        $estado = false;
        if(isset($data['codigoDeMesa']) &&  
        Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa']) !== null)
        {
            $estado = true;
        }

        return $estado;
    }
   
  

    //  public static function EscribirJson($listaDeMesa,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeMesa))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeMesa,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Mesa::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeMesa = null; 
    //      $unMesa = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeMesa = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unMesa = Mesa::DeserializarUnMesaPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unMesa))
    //              {
    //                  array_push($listaDeMesa,$unMesa);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeMesa ;
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

    
   


   

    // public static function CompararPorclave($unMesa,$otroMesa)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unMesa->clave,$otroMesa->clave);

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

    // public static function BuscarMesaPorId($listaDeMesa,$id)
    // {
    //     $unaMesaABuscar = null; 

    //     if(isset($listaDeMesa) )
    //     {
    //         foreach($listaDeMesa as $unaMesa)
    //         {
    //             if($unaMesa->id == $id)
    //             {
    //                 $unaMesaABuscar = $unaMesa; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaMesaABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unMesa,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unMesa = $unMesa;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Mesa::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarMesaPorId($listaDeMesas,$id)
    // {
    //     $unaMesaABuscar = null; 

    //     if(isset($listaDeMesas)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeMesas as $unaMesa)
    //         {
    //             if($unaMesa->id == $id)
    //             {
    //                 $unaMesaABuscar = $unaMesa; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaMesaABuscar;
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
 
    //  public static function ContarPorUnaFecha($listaDeMesa,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeMesa) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeMesa as $unaMesa)
    //          {
    //              if($unaMesa::$fechaDeMesa == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>