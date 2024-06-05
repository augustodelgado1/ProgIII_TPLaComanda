

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/File.php';
require_once './Clases/Mesa.php';
require_once './Clases/Pedido.php';
require_once './Clases/Cliente.php';

class Orden 
{
    public static const ESTADO_ACTIVO = "activo";
    public static const ESTADO_INACTIVO = "inactivo";
    private $id;
    private $codigo;
    private $unCliente;
    private $unaMesa;
    private $fechaDeOrden;
    private $rutaDeLaImagen;
    private $nombreDeLaImagen;
    private $costoTotal;
    private $estado;

   
    protected function DarDeAlta()
    {
        $estado = false;
        $this->fechaDeOrden = new DateTime('now');
        $this->estado = "activa";
        $this->costoTotal = 0;
        $this->codigo = Usuario::CrearUnCodigoAlfaNumerico(5);
        $estado = $this->AgregarBD();
        return $estado;
    }
    private function CalcularCostoTotal()
    {
        $this->costoTotal = 0;
        $listaDePedidos = $this->ObtenerListaDePedidos();
        if(isset($listaDePedidos))
        {
            foreach ($listaDePedidos as $unPedido) {
               
                if(isset($unPedido))
                {
                    $this->costoTotal += $unPedido->GetImporteTotal();
                }
            }
        }
        return $this->costoTotal;
    }

    //hacer
    private function CalcularTiempoTotal()
    {
        $tiempoTotal = null;
        $listaDePedidos = $this->ObtenerListaDePedidos();
        if(isset($listaDePedidos) && count($listaDePedidos) > 0)
        {
            $tiempoTotal = new DateTime('00:00');
            foreach ($listaDePedidos as $unPedido) {
               
                if(isset($unPedido))
                {
                    $tiempoTotal->add($unPedido->GetTiempoEstimado());
                }
            }
        }
    }

    public function ObtenerListaDePedidos()
    {
        return  Pedido::FiltrarPedidosPorIdDeOrdenBD($this->id);
    }

    public function ObtenerUnPedidoPendiente()
    {
        $unPedido = null;
        $listaDePedidos = $this->ObtenerListaDePedidos(); 
        $listaDeFiltrada = Pedido::FiltrarPorEstado($listaDePedidos,Pedido::ESTADO_INICIAL); 

        if(isset( $listaDeFiltrada) && count($listaDePedidos) > 0)
        {
            $unPedido = $listaDeFiltrada[0];
        }

        return $unPedido;
    }

    #Imagen
    private function SetImagen($ruta,$nombreDelaImagen)
    {
        $estado = false;
        if(isset($ruta) && isset($nombreDelaImagen))
        {
            $this->nombreDeLaImagen = $nombreDelaImagen;
            $this->rutaDeLaImagen = $ruta;
            $estado = true;
        }

        return $estado;
    }

    public function GuardarImagen($tmpNombre,$rutaASubir,$nombreDeArchivo)
    {
        $estado = false;
    
        if(File::MoverArchivoSubido($tmpNombre,$rutaASubir,$nombreDeArchivo))
        {
            $this->SetImagen($rutaASubir,$nombreDeArchivo);
            $estado = true;
        }

        return $estado;
    }

    #BaseDeDatos


    protected function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Orden (codigo,idDeCliente,idDeMesa,fechaDeOrden,costoTotal,estado,rutaDeLaImagen,nombreDeLaImagen) 
            values (:codigo,:idDeCliente,:idDeMesa,:fechaDeOrden,:costoTotal,:estado,:rutaDeLaImagen,:nombreDeLaImagen)");
            $consulta->bindValue(':codigo',$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(':idDeCliente',$this->unCliente->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':idDeMesa',$this->unaMesa->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':fechaDeOrden',$this->fechaDeOrden->format("y-m-d"),PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->GetEstado(),PDO::PARAM_STR);
            $consulta->bindValue(':costoTotal',$this->costoTotal);
            $consulta->bindValue(':rutaDeLaImagen',$this->rutaDeLaImagen);
            $consulta->bindValue(':nombreDeLaImagen',$this->nombreDeLaImagen);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    
    public static function BuscarOrdenPorIdBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unaOrden = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.id = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_STR);
            $consulta->execute();
            $unaOrden = Orden::CrearUnaOrden($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unaOrden;
    }

    public static function FiltrarPorIdDeMesaBD($idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.idDeMesa = :idDeMesa");
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }
    public static function FiltrarPorIdDeClienteBD($idDeCliente)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.idDeCliente = :idDeCliente");
            $consulta->bindValue(':idDeMesa',$idDeCliente,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }

    public static function BuscarPorCodigoBD($codigo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unaOrden = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.codigo = :codigo");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unaOrden =  Orden::CrearUnaOrden($data);
        }

