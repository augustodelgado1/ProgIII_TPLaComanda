

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/File.php';
require_once './Clases/Mesa.php';
require_once './Clases/Pedido.php';
require_once './Clases/Encuesta.php';
require_once './Clases/Util.php';

class Orden 
{
    public const ESTADO_ACTIVO = "activo";
    public const ESTADO_INACTIVO = "inactivo";
    private $id;
    private $codigo;
    private $nombreDelCliente;
    private $idDeMesa;
    private $fechaDeOrden;
    private $rutaDeLaImagen;
    private $nombreDeLaImagen;
    private $costoTotal;
    private $tiempoTotal;
    private $estado;
    private $listaDePedidos;

    public function __construct($nombreDelCliente,$idDeMesa,$rutaDeLaImagen = null,$nombreDeLaImagen = null) 
    {
        $this->idDeMesa = $idDeMesa;
        $this->nombreDelCliente = $nombreDelCliente;
        $this->fechaDeOrden = new DateTime('now');
        $this->estado = "activa";
        $this->costoTotal = 0;
        $this->codigo = Util::CrearUnCodigoAlfaNumerico(5);
        $this->SetImagen($rutaDeLaImagen,$nombreDeLaImagen);
        $this->CalcularTiempoTotal();
        $this->CalcularCostoTotal();

        
    }
    private function CalcularCostoTotal()
    {
        $this->costoTotal = 0;

        $this->listaDePedidos = $this->ObtenerListaDePedidos();
        if(isset( $this->listaDePedidos))
        {
            foreach ($this->listaDePedidos as $unPedido) {
               
                if(isset($unPedido))
                {
                    $this->costoTotal += $unPedido->GetImporteTotal();
                }
            }
        }
        return $this->costoTotal;
    }
    private function CalcularTiempoTotal()
    {
        $tiempoTotal = null;
        $this->listaDePedidos = $this->ObtenerListaDePedidos();

        if(isset($this->listaDePedidos) && count($this->listaDePedidos) > 0)
        {
            $tiempoTotal = new DateTime('00:00');
            foreach ($this->listaDePedidos as $unPedido) {
               
                if(isset($unPedido) && $unPedido->GetEstado() === Pedido::ESTADO_INTERMEDIO)
                {
                    $tiempoTotal->add($unPedido->GetTiempoEstimado());
                }
            }
        }

        return $tiempoTotal;
    }

    public function ObtenerListaDePedidos()
    {
        $cantidad = Pedido::ContarPedidosPorIdDeOrdenBD($this->id);
      
        if((isset($this->listaDePedidos) == false || 
        (isset($this->listaDePedidos) && count($this->listaDePedidos) <= $cantidad)) &&  $cantidad > 0)
        {
            echo "Entro";
            $this->listaDePedidos = Pedido::FiltrarPedidosPorIdDeOrdenBD($this->id);
        }
        
        return  $this->listaDePedidos;
    }
    public function ObtenerListaDeEncuestas()
    {
        return  Encuesta::FiltrarPorIdDeOrdenBD($this->id);
    }

