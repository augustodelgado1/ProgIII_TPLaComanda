

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
    private $numeroDePedido;
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
        $this->numeroDePedido = rand(100,1000);
        $this->orden = $idDeOrden;
        $this->unProducto = $idDeProducto;
        $this->estado = Pedido::ESTADO_INICIAL;
        $this->fechaDelPedido = new DateTime("now");
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
           Insert into Pedido (numeroDePedido,idDeOrden,idDeSector,idDeProducto,importeTotal,fechaDePedido,estado,estadoDelTiempo) 
        values (:numeroDePedido,:idDeOrden,:idDeSector,:idDeProducto,:importeTotal,:fechaDePedido,:estado,:estadoDelTiempo)");
            $consulta->bindValue(':numeroDePedido',$this->numeroDePedido,PDO::PARAM_INT);
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
    public static function ModificarUnoBD($id,$idDeOrden,$idDeProducto,$estado)
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
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
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
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Pedido as p where p.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    // $unPedido->ModificarIdDeEmpleadoBD($unEmpleado->GetId());
    // $unPedido->ModificarEstadoBD(Pedido::ESTADO_INTERMEDIO);
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
    public function ModificarEstadoBD($estado)
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
    public function ModificarTiempoEstimadoBD($tiempoEstimado)
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
    public function ModificarTiempoDeFinalizacionBD($tiempoDeFinalizacion)
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
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
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

        if(isset($unObjetoAccesoDato) && isset($idDeOrden))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS totalPedidos FROM Pedido as p where p.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidadTotal =  $data['totalPedidos'];
        }

        return  $cantidadTotal;
    }
    
    public static function ContarPedidosPorIdDeProductoBD($idDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidadTotal = null;

        if(isset($unObjetoAccesoDato) && isset($idDeProducto))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS totalPedidos FROM Pedido as p where p.idDeProducto = :idDeProducto");
            $consulta->bindValue(':idDeProducto',$idDeProducto,PDO::PARAM_INT);
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

        if(isset($data))
        {
           
            $unaPedido = new Pedido($data['idDeOrden'],$data['idDeProducto']);
            $unaPedido->SetId($data['id']);
            $unaPedido->SetEmpleado($data['idDeEmpleado']);
            $unaPedido->SetNumeroDePedido($data['numeroDePedido']);

            if(isset($data['tiempoEstimado']) 
            && isset($data['tiempoDeFinalizacion']) 
            && isset($data['tiempoDeInicio']))
            {
                $unaPedido->SetTiempoEstimado(DateInterval::createFromDateString($data['tiempoEstimado']));
                $unaPedido->SetTiempoDeFinalizacion(new DateTime($data['tiempoDeFinalizacion']));
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
        if(isset($tiempoEstimado))
        {
            $this->tiempoEstimado = $tiempoEstimado;
            $estado = true;
        }

        return  $estado ;
    }

    private function SetEstado($estadoDelaPedido)
    {
        $estado = false;
        $array = array(Pedido::ESTADO_INICIAL,Pedido::ESTADO_INTERMEDIO,Pedido::ESTADO_FINAL,Pedido::ESTADO_CANCELADO);

        if(isset($estado) && in_array($estadoDelaPedido,$array))
        {
            $this->estado = $estadoDelaPedido;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetEstadoDelTiempo($estadoDelaPedido)
    {
        $estado = false;
        $array = array(Pedido::ESTADO_TIEMPO_CUMPLIDO,Pedido::ESTADO_TIEMPO_NOCUMPLIDO,Pedido::ESTADO_TIEMPO_INDETERMINADO);

        if(isset($estado) && in_array($estadoDelaPedido,$array))
        {
            $this->estado = $estadoDelaPedido;
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
        $estado = false;
        if(isset( $idDeSector))
        {
            $this->idDeSector = $idDeSector;
            $estado = true;
        }

        return  $estado ;
    }
    private function SetIdProducto($idDeProducto)
    {
        $estado = false;
        if(isset( $idDeProducto))
        {
            $this->unProducto = $idDeProducto;
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
        $estado = false;
        if(isset($idDeOrden))
        {
            $this->orden = $idDeOrden;
            $estado = true;
        }

        
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
    public function GetProducto()
    {
        return  Producto::BuscarProductoPorIdBD($this->unProducto);
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
        "Tiempo de preparacion estimado: ".$this->GetStrTiempoEstimado().'<br>'.
        "Cliente que lo pidio: ".$this->GetOrden()->GetNombreDelCliente().'<br>'.
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
            || ($diferencia->h === $this->tiempoEstimado->h && $diferencia->i >  $this->tiempoEstimado->i)  )
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
            $this->idDeSector =  $this->GetProducto()->GetTipo()->GetSector();
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

                if($mayor > $cantidad || $flag === false)
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

                if($menor < $cantidad || $flag === false)
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

        return $unPedido;
    }
    public static function BuscarPorCantidad($listaDePedidos,$cantidad)
    {
       $unPedido = null;
      
        
        if(isset($listaDePedidos) && isset($unProducto))
        {
            foreach ($listaDePedidos as $unPedidoDelaLista) 
            {
                $cantidadVendida = Pedido::ContarProductosVendidos($listaDePedidos,$unPedidoDelaLista->unProducto);

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

    
}


?>