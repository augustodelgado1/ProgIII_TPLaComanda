

<?php

require_once './db/AccesoDatos.php';
require_once './Herramientas/Util.php';
require_once './Clases/Mesa.php';
require_once './Clases/Pedido.php';
require_once './Clases/Encuesta.php';


class Orden 
{
    public const ESTADO_ACTIVO = "activa";
    public const ESTADO_INACTIVO = "inactiva";
    private $id;
    private $codigo;
    private $nombreDelCliente;
    private $idDeMesa;
    private $fechaDeOrden;
    private $rutaDeLaImagen;
    private $nombreDeLaImagen;
    private $costoTotal;
    private $tiempoDeInicio;
    private $tiempoDeFinalizacion;
    private $tiempoTotalEstimado;
    private $estadoDelTiempo;
    private $estado;
    private $listaDePedidos;

    public const ESTADO_TIEMPO_NOCUMPLIDO = "no cumplido";
    public const ESTADO_TIEMPO_CUMPLIDO = "cumplido";
    public const ESTADO_TIEMPO_INDETERMINADO = "indeterminado";


    public function __construct($nombreDelCliente,$idDeMesa,$rutaDeLaImagen = null,$nombreDeLaImagen = null) 
    {
        $this->idDeMesa = $idDeMesa;
        $this->nombreDelCliente = $nombreDelCliente;
        $this->fechaDeOrden = new DateTime('now');
        $this->estado = "activa";
        $this->costoTotal = 0;
        $this->codigo = Util::CrearUnCodigoAlfaNumerico(5);
        $this->SetImagen($rutaDeLaImagen,$nombreDeLaImagen);
        $this->CalculartiempoTotalEstimado();
        $this->CalcularCostoTotal();
    }

    private function ObetenerEstadoDelTiempo()
    {
        $this->estadoDelTiempo = Pedido::ESTADO_TIEMPO_INDETERMINADO;

        $diferencia = $this->tiempoDeInicio->diff($this->tiempoDeFinalizacion);

        if(isset($diferencia))
        {
            if($diferencia > $this->tiempoTotalEstimado)
            {
                $this->estadoDelTiempo  = Pedido::ESTADO_TIEMPO_NOCUMPLIDO;
            }else
            {
                if($diferencia < $this->tiempoTotalEstimado)
                {
                    $this->estadoDelTiempo  = Pedido::ESTADO_TIEMPO_CUMPLIDO;
                }
            }
        }
        
        
        return  $this->estadoDelTiempo ;
    }

