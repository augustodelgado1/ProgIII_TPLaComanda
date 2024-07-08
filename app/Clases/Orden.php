

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
        $this->estadoDelTiempo = Orden::ESTADO_TIEMPO_INDETERMINADO;

        $diferencia = $this->tiempoDeInicio->diff($this->tiempoDeFinalizacion);

        if(isset($diferencia))
        {
            if($diferencia > $this->tiempoTotalEstimado)
            {
                $this->estadoDelTiempo  = Orden::ESTADO_TIEMPO_NOCUMPLIDO;
            }else
            {
                if($diferencia < $this->tiempoTotalEstimado)
                {
                    $this->estadoDelTiempo  = Orden::ESTADO_TIEMPO_CUMPLIDO;
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
            
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden 
            SET tiempoInicio = :tiempoInicio where id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoInicio',$this->tiempoDeInicio->format('Y-m-d-H-i-s'),PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    public function ModificarTiempoDeFinalizacionBD($tiempoDeFinalizacion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;
        if($this->SetTiempoDeFinalizacion($tiempoDeFinalizacion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden as o SET o.tiempoFinal = :tiempoDeFinal where o.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoDeFinal',$this->tiempoDeFinalizacion->format('Y-m-d-H-i-s'),PDO::PARAM_STR);
            $consulta->execute();
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarTiempoTotalEstimadoBD($tiempoTotalEstimado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if($this->SetTiempoEstimado($tiempoTotalEstimado))
        {
            
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden as o SET o.tiempoTotalEstimado = :tiempoTotalEstimado where o.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':tiempoTotalEstimado',$this->tiempoTotalEstimado->format('h-i-s'),PDO::PARAM_STR);
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
               
                if(isset($unPedido) && $unPedido->GetEstado() !== Pedido::ESTADO_INICIAL )
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
    public function ObtenerListaDeProductos()
    {
        return  Producto::FiltrarProductosPorIdDeOrdenBD($this->id);
    }
    public function ObtenerTiempoDeInicio()
    {
        $unPedido = Pedido::BuscarPedidoConMayorTiempoDeInicio($this->ObtenerListaDePedidos());
      
        if(isset($unPedido))
        {
            $this->tiempoDeInicio = $unPedido->GetTiempoDeInicio();
        }
        return  $this->tiempoDeInicio;
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
            $this->ModificarNombreImagen($nombreDeArchivo);
            $this->ModificarRutaDeFoto($rutaASubir);
            $estado = true;
        }

        return $estado;
    }

    #BaseDeDatos

    public function ModificarRutaDeFoto($rutaASubir)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if(isset($rutaASubir))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE orden SET rutaDeLaImagen = :ruta WHERE id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':ruta',$rutaASubir,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public function ModificarNombreImagen($imagen)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = null;

        if(isset($imagen))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `orden` SET nombreDeLaImagen=:imagen WHERE id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':imagen',$imagen,PDO::PARAM_STR);
            $estado = $consulta->execute();
            
        }

        return  $estado;
    }


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

        if(isset($idDeOrden))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.id = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $unaOrden = Orden::CrearUnaOrden($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unaOrden;
    }

    public function EvaluarEstadoDelTiempo()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        $this->ObetenerEstadoDelTiempo();

        if($this->SetEstadoDelTiempo($this->estadoDelTiempo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Orden as o 
            SET o.estadoDelTiempo = :estadoDelTiempo 
            where o.id = :id");
            $consulta->bindValue(':id',$this->id,PDO::PARAM_INT);
            $consulta->bindValue(':estadoDelTiempo',$this->estadoDelTiempo,PDO::PARAM_STR);
            $estado =$consulta->execute();
        }

        return  $estado;
    }
    public static function FiltrarPorIdDeMesaBD($idDeMesa)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($idDeMesa))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Orden as o where o.idDeMesa = :idDeMesa");
            $consulta->bindValue(':idDeMesa',$idDeMesa,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }

    public static function FiltrarPorMesBD($fecha)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($fecha))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("
            SELECT * FROM Orden as o WHERE MONTH(o.fechaDeOrden) = MONTH(:fecha) and YEAR(o.fechaDeOrden) = YEAR(:fecha)");
            $consulta->bindValue(':fecha',$fecha->format("y-m-d"),PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeOrdenes = Orden::CrearLista($data);
        }

        return  $listaDeOrdenes;
    }
    public static function CalcularFacturacionDeUnaMesaEntreDosFechas($idDeLaMesa,$fechaInicio,$fechaFin)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeOrdenes = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("
            SELECT * FROM Orden as o WHERE o.idDeMesa = :idDeLaMesa AND o.fechaDeOrden BETWEEN :fechaInicio AND :fechaFin");
            $consulta->bindValue(':fechaInicio',$fechaInicio,PDO::PARAM_STR);
            $consulta->bindValue(':idDeLaMesa',$idDeLaMesa,PDO::PARAM_INT);
            $consulta->bindValue(':fechaFin',$fechaFin,PDO::PARAM_STR);
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
    public static function BuscarMenorImportePorMesBD($fecha)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $menorImporte = null;

        if(isset($fecha))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT MIN(costoTotal) as costoMenor
            FROM orden o
            WHERE o.estado = :estado 
            and MONTH(o.fechaDeOrden) = MONTH(:fecha) 
            and YEAR(o.fechaDeOrden) = YEAR(:fecha) ");
            $consulta->bindValue(':estado',ORDEN::ESTADO_INACTIVO,PDO::PARAM_STR);
            $consulta->bindValue(':fecha',$fecha->format('Y-m-d'),PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $menorImporte = $data['costoMenor'];
            
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
            $unaOrden->SetEstadoDelTiempo($unArrayAsosiativo['estadoDelTiempo']);

            if(isset($unArrayAsosiativo['tiempoFinal']))
            {
                $unaOrden->SetTiempoDeFinalizacion(new DateTime($unArrayAsosiativo['tiempoFinal']));
            }

            if(isset($unArrayAsosiativo['tiempoInicio']))
            {
                $unaOrden->SetTiempoDeInicio(new DateTime($unArrayAsosiativo['tiempoInicio']));   
            }  
        }
        
        return $unaOrden ;
    }

    private static function CrearLista($data)
    {
        $listaDeOrdenes = null;
        if(isset($data) && $data !== false)
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
    private function SetTiempoEstimado($tiempoEstimado)
    {
        $estado = false;
        if(isset($tiempoEstimado))
        {
            $this->tiempoTotalEstimado = $tiempoEstimado;
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
    public function GetTiempoEstimado()
    {
        return  $this->CalculartiempoTotalEstimado();
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
        return  $this->fechaDeOrden->format('Y-m-d-h-i-s');
    }
    public function GetFecha()
    {
        return  $this->fechaDeOrden;
    }
    public function GetTiempoDeInicio()
    {
        return  $this->ObtenerTiempoDeInicio();
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
        $contador = 1;
        if(isset($listaDeOrdenes) )
        {
            $strLista = "Ordenes".'<br>'.'<br>';
            foreach($listaDeOrdenes as $unaOrden)
            {
                $strLista .= "Orden ".$contador.'<br>'.$unaOrden->ToString().'<br>'.'<br>';
                $contador++;            
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
            $mensaje = Producto::MostrarConCantiadadDeOrden($this->ObtenerListaDeProductos(),$this->id);
            
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
        "Tiempo Total De Inicio: ".$this->GetStrTiempoInicio().'<br>'.
        "Tiempo Total De Espera: ".$this->GetStrTiempoEstimado().'<br>'.
        "Tiempo Total De Finalizacion: ".$this->GetStrTiempoFinalizacion().'<br>'.
        "Mesa: ".'<br>'.$this->GetMesa()->ToString().'<br>'.
        "Facturacion Total: ".$this->GetStrCosto();
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
    public static function FiltrarPorMes($listaDeOrdenes,$fechaDeOrden)
    {
        $listaFiltrada = null;

        if(isset($listaDeOrdenes) && isset($fechaDeOrden) && count($listaDeOrdenes) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDeOrdenes as $unaOrden)
            {
                if($unaOrden->fechaDeOrden->format('m') === $fechaDeOrden)
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
    public static function CalcularFacturacionTotal($listaDeOrdenes,$idDeMesa)
    {
        $acumulador = -1;

        if(isset($listaDeOrdenes))
        {
            $acumulador = 0;
            foreach ($listaDeOrdenes as $unaOrden) 
            {
                if($unaOrden->idDeMesa === $idDeMesa && $unaOrden->costoTotal > 0)
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