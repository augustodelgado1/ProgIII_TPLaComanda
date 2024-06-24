

<?php

require_once './db/AccesoDatos.php';
require_once './Clases/Puntuacion.php';
require_once './Clases/Orden.php';

class Encuesta 
{
    public const ESTADO_POSITIVO = "positivo";
    public const ESTADO_NEGATIVA = "negativo";
    public const ESTADO_INTERMEDIO = "indefinido";
    private $id;
    private $nombreDelCliente;
    private $idDeOrden;
    private $mensaje;
    private $estado;
   
    public function __construct($idDeOrden,$nombreDelCliente,$mensaje) 
    {
        $this->SetMensaje($mensaje);
        $this->idDeOrden = $idDeOrden;
        $this->nombreDelCliente = $nombreDelCliente;
        $this->EvaluarEstado();
    }

    

    private  function EvaluarEstado()
    {
        $this->estado = Encuesta::ESTADO_INTERMEDIO;
        $cantidadDePuntuaciones = Puntuacion::ContarPorIdDeEncuestaBD($this->id);
       
        if( $cantidadDePuntuaciones > 0 )
        {
            // $listaDePuntuaciones = $this->ObtenerListaDePuntuaciones();
            $cantidadDeNegativas = Puntuacion::CantidadDePuntuacionesDeUnaEncuestaPorEstadoBD($this->id,Puntuacion::ESTADO_NEGATIVO);
            $cantidadDePositivas = Puntuacion::CantidadDePuntuacionesDeUnaEncuestaPorEstadoBD($this->id,Puntuacion::ESTADO_POSITIVO);

            if($cantidadDePositivas  >  $cantidadDeNegativas)
            {
                $this->estado = Encuesta::ESTADO_POSITIVO ;
               
            }else{
    
                if($cantidadDePositivas  <  $cantidadDeNegativas)
                {
                    $this->estado = Encuesta::ESTADO_NEGATIVA;
                }
            }
        }
      

        return  $this->estado;
    }


    public function ObtenerListaDePuntuaciones()
    {
       return Puntuacion::FiltrarPorIdDeEncuestaBD($this->id);
    }
    public function CantidadDePuntuaciones()
    {
       return Puntuacion::ContarPorIdDeEncuestaBD($this->id);
    }
    

    #BaseDeDatos
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
       
        $consulta = $objAccesoDatos->RealizarConsulta("Insert into Encuesta (nombreDelCliente,mensaje,idDeOrden,estado) 
        values (:nombreDelCliente,:mensaje,:idDeOrden,:estado)");
        $consulta->bindValue(':mensaje',$this->mensaje,PDO::PARAM_STR);
        $consulta->bindValue(':nombreDelCliente',$this->nombreDelCliente,PDO::PARAM_STR);
        $consulta->bindValue(':idDeOrden',$this->idDeOrden,PDO::PARAM_INT);
        $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
        $consulta->execute();
        $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();
        $this->id =  $idDeEncuesta;
      
        return $idDeEncuesta;
    }