    public function ModificarTiempoDeInicioBD($tiempoInicio)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if($this->SetTiempoDeInicio($tiempoInicio))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.tiempoDeInicio = :tiempoInicio 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoInicio',$this->tiempoDeInicio->format('Y-m-d-H-i-s'),PDO::PARAM_STR);
            $estado =$consulta->execute();
            // var_dump($estado);
        }

        return  $estado;
    }
    public function ModificarTiempoDeFinalizacionBD($tiempoDeFinalizacion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;
        // var_dump($tiempoDeFinalizacion);
        if($this->SetTiempoDeFinalizacion($tiempoDeFinalizacion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p SET p.tiempoDeFinalizacion = :tiempoDeFinalizacion where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoDeFinalizacion',$this->tiempoDeFinalizacion->format('Y-m-d-H-i-s'),PDO::PARAM_STR);
            $consulta->execute();
            $estado = $consulta->execute();
        }

        return  $estado;
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
    private function CalculartiempoTotalEstimado()
    {
        $tiempoTotalEstimado = null;
        $this->listaDePedidos = $this->ObtenerListaDePedidos();
       
        if(isset($this->listaDePedidos) && count($this->listaDePedidos) > 0)
        {
            $tiempoTotalEstimado = new DateTime('00:00');
            foreach ($this->listaDePedidos as $unPedido) {
               
                if(isset($unPedido) && $unPedido->GetEstado() === Pedido::ESTADO_INTERMEDIO)
                {
                    $tiempoTotalEstimado->add($unPedido->GetTiempoEstimado());  
                }
            }
        }

        return $tiempoTotalEstimado;
    }

    public function ObtenerListaDePedidos()
    {
        $cantidad = Pedido::ContarPedidosPorIdDeOrdenBD($this->id);
      
       
        if((isset($this->listaDePedidos) == false || 
        (isset($this->listaDePedidos) && count($this->listaDePedidos) <= $cantidad)) &&  $cantidad > 0)
        {
           
            $this->listaDePedidos = Pedido::FiltrarPedidosPorIdDeOrdenBD($this->id);
        }
        
        return  $this->listaDePedidos;
    }

    public static function ContarPorIdDeMesaBD($idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidadTotal = null;

        if(isset($idDeMesa))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS total FROM Orden as o where o.idDeMesa = :idDeMesa");
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidadTotal =  $data['total'];
        }

        return  $cantidadTotal;
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
        
        $consulta = $objAccesoDatos->RealizarConsulta("Insert into 
        Orden (codigo,nombreDelCliente,idDeMesa,fechaDeOrden,costoTotal,estado) 
        values (:codigo,:nombreDelCliente,:idDeMesa,:fechaDeOrden,:costoTotal,:estado)");
        $consulta->bindValue(':codigo',$this->codigo,PDO::PARAM_STR);
        $consulta->bindValue(':nombreDelCliente',$this->nombreDelCliente,PDO::PARAM_STR);
        $consulta->bindValue(':idDeMesa',$this->idDeMesa,PDO::PARAM_INT);
        $consulta->bindValue(':fechaDeOrden',$this->fechaDeOrden->format("y-m-d"),PDO::PARAM_STR);
        $consulta->bindValue(':estado',$this->GetEstado(),PDO::PARAM_STR);
        $consulta->bindValue(':costoTotal',$this->costoTotal);
        $estado = $consulta->execute();
        

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

    public function ModificarEstadoBD($estadoDelaOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if($this->SetEstado($estadoDelaOrden))
        {
            // var_dump($estadoDelaOrden);
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden 
            as o SET estado = :estado where o.id = :id");
            $consulta->bindValue(':estado',$estadoDelaOrden,PDO::PARAM_STR);
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public function ActualizarImporte()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden 
            as o SET costoTotal = :costoTotal where o.id = :id");
            $consulta->bindValue(':costoTotal',$this->CalcularCostoTotal());
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
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
    public static function FiltrarPorFechaBD($fecha)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.fechaDeOrden = :fecha");
            $consulta->bindValue(':fecha',$fecha,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }
    public static function ObtenerUnoPorIdDeMesaYEstadoBD($idDeMesa,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unaOrden = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.idDeMesa = :idDeMesa and o.estado = :estado");
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unaOrden = Orden::CrearUnaOrden($data);
        }

        return  $unaOrden;
    }
    public static function BuscarMayorImportePorFechaBD($fecha)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $mayorImporte = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT MAX(costoTotal) as importeTotal
            FROM orden
            WHERE estado = :estado and o.fechaDeOrden = :fecha");
            $consulta->bindValue(':estado',ORDEN::ESTADO_INACTIVO,PDO::PARAM_STR);
            $consulta->bindValue(':fecha',$fecha,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $mayorImporte = $data['importeTotal'];
            // var_dump($mayorImporte);
        }

        return  $mayorImporte;
    }
    public static function BuscarMenorImportePorFechaBD($fecha)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $menorImporte = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT MIN(costoTotal) as importeTotal
            FROM orden
            WHERE estado = :estado and o.fechaDeOrden = :fecha");
            $consulta->bindValue(':estado',ORDEN::ESTADO_INACTIVO,PDO::PARAM_INT);
            $consulta->bindValue(':fecha',$fecha,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $menorImporte = $data['importeTotal'];
        }

        return  $menorImporte;
    }
    public static function FiltrarPorImporteBD($importe)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($importe))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.costoTotal = :importe");
            $consulta->bindValue(':importe',$importe);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }

    public static function FiltrarPorEstadoDelTiempoBD($estadoDelTiempo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada= null;

        if(isset($estadoDelTiempo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden 
            as o where o.estadoDelTiempo = :estadoDelTiempo");
            $consulta->bindValue(':estadoDelTiempo',$estadoDelTiempo,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaFiltrada =  Orden::CrearLista($data);
        }

        return $listaFiltrada;
    }
  
    public static function BuscarPorCodigoBD($codigo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = false;

        if(isset($codigo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o
             where LOWER(o.codigo) = LOWER(:codigo)");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
             
        }

        return  $data;
    }
    public static function ObtenerUnoPorCodigo($codigo)
    {
        return  Orden::CrearUnaOrden(Orden::BuscarPorCodigoBD($codigo));
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
            $unaOrden->SetFechaDeOrden(new DateTime($unArrayAsosiativo['fechaDeOrden']));
            $unaOrden->SetCostoTotal($unArrayAsosiativo['costoTotal']);
            $unaOrden->SetEstado($unArrayAsosiativo['estado']);
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

    public static function ContarPorIdDeMesa($listaDeOrdenes,$idDeMesa)
    { 
        $cantidadDeOrdenes = -1;

        if(isset($listaDeOrdenes))
        {
            $cantidadDeOrdenes = 0;
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                
                if($unaOrden->idDeMesa === $idDeMesa)
                {
                    $cantidadDeOrdenes++;
                }
                
            }
        }

        return $cantidadDeOrdenes;
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

    private function SetEstadoDelTiempo($estadoDelTiempo)
    {
        $estado = false;
        $array = array(Orden::ESTADO_TIEMPO_CUMPLIDO,Orden::ESTADO_TIEMPO_NOCUMPLIDO,Orden::ESTADO_TIEMPO_INDETERMINADO);

        if(isset($estadoDelTiempo) && in_array($estadoDelTiempo,$array))
        {
            $this->estadoDelTiempo = $estadoDelTiempo;
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

    private function SetEstado($estadoDelaOrden)
    {
        $estado = false;

        if(Orden::ValidadorEstado($estadoDelaOrden))
        {
            $this->estado = $estadoDelaOrden;
            $this->CalcularCostoTotal();
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


    #Getters

    public function GetStrTiempoInicio()
    {
        $mensaje = "No definido";

        if(isset($this->tiempoDeInicio))
        {
            $mensaje = $this->tiempoDeInicio->format('Y-m-d-H-i-s'); 
        }

        return  $mensaje;
    }
    public function GetStrTiempoFinalizacion()
    {
        $mensaje = "No definido";
      
        if(isset($this->tiempoDeFinalizacion))
        {
            $mensaje = $this->tiempoDeFinalizacion->format('Y-m-d-H-i-s');; 
           
        }

        return  $mensaje;
    }
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
    public function GetFechaStr()
    {
        return  $this->fechaDeOrden->format('Y-m-d-H-i-s');
    }
    public function GetFecha()
    {
        return  $this->fechaDeOrden;
    }
    public function GetMesa()
    {
        return  Mesa::BuscarUnoPorIdBD($this->idDeMesa);
    }
    public function GetIdDeMesa()
    {
        return  $this->idDeMesa;
    }

    public function GetStrTiempoEstimado()
    {
        $mensaje = "No definido";
        $this->tiempoTotalEstimado = $this->CalculartiempoTotalEstimado();

        if(isset($this->tiempoTotalEstimado) 
        && ($this->tiempoTotalEstimado->format('H') != '00' || 
           $this->tiempoTotalEstimado->format('i') != '00'))
        {
            $mensaje = $this->tiempoTotalEstimado->format('H')
            ." horas y ".$this->tiempoTotalEstimado->format('i')
            ." minutos";
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
            $strLista  = "";
            foreach($listaDeOrdenes as $unaOrden)
            {
                $listafiltrada = Encuesta::FiltrarPorIdDeOrdenes($listaDeEncuesta,$unaOrden->id);

                if(isset($listafiltrada) && count($listafiltrada) > 0)
                {
                     $strLista .= "Orden: ".strtoupper($unaOrden->codigo).'<br>'.
                    Encuesta::ToStringList( $listafiltrada);
                }
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
                if( $unaOrden->fechaDeOrden >= $fechaDesde
                && $unaOrden->fechaDeOrden <= $fechaHasta )
                {
                    array_push($listaDefiltrada,$unaOrden);
                }
            }
        }

        return  $listaDefiltrada;
    }

    public static function FiltrarPorEstado($listaDeOrdenes,$estado)
    {
        $listaFiltrada = null;

        if(isset($listaDeOrdenes) && isset($estado) && count($listaDeOrdenes) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDeOrdenes as $unaOrden)
            {
                if(strcasecmp($unaOrden->estado,$estado) === 0)
                {
                    array_push($listaFiltrada,$unaOrden);
                }
            }
        }

        return  $listaFiltrada;
    }
    public static function FiltrarPorFecha($listaDeOrdenes,$fechaDeOrden)
    {
        $listaFiltrada = null;

        if(isset($listaDeOrdenes) && isset($fechaDeOrden) && count($listaDeOrdenes) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDeOrdenes as $unaOrden)
            {
                if($unaOrden->fechaDeOrden === $fechaDeOrden)
                {
                    array_push($listaFiltrada,$unaOrden);
                }
            }
        }

        return  $listaFiltrada;
    }
    public static function FiltrarOrdenesPorIdDeMesa($listaDeOrdenes,$idDeMesa)
    {
       
        $listaDefiltrada = null;

        if(isset($listaDeOrdenes) && isset($idDeMesa))
        {
            $listaDefiltrada = [];

            foreach ($listaDeOrdenes as $unaOrden) 
            {
                if($unaOrden->idDeMesa === $idDeMesa)
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
    public static function CalcularFacturacionTotal($listaDeOrdenes)
    {
        $acumulador = -1;

        if(isset($listaDeOrdenes))
        {
            $acumulador = 0;
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                if($unaOrden->costoTotal > 0)
                {
                    $acumulador +=  $unaOrden->costoTotal;
                }
                
            }
        }

        return $acumulador;
    }

    #Validaciones

    public static function Validador($data)
    {
        return  Orden::ValidarNombreDeCliente($data['nombreDelCliente']) 
                && ($unaMesa = Mesa::BuscarMesaPorCodigoBD($data['codigoDeMesa'])) !== null
                && $unaMesa['estado'] === Mesa::ESTADO_CERRADO; ;
    }
      
    private static function ValidarNombreDeCliente($nombreDelCliente)
    {
        return Util::ValidadorDeNombre($nombreDelCliente) ;
    }


    public static function VerificarUnoPorCodigo($codigo)
    {
        return  Orden::BuscarPorCodigoBD($codigo) !== false;
    }
    public static function ValidadorCodigo($data)
    {
        return  Orden::VerificarUnoPorCodigo($data['codigoDeOrden']);
    }
  
      private static function ValidadorEstado($estadoDelaOrden)
      {
          $array = array(Orden::ESTADO_ACTIVO,Orden::ESTADO_INACTIVO);
  
          return  isset($estadoDelaOrden) && in_array($estadoDelaOrden,$array);
      }
  
      
      #End

   
}


?>