        return  $unaOrden;
    }

    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }

    #end

    private static function CrearUnaOrden($unArrayAsosiativo)
    {
        $unaOrden = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unaOrden = new Orden();
            $unaOrden->SetId($unArrayAsosiativo['id']);
            $unaOrden->SetCodigo($unArrayAsosiativo['codigo']);
            $unaOrden->SetIdMesa($unArrayAsosiativo['idDeMesa']);
            $unaOrden->SetIdCliente($unArrayAsosiativo['idDeCliente']);
            $unaOrden->SetFechaDeOrden($unArrayAsosiativo['fechaDeOrden']);
            $unaOrden->SetCostoTotal($unArrayAsosiativo['costoTotal']);
            $unaOrden->SetImagen($unArrayAsosiativo['rutaDeLaImagen'],$unArrayAsosiativo['nombreDeLaImagen']);
        }
        
        return $unaOrden ;
    }

    private static function CrearLista($data)
    {
        $listaDeOrdenes = null;
        if(isset($data))
        {
            $listaDeOrdenes = [];

            foreach($data as $unArray)
            {
                $unaOrden = Orden::CrearUnaOrden($unArray);
                
                
                if(isset($unaOrden))
                {
                    array_push($listaDeOrdenes,$unaOrden);
                }
            }
        }

        return   $listaDeOrdenes;
    }

    public static function BuscarOrdenPorId($listaDeOrdens,$id)
    {
        $unaOrdenABuscar = null; 
        $index = Orden::ObtenerIndicePorId($listaDeOrdens,$id);
        if($index > 0 )
        {
            $unaOrdenABuscar = $listaDeOrdens[$index];
        }

        return  $unaOrdenABuscar;
    }

     public static function ObtenerIndicePorId($listaDeOrdens,$id)
    {
        $index = -1;
       
        if(isset($listaDeOrdens)  && isset($id))
        {
            $leght = count($listaDeOrdens); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeOrdens[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function Equals($unaOrden)
    {
        $estado = false;
 
        if(isset($unaOrden))
        {
            $estado =  $unaOrden->id === $this->id;
        }
        return  $estado ;
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

    

    public function SetCodigo($codigo)
    {
        $estado = false;
        if(isset($codigo) )
        {
            $this->codigo = $codigo;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetCostoTotal($costoTotal)
    {
        $estado = false;
        if(isset($costoTotal))
        {
            $this->costoTotal = $costoTotal;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetFechaDeOrden($fechaDeOrden)
    {
        $estado = false;
        if(isset($fechaDeOrden))
        {
            $this->fechaDeOrden = $fechaDeOrden;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetIdCliente($idDeCliente)
    {
        $unCliente =  Cliente::BuscarPorIdBD($idDeCliente);
        $estado  = Orden::SetCliente($unCliente);
        return $estado;
    }

    protected function SetCliente($unCliente)
    {
        $estado = false;
        if(isset($unCliente))
        {
            $this->unCliente = $unCliente;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetIdMesa($idDeMesa)
    {
        $unaMesa =  Mesa::BuscarMesaPorIdBD($idDeMesa);
        $estado  = Orden::SetMesa($unaMesa);

        return $estado;
    }
    protected function SetMesa($unaMesa)
    {
        $estado = false;
        if(isset($unaMesa))
        {
            $this->unaMesa = $unaMesa;
            $estado = true;
        }

        return  $estado ;
    }

    #Getters
    public function GetCodigo()
    {
        return  $this->codigo;
    }
    public function GetEstado()
    {
        return  $this->estado;
    }
    public function GetNombreDelCliente()
    {
        return  $this->unCliente->GetNombreCompleto();
    }

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
     public static function ToStringList($listaDeOrdenes)
    {
        $strLista = null; 

        if(isset($listaDeOrdenes) )
        {
            $strLista = "Ordenes".'<br>';
            foreach($listaDeOrdenes as $unaOrden)
            {
                $strLista .= $unaOrden->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "codigo: ".strtoupper($this->codigo).'<br>';
    }

    //  public static function EscribirJson($listaDeOrden,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeOrden))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeOrden,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Orden::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeOrden = null; 
    //      $unaOrden = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeOrden = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unaOrden = Orden::DeserializarunaOrdenPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unaOrden))
    //              {
    //                  array_push($listaDeOrden,$unaOrden);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeOrden ;
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

    
   


   

    // public static function CompararPorclave($unaOrden,$otroOrden)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unaOrden->clave,$otroOrden->clave);

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

    // public static function BuscarOrdenPorId($listaDeOrden,$id)
    // {
    //     $unaOrdenABuscar = null; 

    //     if(isset($listaDeOrden) )
    //     {
    //         foreach($listaDeOrden as $unaOrden)
    //         {
    //             if($unaOrden->id == $id)
    //             {
    //                 $unaOrdenABuscar = $unaOrden; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaOrdenABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unaOrden,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unaOrden = $unaOrden;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Orden::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarOrdenPorId($listaDeOrdens,$id)
    // {
    //     $unaOrdenABuscar = null; 

    //     if(isset($listaDeOrdens)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeOrdens as $unaOrden)
    //         {
    //             if($unaOrden->id == $id)
    //             {
    //                 $unaOrdenABuscar = $unaOrden; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaOrdenABuscar;
    // }
  
    // public static function ToStringList($listaDeOrdens)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeOrdens) )
    //     {
    //         foreach($listaDeOrdens as $unaOrden)
    //         {
    //             $strLista = $unaOrden->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeOrden,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeOrden) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeOrden as $unaOrden)
    //          {
    //              if($unaOrden::$fechaDeOrden == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>