    public function ObtenerUnPedidoPendiente()
    {
        $unPedido = null;
        $listaDePedidos = $this->ObtenerListaDePedidos(); 
        $listaDeFiltrada = Pedido::FiltrarPorEstado($listaDePedidos,Pedido::ESTADO_INICIAL); 

        if(isset($listaDeFiltrada) && count($listaDePedidos) > 0)
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


    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Orden (codigo,nombreDelCliente,idDeMesa,fechaDeOrden,costoTotal,estado,rutaDeLaImagen,nombreDeLaImagen) 
            values (:codigo,:nombreDelCliente,:idDeMesa,:fechaDeOrden,:costoTotal,:tiempoTotal,:estado,:rutaDeLaImagen,:nombreDeLaImagen)");
            $consulta->bindValue(':codigo',$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(':nombreDelCliente',$this->nombreDelCliente,PDO::PARAM_STR);
            $consulta->bindValue(':idDeMesa',$this->idDeMesa,PDO::PARAM_INT);
            $consulta->bindValue(':fechaDeOrden',$this->fechaDeOrden->format("y-m-d"),PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->GetEstado(),PDO::PARAM_STR);
            $consulta->bindValue(':costoTotal',$this->costoTotal);
            $consulta->bindValue(':tiempoTotal',$this->tiempoTotal);
            $consulta->bindValue(':rutaDeLaImagen',$this->rutaDeLaImagen);
            $consulta->bindValue(':nombreDeLaImagen',$this->nombreDeLaImagen);
            $estado = $consulta->execute();
        }

        return $estado;
    }
    public static function ModificarUnoBD($id,$nombreDelCliente,$idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato) && isset($arrayDeEmpleado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden as o
            SET `nombreDelCliente`= :nombreDelCliente,
            `idDeMesa`= :idDeMesa,
            `rutaDeLaImagen`= :rutaDeLaImagen,
            `nombreDeLaImagen`= :nombreDeLaImagen,
            Where o.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':nombreDelCliente',$nombreDelCliente,PDO::PARAM_STR);
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Orden as o where o.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    
    public static function BuscarOrdenPorIdBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unaOrden = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.id = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
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
    public function VerificarIdDeMesa($idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o 
            where o.id = :id and o.idDeMesa = :idDeMesa");
            $consulta->bindValue(':idDeMesa',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $estado = $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $estado = $data !== null && count($data) > 0;
        }

        return  $estado;
    }
    public static function FiltrarPorImporteBD($importe)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.importe = :importe");
            $consulta->bindValue(':importe',$importe);
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
            $unaOrden = new Orden($unArrayAsosiativo['nombreDelCliente'],$unArrayAsosiativo['idDeMesa'],
            $unArrayAsosiativo['rutaDeLaImagen'],$unArrayAsosiativo['nombreDeLaImagen']);
            $unaOrden->SetId($unArrayAsosiativo['id']);
            $unaOrden->SetCodigo($unArrayAsosiativo['codigo']);
            $unaOrden->SetNombreDelCliente($unArrayAsosiativo['nombreDelCliente']);
            $unaOrden->SetFechaDeOrden($unArrayAsosiativo['fechaDeOrden']);
            $unaOrden->SetCostoTotal($unArrayAsosiativo['costoTotal']);
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
    protected function SetNombreDelCliente($nombreDelCliente)
    {
        $estado = false;
        if(isset($nombreDelCliente))
        {
            $this->nombreDelCliente = $nombreDelCliente;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetIdMesa($idDeMesa)
    {
        $estado = false;
        if(isset($idDeMesa))
        {
            $this->idDeMesa = $idDeMesa;
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
        return  $this->nombreDelCliente;
    }

    public function GetId()
    {
        return  $this->id;
    }
    public function GetMesa()
    {
        return  Mesa::BuscarMesaPorIdBD($this->idDeMesa);
    }

    public function GetStrTiempoEstimado()
    {
        $mensaje = "No definido";
        $this->CalcularTiempoTotal();
        if(isset($this->tiempoTotal) 
        && ($this->tiempoTotal->h > 0 || $this->tiempoTotal->m > 0) )
        {
            $mensaje = $this->tiempoTotal->format('H')." horas y ".$this->tiempoTotal->format('i')." minutos";
        }

        return  $mensaje;
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

    public function GetStrPedidos()
    {
        $ListaDePedidos = $this->ObtenerListaDePedidos();

        $mensaje = "no se encontraron pedidos";
        if(isset($ListaDePedidos) && count($ListaDePedidos) > 0)
        {
            $mensaje = Pedido::MostrarProductos($ListaDePedidos);
        }

        return  $mensaje ;
    }
    public function GetStrCosto()
    {
        $mensaje = "no definido";
        $costo = $this->CalcularCostoTotal();
        if($costo  > 0)
        {
            $mensaje = "$".$this->costoTotal;
        }

        return  $mensaje ;
    }

    public function ToString()
    {
        return "Codigo: ".strtoupper($this->codigo).'<br>'.
        "Productos Pedidos: ".'<br>'.$this->GetStrPedidos().'<br>'.
        "Tiempo Total De Espera: ".$this->GetStrTiempoEstimado().'<br>'.
        "Mesa: ".'<br>'.$this->GetMesa()->ToString().'<br>'.
        "Facturacion Total: ".$this->GetStrCosto();
    }

    public static function MostarComentarios($listaDeOrdenes,$listaDeEncuesta)
    {
        $strLista = null; 

        if(isset($listaDeOrdenes) && isset($listaDeEncuesta))
        {
            $strLista  = "Or".'<br>';
            foreach($listaDeOrdenes as $unaOrden)
            {
                $strLista .= "Orden: ".strtoupper($unaOrden->codigo).'<br>'.
                Encuesta::ToStringList(Encuesta::FiltrarPorLista($unaOrden->listaDeEncuesta,$listaDeEncuesta));
                 
            }
        }

        return   $strLista;
    }

    public static function FiltrarEntreDosFechas($listaDeOrdenes,$fechaDesde,$fechaHasta)
    {
       
        $listaDefiltrada = null;

        if(isset($listaDeOrdenes) && $fechaDesde <= $fechaHasta)
        {
            $listaDefiltrada = [];

            foreach ($listaDeOrdenes as $unaOrden) 
            {
                if($unaOrden->fechaDeOrden >= $fechaDesde 
                && $unaOrden->fechaDeOrden <= $fechaHasta)
                {
                    array_push($listaDefiltrada,$unaOrden);
                }
                
            }
        }

        return  $listaDeOrdenes;
    }

    public static function ValidarOrdenIngresada($data)
    {
        $estado = false;
        if(isset($data['codigoDeOrden']) && 
        Orden::BuscarPorCodigoBD($data['codigoDeOrden']) !== null)
        {
            $estado = true;
        }

        return $estado;
    }

    public static function BuscarElMayorImporte($listaDeOrdenes)
    { 
        $importe = 0;
        $flag = false;
        $mayor =null;

        if(isset($listaDeOrdenes))
        {
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                $importe = $unaOrden->importeTotal;

                if($mayor >  $importe || $flag === false)
                {
                    $mayor =  $importe;
                    $flag = true;
                }
                
            }
        }

        return $mayor;
    }
    public static function BuscarElMenorImporte($listaDeOrdenes)
    {
        $importe = 0;
        $flag = false;
        $menor =null;

        if(isset($listaDeOrdenes))
        {
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                $importe = $unaOrden->importeTotal;

                if($menor <  $importe || $flag === false)
                {
                    $menor =  $importe;
                    $flag = true;
                }
                
            }
        }

        return $menor;
    }
    public static function ObtenerFacturacionTotal($listaDeOrdenes)
    {
        $acumulador = -1;

        if(isset($listaDeOrdenes))
        {
            $acumulador = 0;
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                if($unaOrden->importeTotale > 0)
                {
                    $acumulador +=  $unaOrden->importeTotal;
                }
                
            }
        }

        return $acumulador;
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