    public static function ModificarUnoBD($id,$nombreDelCliente,$mensaje,$idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($nombreDelCliente) && isset($mensaje) && isset($idDeOrden) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Encuesta 
            SET `nombreDelCliente`= :nombreDelCliente,
            `mensaje`= :mensaje,
            `idDeOrden`= :idDeOrden,
            Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':nombreDelCliente',$nombreDelCliente,PDO::PARAM_STR);
            $consulta->bindValue(':mensaje',$mensaje,PDO::PARAM_STR);
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }
        

        return  $estado;
    }
    private static function ModificarEstadoBD($id,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($estado) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Encuesta 
            SET `estado`= :estado Where id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }
        
        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Encuesta as e where e.id = :id");
        $consulta->bindValue(':id',$idDeEncuesta,PDO::PARAM_INT);
        $estado = $consulta->execute();
        

        return  $estado;
    }
    private static function BuscarUnoPorIdBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unEncuesta = null;

        if(isset($idDeEncuesta))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.id = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $unEncuesta = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $unEncuesta;
    }
    public static function ObtenerUnoPorIdBD($idDeEncuesta)
    {
        return  Encuesta::CrearUnEncuesta(Encuesta::BuscarUnoPorIdBD($idDeEncuesta));
    }
     
    public static function FiltrarPorIdDeOrdenBD($idDeOrden)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestas = null;

        if(isset($idDeOrden))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.idDeOrden = :idDeOrden");
            $consulta->bindValue(':idDeOrden',$idDeOrden,PDO::PARAM_INT);
            $consulta->execute();
            $listaDeEncuestas = Encuesta::CrearLista($consulta->fetchAll(PDO::FETCH_ASSOC));
        }

        return  $listaDeEncuestas;
    }
    public static function FiltrarPorEstadoBD($estadoDeLaEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestas = null;

        if(Encuesta::ValidarEstado($estadoDeLaEncuesta))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta as e where e.estado = :estado");
            $consulta->bindValue(':estado',$estadoDeLaEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $listaDeEncuestas = Encuesta::CrearLista($consulta->fetchAll(PDO::FETCH_ASSOC));
        }

        return  $listaDeEncuestas;
    }
    public static function FiltrarPorPuntucionBD($descripcion,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestas = null;

        if(isset($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT e.id,e.nombreDelCliente,e.mensaje,e.idDeOrden,e.estado FROM Encuesta e
            JOIN Puntuacion p ON p.idDeEncuesta = e.id WHERE LOWER(p.descripcion) = LOWER(:descripcion) AND LOWER(p.estado) = LOWER(:estado)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->execute();
            $listaDeEncuestas = Encuesta::CrearLista($consulta->fetchAll(PDO::FETCH_ASSOC));
          
        }
       

        return  $listaDeEncuestas;
    }

    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeEncuestaes = null;
        $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Encuesta");
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $listaDeEncuestaes = Encuesta::CrearLista($data);
        

        return  $listaDeEncuestaes;
    }


    #end

    public static function FiltrarPorEstado($listaDeEncuestas,$estado)
    {
        $listaFiltrada = null;

        if(isset($listaDeEncuestas) && isset($estado) && count($listaDeEncuestas) > 0)
        {
            $listaFiltrada =  [];

            foreach($listaDeEncuestas as $unaEncuesta)
            {
                if(strcasecmp($unaEncuesta->estado,$estado) === 0)
                {
                    array_push($listaFiltrada,$unaEncuesta);
                }
            }
        }

        return  $listaFiltrada;
    }

    private static function CrearUnEncuesta($unArrayAsosiativo)
    {
        $unEncuesta = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unEncuesta = new Encuesta($unArrayAsosiativo['idDeOrden'],$unArrayAsosiativo['nombreDelCliente'],
            $unArrayAsosiativo['mensaje']);
            $unEncuesta->SetId($unArrayAsosiativo['id']);
            $unEncuesta->EvaluarEstado();

           
        }
        
        return $unEncuesta ;
    }

    private static function CrearLista($data)
    {
        $listaDeEncuestaes = null;
        if(isset($data))
        {
            $listaDeEncuestaes = [];

            foreach($data as $unArray)
            {
                $unEncuesta = Encuesta::CrearUnEncuesta($unArray);
                
                if(isset($unEncuesta))
                {
                    array_push($listaDeEncuestaes,$unEncuesta);
                }
            }
        }

        return   $listaDeEncuestaes;
    }

     public static function ObtenerIndicePorId($listaDeEncuestas,$id)
    {
        $index = -1;
      
        if(isset($listaDeEncuestas)  && isset($id))
        {
            $leght = count($listaDeEncuestas); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeEncuestas[$i]->id === $id)
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

    private function SetEstado($estadoDelaEncuesta)
    {
        $estado = false;

        if(isset($estado) && Encuesta::ValidarEstado($estadoDelaEncuesta))
        {
            $this->estado = $estadoDelaEncuesta;
            $estado = true;
        }

        return  $estado ;
    }

    private static function ValidarEstado($estado)
    {
        $array = array(Encuesta::ESTADO_INTERMEDIO,Encuesta::ESTADO_NEGATIVA,Encuesta::ESTADO_POSITIVO);

        return isset($estado) && in_array($estado,$array);
    }

    public function SetMensaje($mensaje)
    {
        $estado = false;
        if(Encuesta::ValidadorDeMensaje($mensaje))
        {
            $this->mensaje = $mensaje;
            $estado = true;
        }

        return  $estado ;
    }

    public static function ValidadorAlta($data)
    {
        return Encuesta::ValidadorModificacion($data)
        && Puntuacion::ValidarUnaPuntacion($data['puntuacionDeLaMesa'])
        && Puntuacion::ValidarUnaPuntacion($data['puntuacionDelRestaurante'])
        && Puntuacion::ValidarUnaPuntacion($data['puntuacionDelCocinero'])
        && Puntuacion::ValidarUnaPuntacion($data['puntuacionDelMozo'])
        && Mesa::VerificarUnoPorCodigo($data['codigoDeMesa']);
    }
    public static function ValidadorModificacion($data)
    {
        return  Encuesta::ValidadorDeMensaje($data['mensaje'])
        && Orden::VerificarUnoPorCodigo($data['codigoDeOrden'])
        && Encuesta::ValidadorDeCliente($data['nombreDelCliente']);
    }
    private static function ValidadorDeCliente($nombre)
    {
        return Util::ValidadorDeNombre($nombre);
    }


    private static function ValidadorDeMensaje($mensaje)
    {
        return isset($mensaje) && strlen($mensaje) <= 66;
    }
    public static function ValidadorId($data)
    {
        return Encuesta::BuscarUnoPorIdBD($data['id']) !== null;
    }

    #Getters
    public function GetMensaje()
    {
        return  $this->mensaje;
    }
    public function GetNombreDelCliente()
    {
        return  $this->nombreDelCliente;
    }
    public function GetStrPuntuacion()
    {
        $listaDePuntuaciones = $this->ObtenerListaDePuntuaciones();

        $mensaje = "no se encontraron puntuaciones";
        if(isset($listaDePuntuaciones) && count($listaDePuntuaciones) > 0)
        {
            $mensaje = Puntuacion::ToStringList($listaDePuntuaciones);
        }

        return  $mensaje ;
    }

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
     public static function ToStringList($listaDeEncuestaes)
    {
        $strLista = null; 

        if(isset($listaDeEncuestaes) )
        {
            $strLista = "Encuestas".'<br>';
            foreach($listaDeEncuestaes as $unEncuesta)
            {
                $strLista .= $unEncuesta->ToString().'<br>';
            }
        }

        return   $strLista;
    }
    
    public static function FiltrarPorIdDeOrdenes($listaDeEncuesta,$idDeOrden)
    {
       
        $listaDefiltrada = null;

        if(isset($listaDeEncuesta) && isset($idDeOrden))
        {
            $listaDefiltrada = [];

            foreach ($listaDeEncuesta as $unaEncuesta) 
            {
                if($unaEncuesta->idDeOrden === $idDeOrden)
                {
                    array_push($listaDefiltrada,$unaEncuesta);
                }
            }
        }

        return  $listaDefiltrada;
    }

    public function ToString()
    {
        $this->EvaluarEstado();

        return "Nombre del Cliente: ".$this->nombreDelCliente.'<br>'.
        $this->GetStrPuntuacion().'<br>'.
        "Comentario: ".$this->mensaje.'<br>';
    }
   
}


?>