

<?php

require_once './db/AccesoDatos.php';
require_once 'Producto.php';
require_once 'Orden.php';

class Pedido 
{
    
    private $id;
    private $orden;
    private $idDeSector;
    private $idDeEmpleado;
    private $codigo;
    private $unProducto;
    private $fechaDelPedido;
    private $tiempoEstimado;
    private $tiempoDeInicio;
    private $tiempoDeFinalizacion;
    private $importeTotal;
    private $estado;
    private $estadoDelTiempo;

    public const ESTADO_INICIAL = "pendiente";
    public const ESTADO_INTERMEDIO = "en preparacion";
    public const ESTADO_FINAL = "listo para servir";
    public const ESTADO_CANCELADO = "cancelado";
    public const ESTADO_TIEMPO_NOCUMPLIDO = "no cumplido";
    public const ESTADO_TIEMPO_CUMPLIDO = "cumplido";
    public const ESTADO_TIEMPO_INDETERMINADO = "indeterminado";

    public function __construct($idDeOrden,$idDeProducto) 
    {
        $this->codigo = Util::CrearUnCodigoAlfaNumerico(5);
        $this->orden = $idDeOrden;
        $this->unProducto = $idDeProducto;
        $this->estado = Pedido::ESTADO_INICIAL;
        $this->fechaDelPedido = new DateTime("now");
        $this->tiempoEstimado = new DateInterval('PT0H0M');
        $this->estadoDelTiempo = Pedido::ESTADO_TIEMPO_INDETERMINADO;
        $this->ObtenerSector();
        $this->CalcularImporteTotal();
    }

    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("
           Insert into Pedido (codigo,idDeOrden,idDeSector,idDeProducto,importeTotal,fechaDePedido,estado,estadoDelTiempo) 
        values (:codigo,:idDeOrden,:idDeSector,:idDeProducto,:importeTotal,:fechaDePedido,:estado,:estadoDelTiempo)");
            $consulta->bindValue(':codigo',$this->codigo,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$this->idDeSector,PDO::PARAM_INT);
            $consulta->bindValue(':idDeOrden',$this->orden,PDO::PARAM_INT);
            $consulta->bindValue(':idDeProducto',$this->unProducto,PDO::PARAM_INT);
            $consulta->bindValue(':importeTotal',$this->importeTotal);
            $consulta->bindValue(':fechaDePedido',$this->fechaDelPedido->format('y-m-d'),PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->bindValue(':estadoDelTiempo',$this->estadoDelTiempo,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }
    public static function ModificarUnoBD($id,$idDeOrden,$idDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p
            SET `codigo`= :codigo,
                `idDeOrden`= :idDeOrden,
                `idDeProducto`= :idDeProducto,
            Where p.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->bindValue(':idDeProducto',$idDeProducto,PDO::PARAM_INT);
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
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Pedido where id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarIdDeEmpleadoBD($idDeEmpleado)
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
    public function ModificarEstadoBD($estadoDelPedido)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if($this->SetEstado($estadoDelPedido))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.estado = :estado 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estadoDelPedido,PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    public function ModificarTiempoEstimadoBD($tiempoEstimado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($tiempoEstimado) && $this->SetTiempoEstimado($tiempoEstimado))
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
    public function EvaluarEstadoDelTiempo()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        $this->SeEntregoEnElTimpoEstimado();

        if(isset($unObjetoAccesoDato) && $this->SetEstadoDelTiempo($this->estadoDelTiempo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Pedido as p 
            SET p.estadoDelTiempo = :estadoDelTiempo 
            where p.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':estadoDelTiempo',$this->estadoDelTiempo,PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
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
            $consulta->bindValue(':tiempoInicio',$this->GetTiempoDeInicio(),PDO::PARAM_STR);
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
            $consulta->bindValue(':tiempoDeFinalizacion',$this->GetTiempoDeFinalizacion(),PDO::PARAM_STR);
            $consulta->execute();
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    private static function BuscarPorCodigoBD($codigo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = false;

        if(isset($codigo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM 
            Pedido as p where LOWER(p.codigo) = LOWER(:codigo)");
            $consulta->bindValue(':codigo',$codigo,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $data;
    }
    public static function ObtenerUnoPorCodigoBD($codigo)
    {
        return  Pedido::CrearUnaPedido( Pedido::BuscarPorCodigoBD($codigo));
    }

    public static function FiltrarPorFechaDePedidoBD($fechaDePedido)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada= null;

        if(isset($unObjetoAccesoDato) && isset($fechaDePedido))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido 
            as p where p.fechaDePedido = :fechaDePedido");
            $consulta->bindValue(':fechaDePedido',$fechaDePedido->format('y-m-d'),PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaFiltrada =  Pedido::CrearLista($data);
        }

        return $listaFiltrada;
    }
    public static function FiltrarPorEstadoDelTiempoBD($estadoDelTiempo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada= null;

        if(isset($unObjetoAccesoDato) && isset($estadoDelTiempo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido 
            as p where p.estadoDelTiempo = :estadoDelTiempo");
            $consulta->bindValue(':estadoDelTiempo',$estadoDelTiempo,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaFiltrada =  Pedido::CrearLista($data);
        }

        return $listaFiltrada;
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
    public static function ContarPedidosPorIdDeOrdenBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidadTotal = null;

        if(isset($idDeOrden))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS totalPedidos FROM Pedido as p where p.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidadTotal =  $data['totalPedidos'];
        }

        return  $cantidadTotal;
    }
    
   
    public static function ContarPorIdDeEmpeladoBD($idDeEmpelado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidadTotal = null;

        if(isset($idDeEmpelado))
        {
            // var_dump($idDeEmpelado);
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS totalPedidos FROM Pedido as p where p.idDeEmpleado = :idDeEmpleado");
            $consulta->bindValue(':idDeEmpleado',$idDeEmpelado,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidadTotal =  $data['totalPedidos'];
        }

        return  $cantidadTotal;
    }
    public static function FiltrarPorIdDeEmpleadoBD($idDeEmpleado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaFiltrada = null;

        if(isset($idDeEmpleado))
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

    
    public static function ObtenerListaBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePedidos = null;

        if(isset($unObjetoAccesoDato))
        {
            $data = Pedido::ObtenerListaDeArrayBD(PDO::FETCH_ASSOC);
            $listaDePedidos = Pedido::CrearLista($data);
        }

        return  $listaDePedidos;
    }
    public static function ObtenerListaDeArrayBD($mode)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($unObjetoAccesoDato) && isset($mode))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Pedido");
            $consulta->execute();
            $data = $consulta->fetchAll($mode);
        }

        return  $data;
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
        $listaDePedidos = null;
        if(isset($data))
        {
            $listaDePedidos = [];

            foreach($data as $unArray)
            {
                $unPedido = Pedido::CrearUnaPedido($unArray);
                if(isset($unPedido))
                {
                    array_push($listaDePedidos,$unPedido);
                }
            }

           
        }

        return   $listaDePedidos;
    }

    private static function CrearUnaPedido($data)
    {
        $unaPedido = null;

        
        if(isset($data) && $data !== false)
        {
           
            $unaPedido = new Pedido($data['idDeOrden'],$data['idDeProducto']);
            $unaPedido->SetId($data['id']);
            $unaPedido->SetEmpleado($data['idDeEmpleado']);
            $unaPedido->Setcodigo($data['codigo']);

           
            if(isset($data['tiempoEstimado']) )
            {
                $unaPedido->SetTiempoEstimado(DateInterval::createFromDateString($data['tiempoEstimado']));
            }

            if(isset($data['tiempoDeFinalizacion']) )
            {
                $unaPedido->SetTiempoDeFinalizacion(new DateTime($data['tiempoDeFinalizacion']));
            }

            if(isset($data['tiempoDeInicio']))
            {
                $unaPedido->SetTiempoDeInicio(new DateTime($data['tiempoDeInicio']));
            }
            $unaPedido->SetIdSector($data['idDeSector']);
            $unaPedido->SetEstado($data['estado']);
            $unaPedido->SetEstadoDelTiempo($data['estadoDelTiempo']);
            
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
    public static function ContarPedidosPorIdDeEmpleado($listaDePedidos,$idDeEmpleado)
    {
        $cantidad = -1;
        if(isset($listaDePedidos) && isset($idDeEmpleado))
        {
            $cantidad = 0;
            foreach ($listaDePedidos as $unPedidoDeLaLista) {
               
                if($unPedidoDeLaLista->idDeEmpleado === $idDeEmpleado)
                {
                    $cantidad++;
                }                
            }
        }

        return  $cantidad;
    }
    public static function ContarPedidosPorIdDeSector($listaDePedidos,$idDeSector)
    {
        $cantidad = -1;
        if(isset($listaDePedidos) && isset($idDeSector))
        {
            $cantidad = 0;
            foreach ($listaDePedidos as $unPedidoDeLaLista) 
            {
                if($unPedidoDeLaLista->idDeSector === $idDeSector)
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
        // var_dump($tiempoEstimado);
        if(isset($tiempoEstimado))
        {
            $this->tiempoEstimado = $tiempoEstimado;
            $estado = true;
        }

        return  $estado ;
    }
    public function SetHora($horaEstimada)
    {
        $estado = false;
        if(Pedido::ValidadorTiempo($horaEstimada) )
        {
            $this->tiempoEstimado->h = $horaEstimada;
            $estado = true;
        }

        return  $estado ;
    }
    public function SetMinuto($minutosEstimada)
    {
        $estado = false;
        if(Pedido::ValidadorTiempo($minutosEstimada))
        {
            $this->tiempoEstimado->i = $minutosEstimada;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetEstado($estadoDelaPedido)
    {
        $estado = false;
       

        if(Pedido::ValidarEstado($estadoDelaPedido))
        {
            $this->estado = $estadoDelaPedido;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetEstadoDelTiempo($estadoDelTiempo)
    {
        $estado = false;
        $array = array(Pedido::ESTADO_TIEMPO_CUMPLIDO,Pedido::ESTADO_TIEMPO_NOCUMPLIDO,Pedido::ESTADO_TIEMPO_INDETERMINADO);

        if(isset($estadoDelTiempo) && in_array($estadoDelTiempo,$array))
        {
            $this->estadoDelTiempo = $estadoDelTiempo;
            $estado = true;
        }

        return  $estado ;
    }
    private function Setcodigo($codigo)
    {
        $estado = false;
        if(isset($codigo) && $codigo > 0)
        {
            $this->codigo = $codigo;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetIdSector($idDeSector)
    {
        $estado = false;
        if(isset( $idDeSector))
        {
            $this->idDeSector = $idDeSector;
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



    //Getters

    public function GetImporteTotal()
    {
        return  $this->CalcularImporteTotal();
    }
    public function GetId()
    {
        return  $this->id;
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
      
       
        if(isset($this->tiempoEstimado) && 
        ($this->tiempoEstimado->h > 0
        || $this->tiempoEstimado->i > 0))
        {
            $mensaje = $this->tiempoEstimado->h." hours ".$this->tiempoEstimado->i." minutes"; 
           
        }

        return  $mensaje;
    }
    public function GetStrTiempoInicio()
    {
        $mensaje = "No definido";

       
        if(isset($this->tiempoDeInicio))
        {
            $mensaje = $this->GetTiempoDeInicio(); 
           
        }

        return  $mensaje;
    }
    public function GetStrTiempoFinalizacion()
    {
        $mensaje = "No definido";
      
        if(isset($this->tiempoDeFinalizacion))
        {
            $mensaje = $this->GetTiempoDeFinalizacion(); 
        }

        return  $mensaje;
    }
    public function GetTiempoDeInicio()
    {
      
        return  $this->tiempoDeInicio->format("Y-m-d H-i-s");
    }

    public function GetTiempoDeFinalizacion()
    {
        return  $this->tiempoDeFinalizacion->format("Y-m-d H-i-s");
    }
    public function Getcodigo()
    {
        return  $this->codigo;
    }
    public function GetProducto()
    {
        return  Producto::ObtenerUnoPorIdBD($this->unProducto);
    }
    public function GetOrden()
    {
        return  Orden::BuscarOrdenPorIdBD($this->orden);;
    }
    public function GetStrEstadoDelTiempo()
    {
        $mensaje = "";
        if($this->estadoDelTiempo)
        {
            $mensaje = "Se entrego en el tiempo estimado";
        }
        return  $mensaje;
    }
    public function GetStrClienteIngresado()
    {
        $mensaje = "";
        $unaOrden = $this->GetOrden();
        if(isset($unaOrden) && $unaOrden !== false )
        {
            $mensaje = "Cliente que lo pidio: ".$unaOrden->GetNombreDelCliente();
        }
        return  $mensaje;
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
        "Codigo Del Pedido: ".$this->codigo.'<br>'.
        "Tiempo de preparacion estimado: ".$this->GetStrTiempoEstimado().'<br>'.
        "Tiempo de Inicio: ".$this->GetStrTiempoInicio().'<br>'.
        "Tiempo de Finalizacion: ".$this->GetStrTiempoFinalizacion().'<br>'.
        $this->GetStrClienteIngresado().'<br>'.
        "Fecha Del Pedido: ".$this->fechaDelPedido->format('y-m-d H:i:s').'<br>'.
        "Producto Pedido: ".'<br>'.$this->GetProducto()->ToString().'<br>'.
        "importe Total: ".$this->GetImporteTotal().'<br>'
        ."Estado: ".$this->estado.'<br>';
    }

    private function SeEntregoEnElTimpoEstimado()
    {
        $this->estadoDelTiempo = Pedido::ESTADO_TIEMPO_INDETERMINADO;

        $diferencia = $this->tiempoDeInicio->diff($this->tiempoDeFinalizacion);

        if(isset($diferencia))
        {
            $this->estadoDelTiempo  = Pedido::ESTADO_TIEMPO_CUMPLIDO;
            if($diferencia->h > $this->tiempoEstimado->h 
            || ($diferencia->i >  $this->tiempoEstimado->i)  )
            {
                $this->estadoDelTiempo  = Pedido::ESTADO_TIEMPO_NOCUMPLIDO;
            }
        }
        
        return  $this->estadoDelTiempo ;
    }

    private function CalcularImporteTotal()
    {
        $this->importeTotal = 0;

        if(isset($this->unProducto))
        {
            $this->importeTotal =  $this->GetProducto()->GetPrecio();
        }

        return $this->importeTotal;
    }

    private function ObtenerSector()
    {
        $this->idDeSector = null;
        if(isset($this->unProducto))
        {
            $this->idDeSector =  $this->GetProducto()->GetTipo()->GetIdSector();
        }

        return $this->idDeSector ;
    }

    public static function BuscarElPedidoMasVendido($listaDeProductos ,$listaDePedidos)
    { 
        $cantidad = 0;
        $flag = false;
        $mayor =null;

        if(isset($listaDePedidos))
        {
            foreach ($listaDeProductos as $unProducto) 
            {
                $cantidad = Pedido::ContarProductosVendidos($listaDePedidos,$unProducto);

                if($cantidad > $mayor || $flag === false)
                {
                    $mayor = $cantidad;
                    $flag = true;
                }
                
            }
        }

        return $mayor;
    }
    public static function BuscarElPedidoMenosVendido($listaDeProductos,$listaDePedidos)
    {
        $cantidad = 0;
        $flag = false;
        $menor =null;
        
        if(isset($listaDePedidos) && isset($listaDeProductos))
        {
            foreach ($listaDeProductos as $unProducto) 
            {
                $cantidad = Pedido::ContarProductosVendidos($listaDePedidos,$unProducto);

                if($cantidad > 0 && ($cantidad < $menor || $flag === false))
                {
                    $menor = $cantidad;
                    
                    $flag = true;
                }
                
            }
        }

        return $menor;
    }
    public static function ContarProductosVendidos($listaDePedidos,$unProducto)
    {
       $unPedido = null;
       $cantidad = 0;
        
        if(isset($listaDePedidos) && isset($unProducto))
        {
            foreach ($listaDePedidos as $unPedido) 
            {
                
                if($unProducto->Equals($unPedido->GetProducto()))
                {
                    $cantidad++;
                    
                }
            }
        }

        return $cantidad;
    }
    public static function BuscarPorCantidad($listaDePedidos,$cantidad)
    {
       $unPedido = null;
      
        
        if(isset($listaDePedidos) && isset($cantidad))
        {
            foreach ($listaDePedidos as $unPedidoDelaLista) 
            {
                $cantidadVendida = Pedido::ContarProductosVendidos($listaDePedidos,$unPedidoDelaLista->GetProducto());

           
                if($cantidadVendida === $cantidad)
                {
                    $unPedido = $unPedidoDelaLista;
                    break;
                }
            }
        }

        return $unPedido;
    }
    public static function MostrarProductos($listaDePedidos)
    {
       $strLista = null;
      
        if(isset($listaDePedidos))
        {
            $strLista = "";
            foreach ($listaDePedidos as $unPedidoDelaLista) 
            {
                $unProducto = $unPedidoDelaLista->GetProducto();
                if(isset($unProducto))
                {
                    $strLista .= $unProducto->ToString();
                }
            }
        }

        return $strLista;
    }

    public static function ValidadorAlta($data)
    {
        return     Pedido::ValidarProducto($data)
                   && ($unaOrden = Orden::BuscarPorCodigoBD($data['codigoDeOrden'])) !== null 
                   && $unaOrden['estado'] === Orden::ESTADO_ACTIVO;
    }

    private static function ValidarProducto($data)
    {
       
       return   Producto::VerificarPorNombre($data['tipoDeProducto'],$data['nombreDeProducto']);
        
    }
    public static function VerificarCodigo($data)
    {
       return   Pedido::BuscarPorCodigoBD($data['codigo']) !== null;
        
    }

    public static function  ValidadorPreparacion($data)
    {
        return Pedido::ValidadorTiempo($data['hora']) || 
               Pedido::ValidadorTiempo($data['minutos']);
    }
    private static function  ValidadorTiempo($tiempo)
    {
        return  isset($tiempo) && $tiempo > 0 && $tiempo <= 60;
    }

    private static function ValidarEstado($estadoDelaPedido)
    {
        $array = array(Pedido::ESTADO_INICIAL,Pedido::ESTADO_INTERMEDIO,Pedido::ESTADO_FINAL,Pedido::ESTADO_CANCELADO);

       return   isset($estadoDelaPedido) && in_array($estadoDelaPedido,$array);
        
    }
}


?>