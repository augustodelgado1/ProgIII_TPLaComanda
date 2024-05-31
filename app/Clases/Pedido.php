

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class Pedido 
{
    private $id;
    private $orden;
    private $idDeSector;
    private $numeroDePedido;
    private $unProducto;
    private $cantidad;
    private $tiempoDePreparacion;
    private $tiempoDeEntrega;
    private $importeTotal;
    private $estado;

    private function __construct( $orden,$unProducto,$cantidad) 
    {
        $this->numeroDePedido = rand(100,1000);
        $this->orden = $orden;
        $this->unProducto = $unProducto;
        $this->SetCantidad($cantidad);
        $this->estado = "pendiente";
        $this->CalcularImporteTotal();
    }

    private function CalcularImporteTotal()
    {
        $this->importeTotal = 0;

        if(isset($this->unProducto) && isset($this->cantidad)  && $this->cantidad > 0)
        {
            $this->importeTotal =  $this->unProducto->GetPrecio() * $this->cantidad;
        }
    }

    private function ObtenerSector()
    {
        $this->idDeSector = 0;

        if(isset($this->unProducto))
        {
            $this->idDeSector =  $this->unProducto->GetTipo()->ObtenerSector();
        }
    }
    public static function Alta($orden,$unProducto,$cantidad)
    {
        $unPedido = new Pedido($orden,$unProducto,$cantidad);
        $estado = false;
        if(empty($unPedido->cantidad) == false)
        {
            $estado = $unPedido->AgregarBD();
        }

        return $estado;
    }
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Pedido (numeroDePedido,idDeLaOrden,idDeSector,idDeProducto,cantidad,tiempoDePreparacion) 
            values (:numeroDePedido,:idDeLaOrden,:idDeSector,:idDeProducto,:cantidad,:tiempoDePreparacion,:tiempoDeEntrega,:importeTotal,:estado)");
            $consulta->bindValue(':numeroDePedido',$this->numeroDePedido,PDO::PARAM_INT);
            $consulta->bindValue(':idDeSector',$this->idDeSector,PDO::PARAM_INT);
            $consulta->bindValue(':idDeLaOrden',$this->orden->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':idDeProducto',$this->unProducto->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':cantidad',$this->cantidad,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoDePreparacion',$this->tiempoDePreparacion->format("H:i:s"),PDO::PARAM_STR);
            $consulta->bindValue(':tiempoDeEntrega',$this->tiempoDeEntrega->format("H:i:s"),PDO::PARAM_STR);
            $consulta->bindValue(':importeTotal',$this->importeTotal);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }
    public static function BuscarPedidoPorNumeroDePedidoBD($numeroDePedido)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unPedido = null;

        if(isset($unObjetoAccesoDato))
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

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido as p where p.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaFiltrada =  Pedido::CrearLista($data);
        }

        return  $listaFiltrada;
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
            $unaPedido = new Pedido($data['idDeLaOrden'],$data['idDeProducto'],$data['cantidad']);
            $unaPedido->SetId($data['id']);
            $unaPedido->SetOrden($data['idDeOrden']);
            $unaPedido->SetProducto($data['idDeProducto']);
            $unaPedido->SetNumeroDePedido($data['numeroDePedido']);
            $unaPedido->SetTiempoDePreparacion(new DateTime($data['tiempoDePreparacion']));
            $unaPedido->SetTiempoDeEntrega(new DateTime($data['tiempoDeEntrega']));
            $unaPedido->SetSector($data['idDeSector']);
            $unaPedido->SetEstado($data['estado']);
        }

        return  $unaPedido;
    }

   
    

    public function Equals($unPedido)
    {
        $estado = false;
 
        if(isset($unPedido))
        {
            $estado =  $unPedido->numeroDePedido === $this->numeroDePedido;
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
    private function SetCantidad($cantidad)
    {
        $estado = false;
        if(isset($cantidad) && $cantidad > 0)
        {
            $this->cantidad = $cantidad;
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

    private function SetTiempoDeEntrega($tiempoDeEntrega)
    {
        $estado = false;
        if(isset($tiempoDeEntrega))
        {
            $this->tiempoDeEntrega = $tiempoDeEntrega;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetTiempoDePreparacion($tiempoDePreparacion)
    {
        $estado = false;
        if(isset($tiempoDePreparacion))
        {
            $this->tiempoDePreparacion = $tiempoDePreparacion;
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
    private function SetSector($idDeSector)
    {
        $unSector =  Sector::BuscarSectorPorIdBD($idDeSector);
        $estado  = false;
        if(isset( $unSector))
        {
            $this->idDeSector = $idDeSector;
        }

        return $estado;
    }

    private function SetProducto($idDeProducto)
    {
        $unaProducto =  Producto::BuscarProductoPorIdBD($idDeProducto);
        $estado  = false;
        if(isset( $unaProducto))
        {
            $this->unProducto = $unaProducto;
        }

        return $estado;
    }
    private function SetOrden($idDeOrden)
    {
        $unaOrden =  Orden::BuscarOrdenPorIdBD($idDeOrden);
        $estado  = false;
        if(isset( $unaOrden))
        {
            $this->orden = $unaOrden;
        }

        return $estado;
    }

    

    //Getters

    public function GetImporteTotal()
    {
        return  $this->importeTotal;
    }

    public function GetEstado()
    {
        return  $this->estado;
    }

    public function GetNumeroDePedido()
    {
        return  $this->numeroDePedido;
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
        "Numero De Pedido: ".$this->numeroDePedido.'<br>'.
        "tiempo De Preparacion: ".$this->tiempoDePreparacion.'<br>'.
        "Cliente que lo pidio: ".$this->orden->GetNombreDelCliente().'<br>'.
        "Producto Pedido: ".$this->unProducto->ToString().'<br>'.
        "cantidad: ".$this->cantidad.'<br>'.
        "importe Total: ".$this->importeTotal.'<br>'
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