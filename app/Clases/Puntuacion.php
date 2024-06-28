

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
   

    public function __construct($idDeEncuesta,$descripcion,$puntuacion) 
    {
        $this->SetPuntuacion($puntuacion);
        $this->descripcion = $descripcion;
        $this->idDeEncuesta = $idDeEncuesta;
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
    public static function FiltrarPorPuntuacionBD($puntuacion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePuntuaciones= null;
        
        if(Puntuacion::ValidarUnaPuntacion($puntuacion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Puntuacion 
            as p where p.puntuacion = :puntuacion");
            $consulta->bindValue(':puntuacion',$puntuacion,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePuntuaciones = Puntuacion::CrearLista($data);
           
        }

        return  $listaDePuntuaciones;
    }
    
    public static function FiltrarPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDePuntuaciones= null;
        
        if(Puntuacion::ValidarDescripcion($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Puntuacion 
            as p where p.descripcion = :descripcion");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDePuntuaciones = Puntuacion::CrearLista($data);
           
        }

        return  $listaDePuntuaciones;
    }

    private static function BuscarUnoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $data = false;

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
        if(Puntuacion::ValidarUnaPuntacion($puntuacion))
        {
            $this->puntuacion = $puntuacion;
            $estado = true;
        }

        return  $estado ;
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
        return Puntuacion::BuscarUnoPorIdBD($data['id']) !== false;
    }
    private static function ValidarDescripcion($descripcion)
    {
        return  isset($descripcion) && Util::ValidadorDeNombre($descripcion);
    }

    public static function ValidarUnaPuntacion($unaPuntuacion)
    {
        return   isset($unaPuntuacion) && $unaPuntuacion >= 0 && $unaPuntuacion < 11;
    }

    private static function ValidarEstado($estado)
    {
        return   isset($estado) && in_array($estado,array(Puntuacion::ESTADO_NEGATIVO,Puntuacion::ESTADO_POSITIVO));
    }
   
}


?>