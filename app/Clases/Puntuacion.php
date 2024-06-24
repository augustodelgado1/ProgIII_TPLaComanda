

<?php

require_once './db/AccesoDatos.php';

class Puntuacion 
{
    public const ESTADO_POSITIVO = "positivo";
    public const ESTADO_NEGATIVO = "negativo";
    private $id;
    private $idDeEncuesta;
    private $descripcion;
    private $puntuacion;
    private $estado;
   

    public function __construct($idDeEncuesta,$descripcion,$puntuacion) 
    {
        $this->SetPuntuacion($puntuacion);
        $this->descripcion = $descripcion;
        $this->idDeEncuesta = $idDeEncuesta;
        $this->ObtenerEstado();
    }
    
    public static function DarDeAltaUnPuntuacion($idDeEncuesta,$descripcion,$puntuacion)
    {
        $unPuntuacion = new Puntuacion($idDeEncuesta,$descripcion,$puntuacion);
        $ultimoID = $unPuntuacion->AgregarBD();

        return $ultimoID;
    }

    #BaseDeDatos
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
     
       
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Puntuacion (descripcion,puntuacion,idDeEncuesta,estado) 
            values (:descripcion,:puntuacion,:idDeEncuesta,:estado)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':puntuacion',$this->puntuacion,PDO::PARAM_INT);
            $consulta->bindValue(':idDeEncuesta',$this->idDeEncuesta,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$this->estado,PDO::PARAM_STR);
            $consulta->execute();
            $this->id = $objAccesoDatos->ObtenerUltimoID();
        }

        return $this->id;
    }

    public static function ModificarUnoBD($id,$descripcion,$puntuacion,$idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($descripcion) && isset($id) && isset($puntuacion) && isset($idDeEncuesta))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Puntuacion 
            SET `descripcion`= :descripcion,
            `puntuacion`= :puntuacion,
            `idDeEncuesta`= :idDeEncuesta,
            Where id=:id");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->bindValue(':puntuacion',$puntuacion,PDO::PARAM_INT);
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDePuntuacion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Puntuacion where id = :id");
            $consulta->bindValue(':id',$idDePuntuacion,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public static function FiltrarPorIdDeEncuestaBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePuntuaciones= null;
        
        if(isset($idDeEncuesta))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Puntuacion 
            as p where p.idDeEncuesta = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePuntuaciones = Puntuacion::CrearLista($data);
           
        }

        return  $listaDePuntuaciones;
    }
    public static function CantidadDePuntuacionesDeUnaEncuestaPorEstadoBD($idDeEncuesta,$estado)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidad= -1;

        if(isset($idDeEncuesta) && Puntuacion::ValidarEstado($estado))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) as total FROM Puntuacion as p
            where p.idDeEncuesta = :idDeEncuesta and p.estado = :estado");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->bindValue(':estado',$estado,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidad= $data['total'];
        }

        return  $cantidad;
    }

    public static function ContarPorIdDeEncuestaBD($idDeEncuesta)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidadTotal = null;

        if(isset($unObjetoAccesoDato) && isset($idDeEncuesta))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(*) AS totalDePuntaciones FROM Puntuacion as p where p.idDeEncuesta = :idDeEncuesta");
            $consulta->bindValue(':idDeEncuesta',$idDeEncuesta,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidadTotal =  $data['totalDePuntaciones'];
        }

        return  $cantidadTotal;
    }

    private static function BuscarUnoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = null;

        if(isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Puntuacion where id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }
    public static function ObtenerUnoPorIdBD($id)
    {
        $data = Puntuacion::BuscarUnoPorIdBD($id);
        return Puntuacion::CrearUnPuntuacion($data);
    }

    // public static function FiltrarPorEstado($listaDePuntuaciones,$estado)
    // {
    //     $listaFiltrada = null;

    //     if(isset($listaDePuntuaciones) && isset($estado) && count($listaDePuntuaciones) > 0)
    //     {
    //         $listaFiltrada =  [];

    //         foreach($listaDePuntuaciones as $unaPuntuacion)
    //         {
    //             if(strcasecmp($unaPuntuacion->estado,$estado) === 0)
    //             {
    //                 array_push($listaFiltrada,$unaPuntuacion);
    //             }
    //         }
    //     }

    //     return  $listaFiltrada;
    // }
    // public static function ContarPorEstado($listaDePuntuaciones,$estado)
    // {
    //     $cantidad = -1;

    //     if(isset($listaDePuntuaciones) && isset($estado))
    //     {
    //         $cantidad = 0;

    //         foreach($listaDePuntuaciones as $unaPuntuacion)
    //         {
    //             if(strcasecmp($unaPuntuacion->estado,$estado) === 0)
    //             {
    //                 $cantidad++;
    //             }
    //         }
    //     }

    //     return  $cantidad;
    // }

    #end


    private static function CrearUnPuntuacion($unArrayAsosiativo)
    {
        $unPuntuacion = null;
      
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unPuntuacion = new Puntuacion($unArrayAsosiativo['idDeEncuesta'],
            $unArrayAsosiativo['descripcion'],
            $unArrayAsosiativo['puntuacion']);
            $unPuntuacion->SetId($unArrayAsosiativo['id']);

          
        }
        
        return $unPuntuacion ;
    }

    private static function CrearLista($data)
    {
        $listaDePuntuaciones = null;
        if(isset($data))
        {
            $listaDePuntuaciones = [];

            foreach($data as $unArray)
            {
                $unPuntuacion = Puntuacion::CrearUnPuntuacion($unArray);
                
                if(isset($unPuntuacion))
                {
                    array_push($listaDePuntuaciones,$unPuntuacion);
                }
            }
        }

        return   $listaDePuntuaciones;
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
    private function SetPuntuacion($puntuacion)
    {
        $estado = false;
        if(isset($puntuacion) 
        && $puntuacion >= 0 
        && $puntuacion <= 10 )
        {
            $this->puntuacion = $puntuacion;
            $estado = true;
        }

        return  $estado ;
    }

    private function ObtenerEstado()
    {
        $this->estado = "negativo";

        if( $this->puntuacion >= 6)
        {
            $this->estado = "positivo";
        }
    }


    #Getters

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
    public static function ToStringList($listaDePuntuaciones)
    {
        $strLista = null; 

        if(isset($listaDePuntuaciones) )
        {
            $strLista = "Puntuaciones".'<br>';
            foreach($listaDePuntuaciones as $unPuntuacion)
            {
                $strLista .= $unPuntuacion->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "Descripcion: ".$this->descripcion.'<br>'. 
               "Puntaje: ".$this->puntuacion.'<br>';
    }

    public static function Validador($data)
    {
        return  isset($data) && Puntuacion::ValidarDescripcion($data['descripcion']) 
                            && Puntuacion::ValidarUnaPuntacion($data['puntuacion']) 
                            && Encuesta::BuscarUnoPorIdBD($data['idDeEncuesta']);;
    }

    public static function VerificarUno($data)
    {
        return Puntuacion::BuscarUnoPorIdBD($data['id']) !== null;
    }
    private static function ValidarDescripcion($descripcion)
    {
        return  isset($descripcion) && Util::ValidadorDeNombre($descripcion);
    }

    public static function ValidarUnaPuntacion($unaPuntuacion)
    {
        return   isset($unaPuntuacion) && $unaPuntuacion > 0 && $unaPuntuacion < 11;
    }

    private static function ValidarEstado($estado)
    {
        return   isset($estado) && in_array($estado,array(Puntuacion::ESTADO_NEGATIVO,Puntuacion::ESTADO_POSITIVO));
    }
   
}


?>