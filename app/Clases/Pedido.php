

<?php

require_once './db/AccesoDatos.php';
require_once 'Producto.php';
require_once 'Orden.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');
class Pedido 
{
    public const ESTADO_INICIAL = "pendiente";
    public const ESTADO_INTERMEDIO = "en preparacion";
    public const ESTADO_FINAL = "listo para servir";
    public const ESTADO_CANCELADO = "cancelado";
    private $id;
    private $orden;
    private $idDeSector;
    private $idDeEmpleado;
    private $numeroDePedido;
    private $unProducto;
    private $tiempoEstimado;

    private $tiempoDeInicio;
    private $tiempoDeFinalizacion;
    private $importeTotal;
    private $estado;

    private $estadoDelTiempo;
    private function CalcularImporteTotal()
    {
        $this->importeTotal = 0;

        if(isset($this->unProducto))
        {
            $this->importeTotal =  $this->unProducto->GetPrecio();
        }

        return $this->importeTotal;
    }

    private function ObtenerSector()
    {
        $this->idDeSector = null;
        if(isset($this->unProducto))
        {
            $this->idDeSector =  $this->unProducto->GetTipo()->GetSector();
        }

        return $this->idDeSector ;
    }
    public static function Alta($orden,$unProducto)
    {
        
        $unPedido = new Pedido();
      
        if($unPedido->SetProducto($unProducto) && $unPedido->SetOrden($orden) 
        && $unPedido->ObtenerSector() !== null)
        {
            $unPedido->numeroDePedido = rand(100,1000);
            $unPedido->SetEstado(Pedido::ESTADO_INICIAL);
            $unPedido->AgregarBD();
        }

        return $unPedido;
    }

    
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("
            Insert into Pedido (numeroDePedido,idDeOrden,idDeSector,idDeProducto,importeTotal,estado) 
            values (:numeroDePedido,:idDeLaOrden,:idDeSector,:idDeProducto,:importeTotal,:estado)");
            $consulta->bindValue(':numeroDePedido',$this->numeroDePedido,PDO::PARAM_INT);
            $consulta->bindValue(':idDeSector',$this->idDeSector->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':idDeLaOrden',$this->orden->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':idDeProducto',$this->unProducto->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':importeTotal',$this->importeTotal);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    // $unPedido->ModificarIdDeEmpleadoBD($unEmpleado->GetId());
    // $unPedido->ModificarEstadoBD(Pedido::ESTADO_INTERMEDIO);
    protected function ModificarIdDeEmpleadoBD($idDeEmpleado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if(isset($unObjetoAccesoDato) && $this->SetEmpleado($idDeEmpleado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.idDeEmpleado = :idDeEmpleado 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':idDeEmpleado',$this->idDeEmpleado,PDO::PARAM_INT);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    protected function ModificarEstadoBD($estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if(isset($unObjetoAccesoDato) && $this->SetEmpleado($estado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.estado = :estado 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    protected function ModificarTiempoEstimadoBD($tiempoEstimado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato) && $this->SetTiempoEstimado($tiempoEstimado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.tiempoEstimado = :tiempoEstimado 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoEstimado',$this->GetStrTiempoEstimado(),PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }

  
    
    private function SeEntregoEnElTimpoEstimado()
    {
        $this->estadoDelTiempo = false;

        $diferencia = $this->tiempoDeInicio->diff($this->tiempoDeFinalizacion);

        if(isset($diferencia))
        {
            $this->estadoDelTiempo  = true;
            if($diferencia->h > $this->tiempoEstimado->h 
            || ($diferencia->h === $this->tiempoEstimado->h && $diferencia->i >  $this->tiempoEstimado->i)  )
            {
                $this->estadoDelTiempo  = false;
            }
        }
        
        return  $this->estadoDelTiempo ;
    }
    protected function ModificarTiempoDeInicioBD($tiempoInicio)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato) && $this->SetTiempoDeInicio($tiempoInicio))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.tiempoInicio = :tiempoInicio 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoInicio',$this->GetTiempoDeInicio(),PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    protected function ModificarTiempoDeFinalizacionBD($tiempoDeFinalizacion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if(isset($unObjetoAccesoDato) && $this->SetTiempoDeFinalizacion($tiempoDeFinalizacion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p SET p.tiempoDeFinalizacion = :tiempoDeFinalizacion where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoDeFinalizacion',$this->GetTiempoDeFinalizacion(),PDO::PARAM_STR);
            $consulta->execute();
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    
    public static function BuscarPedidoPorNumeroDePedidoBD($numeroDePedido)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unPedido = null;

        if(isset($unObjetoAccesoDato) && isset($numeroDePedido))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where p.numeroDePedido = :numeroDePedido");
            $consulta->bindValue(':numeroDePedido',$numeroDePedido,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unPedido =  Pedido::CrearUnaPedido($data);
        }

        return  $unPedido;
    }
    public static function FiltrarPedidosPorIdDeOrdenBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada = null;

        if(isset($unObjetoAccesoDato) && isset($idDeOrden))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where p.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaFiltrada =  Pedido::CrearLista($data);
        }

        return  $listaFiltrada;
    }
    public static function FiltrarPorIdDeEmpleadoBD($idDeEmpleado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada = null;

        if(isset($unObjetoAccesoDato) && isset($idDeEmpleado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where p.idDeEmpleado = :idDeEmpleado");
            $consulta->bindValue(':idDeEmpleado',$idDeEmpleado,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaFiltrada =  Pedido::CrearLista($data);
        }

        return  $listaFiltrada;
    }

    public static function FiltrarPorIdDeSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePedidos = null;

        if(isset($unObjetoAccesoDato) && isset($idDeSector))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where p.idDeSector = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePedidos = Pedido::CrearLista($data);
        }

        return  $listaDePedidos;
    }

    public static function FiltrarPorEstadoBD($estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePedidos = null;

        if(isset($unObjetoAccesoDato) && isset($estado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where LOWER(p.estado) = LOWER(:estado)");
            $consulta->bindValue(':estado',$estado,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePedidos = Pedido::CrearLista($data);
        }

        return  $listaDePedidos;
    }

    public static function ModificarEstadoDeMesaBD($idDePedido,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unPedido = null;

        if(isset($unObjetoAccesoDato) && isset($idDePedido) && isset($estado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET estado = :estado 
            where p.id = :idDePedido");
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->bindValue(':idDePedido',$idDePedido,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unPedido =  Pedido::CrearUnaPedido($data);
        }

        return  $unPedido;
    }

   
    public static function ObtenerListaBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePedidos = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePedidos = Pedido::CrearLista($data);
        }

        return  $listaDePedidos;
    }

    #end

    public static function FiltrarPorEstado($listaDePedidos,$estado)
    {
        $listaFiltrada = null;

        if(isset($listaDePedidos) && isset($estado) && count($listaDePedidos) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDePedidos as $unPedido)
            {
                
                if(strcasecmp($unPedido->estado,$estado) === 0)
                {
                    array_push($listaFiltrada,$unPedido);
                }
            }
        }

        return  $listaFiltrada;
    }
    private static function CrearLista($data)
    {
        $listaDeEmpleados = null;
        if(isset($data))
        {
            $listaDeEmpleados = [];

            foreach($data as $unArray)
            {
                $unEmpleado = Pedido::CrearUnaPedido($unArray);
                if(isset($unEmpleado))
                {
                    array_push($listaDeEmpleados,$unEmpleado);
                }
            }
        }

        return   $listaDeEmpleados;
    }

    private static function CrearUnaPedido($data)
    {
        $unaPedido = null;

        if(isset($data))
        {
            $unaPedido = new Pedido();
            $unaPedido->SetId($data['id']);
            $unaPedido->SetIdOrden($data['idDeOrden']);
            $unaPedido->SetEmpleado($data['idDeEmpleado']);
            $unaPedido->SetIdProducto($data['idDeProducto']);
            $unaPedido->SetNumeroDePedido($data['numeroDePedido']);
            if(isset($data['tiempoEstimado']) && isset($data['tiempoDeFinalizacion']) && isset($data['tiempoDeInicio']))
            {
                $unaPedido->SetTiempoEstimado(DateInterval::createFromDateString($data['tiempoEstimado']));
                $unaPedido->SetTiempoDeFinalizacion(new DateTime($data['tiempoDeFinalizacion']));
                $unaPedido->SetTiempoDeInicio(new DateTime($data['tiempoDeInicio']));
            }
            $unaPedido->SetIdSector($data['idDeSector']);
            $unaPedido->SetEstado($data['estado']);
        }

        return  $unaPedido;
    }

    public function CantidadDeUnPedido($listaDePedidos,$unPedido)
    {
        $cantidad = -1;
        if(isset($listaDePedidos) && isset($unPedido))
        {
            $cantidad = 0;
            foreach ($listaDePedidos as $unPedidoDeLaLista) {
               
                if($unPedidoDeLaLista->Equals($unPedido))
                {
                    $cantidad++;
                }                
            }
        }

        return  $cantidad;
    }
    public function FiltrarPorEstadoDelTiempo($estadoDelTiempo)
    {
        $listafiltrada = null;
        $cantidad = 0;
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
    
            if(isset($unObjetoAccesoDato) && isset($estadoDelTiempo))
            {
                $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p 
                where LOWER(p.estadoDelTiempo) = LOWER(:estadoDelTiempo)");
                $consulta->bindValue(':estadoDelTiempo',$estadoDelTiempo,PDO::PARAM_STR);
                $consulta->execute();
                $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
                $listafiltrada = Pedido::CrearLista($data);
            }
    
            return  $listafiltrada;
    }

  

    // public function BuscarElMayor()
    // {
    //     $cantidadMayor = null;
    //     $flag = false;
    //     $listaDeProductos = Pedido::ObtenerListaDeProductosPedidos($listaDePedidos);
        
    //     if(isset($listaDeProductos) && isset($listaDeProductos))
    //     {
    //         foreach($listaDeProductos as $unProducto)
    //         {
    //             $cantidad = Producto::ContarProductos($listaDePedidos,$unProducto);

                
    //             if($cantidad > $cantidadMayor || $flag === false)
    //             {
    //                 $cantidadMayor = $cantidad;
    //                 $flag = true;
    //             }
    //         }
    //     }
    // }
   
    

    public function Equals($unPedido)
    {
        $estado = false;
 
        if(isset($unPedido))
        {
            $estado =  $unPedido->unProducto->Equals($this->unProducto);
        }
        return  $estado ;
    }

    //Setters
    private function SetId($id)
    {
        $estado = false;
        if(isset($id) && $id > 0)
        {
            $this->id = $id;
            $estado = true;
        }

        return  $estado ;
    }

  
    private function SetEstado($estadoDelaPedido)
    {
        $estado = false;
        if(isset($estado))
        {
            $this->estado = $estadoDelaPedido;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetTiempoDeFinalizacion($tiempoDeFinalizacion)
    {
        $estado = false;
        if(isset($tiempoDeFinalizacion))
        {
            $this->tiempoDeFinalizacion = $tiempoDeFinalizacion;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetTiempoDeInicio($tiempoDeInicio)
    {
        $estado = false;
        if(isset($tiempoDeInicio))
        {
            $this->tiempoDeInicio = $tiempoDeInicio;
            $estado = true;
        }

        return  $estado ;
    }

    public function SetTiempoEstimado($tiempoEstimado)
    {
        $estado = false;
        if(isset($tiempoEstimado))
        {
            $this->tiempoEstimado = $tiempoEstimado;
            $estado = true;
        }

        return  $estado ;
    }
    

    private function SetNumeroDePedido($numeroDePedido)
    {
        $estado = false;
        if(isset($numeroDePedido) && $numeroDePedido > 0)
        {
            $this->numeroDePedido = $numeroDePedido;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetIdSector($idDeSector)
    {
        $unSector =  Sector::BuscarSectorPorIdBD($idDeSector);
        $estado  = false;
        if(isset( $unSector))
        {
            $this->idDeSector = $idDeSector;
        }

        return $estado;
    }
    private function SetIdProducto($idDeProducto)
    {
        $unProducto =  Producto::BuscarProductoPorIdBD($idDeProducto);
        $estado  = Pedido::SetProducto($unProducto);
        return $estado;
    }
 

    private function SetProducto($unProducto)
    {
        $estado = false;
        if(isset( $unProducto))
        {
            $this->unProducto = $unProducto;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetEstadoDelTiempo($estadoDelTiempo)
    {
        $estado = false;
        if(isset( $estadoDelTiempo))
        {
            $this->estadoDelTiempo = $estadoDelTiempo;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetOrden($unaOrden)
    {
        $estado = false;
        if(isset($unaOrden))
        {
            $this->orden = $unaOrden;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetEmpleado($idDeEmpleado)
    {
        $estado = false;
        if(isset($idDeEmpleado))
        {
            $this->idDeEmpleado = $idDeEmpleado;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetIdOrden($idDeOrden)
    {
        $unaOrden =  Orden::BuscarOrdenPorIdBD($idDeOrden);
        $estado  = Pedido::SetOrden($unaOrden);
        return $estado;
    }


    //Getters

    public function GetImporteTotal()
    {
        return  $this->CalcularImporteTotal();
    }

    public function GetEstado()
    {
        return  $this->estado;
    }
    public function GetTiempoEstimado()
    {
        return  $this->tiempoEstimado;
    }
    public function GetStrTiempoEstimado()
    {
        $mensaje = "No definido";

        if(isset($this->tiempoEstimado) 
        && ($this->tiempoEstimado->h > 0 || $this->tiempoEstimado->m > 0) )
        {
            $mensaje = $this->tiempoEstimado->format('%h hours %i minutes');;
        }

        return  $mensaje;
    }
    public function GetTiempoDeInicio()
    {
        return  $this->tiempoDeInicio->format("H:i:s");
    }

    public function GetTiempoDeFinalizacion()
    {
        return  $this->tiempoDeFinalizacion->format("H:i:s");
    }


    public function GetNumeroDePedido()
    {
        return  $this->numeroDePedido;
    }
    public function GetestadoDelTiempo()
    {
        return  $this->estadoDelTiempo;
    }


    public static function ToStringList($listaDePedidos)
    {
        $strLista = null; 

        if(isset($listaDePedidos) )
        {
            $strLista  = "Pedidos".'<br>';
            foreach($listaDePedidos as $unaPedido)
            {
                $strLista .= $unaPedido->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return 
        "tiempo De Preparacion: ".$this->GetStrTiempoEstimado().'<br>'.
        "Cliente que lo pidio: ".$this->orden->GetNombreDelCliente().'<br>'.
        "Producto Pedido: ".'<br>'.$this->unProducto->ToString().'<br>'.
        "importe Total: ".$this->GetImporteTotal().'<br>'
        ."Estado: ".$this->estado.'<br>';
    }

  

    //  public static function EscribirJson($listaDePedido,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDePedido))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDePedido,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Pedido::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDePedido = null; 
    //      $unPedido = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDePedido = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unPedido = Pedido::DeserializarUnPedidoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unPedido))
    //              {
    //                  array_push($listaDePedido,$unPedido);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDePedido ;
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

    
   


   

    // public static function CompararPorclave($unPedido,$otroPedido)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unPedido->clave,$otroPedido->clave);

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

    // public static function BuscarPedidoPorId($listaDePedido,$id)
    // {
    //     $unaPedidoABuscar = null; 

    //     if(isset($listaDePedido) )
    //     {
    //         foreach($listaDePedido as $unaPedido)
    //         {
    //             if($unaPedido->id == $id)
    //             {
    //                 $unaPedidoABuscar = $unaPedido; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaPedidoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unPedido,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unPedido = $unPedido;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Pedido::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarPedidoPorId($listaDePedidos,$id)
    // {
    //     $unaPedidoABuscar = null; 

    //     if(isset($listaDePedidos)  
    //     && isset($id) )
    //     {
    //         foreach($listaDePedidos as $unaPedido)
    //         {
    //             if($unaPedido->id == $id)
    //             {
    //                 $unaPedidoABuscar = $unaPedido; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaPedidoABuscar;
    // }
  
    

//Filtrar




     //  //Contar
 
    //  public static function ContarPorUnaFecha($listaDePedido,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDePedido) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDePedido as $unaPedido)
    //          {
    //              if($unaPedido::$fechaDePedido == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>