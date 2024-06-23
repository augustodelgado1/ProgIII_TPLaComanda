

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class Cargo 
{
    private $id;
    private $descripcion;
    private $idDeSector;


    public function __construct($descripcion,$unSector) {
        $this->descripcion = $descripcion;
        $this->idDeSector = $unSector;
    }
    public function AgregarBD()
    {
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        $consulta = $objAccesoDatos->RealizarConsulta("Insert into Cargo (descripcion,idDeSector) 
        values (:descripcion,:idDeSector)");
        $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
        $consulta->bindValue(':idDeSector',$this->idDeSector->GetId(),PDO::PARAM_INT);
        $consulta->execute();
        $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();

        return $idDeEncuesta;
    }

    public static function ModificarUnoBD($id,$descripcion,$idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Cargo as c
            SET `descripcion`= :descripcion,
            `idDeSector`= :idDeSector,
            Where c.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Cargo where id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    private function SetSector($idDeSector)
    {
        $estado  = false;
        if(isset( $idDeSector))
        {
            $this->idDeSector = $idDeSector;
        }

        return $estado;
    }
   
    public static function BuscarCargoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Cargo::CrearUnCargo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return $unRol;
    }
    public static function BuscarCargoPorIdDeSectorBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.idDeSector = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Cargo::CrearUnCargo($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return $unRol;
    }

    private static function BuscarCargoPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;

        if(isset($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where LOWER(c.descripcion) = LOWER(:descripcion)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $estado = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $estado;
    }

    public static function ObtenerUnoPorDescripcionBD($descripcion)
    {
        return  Cargo::CrearUnCargo(Cargo::BuscarCargoPorDescripcionBD($descripcion));
    }
    public static function VerificarUnoPorDescripcionBD($descripcion)
    {
        return Cargo::BuscarCargoPorDescripcionBD($descripcion) !== false;
    }

    public static function FiltrarPorSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Cargo as c where c.idDeSector = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= Cargo::CrearLista($data);
        }

        return $listaDeTipos;
    }

    public function ObtenerListaDeEmpleados()
    {
        return Usuario::FiltrarPorCargoBD($this->id);
    }
    private static function CrearUnCargo($unArrayAsosiativo)
    {
        $unCargo = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unCargo = new Cargo($unArrayAsosiativo['descripcion'],$unArrayAsosiativo['idDeSector']);
            $unCargo->SetId($unArrayAsosiativo['id']);
            $unCargo->SetSector($unArrayAsosiativo['idDeSector']);
            $unCargo->SetDescripcion( $unArrayAsosiativo['descripcion']);
        }
        
        return $unCargo ;
    }

    private static function CrearLista($data)
    {
        $listaDeRoles = null;
        if(isset($data))
        {
            $listaDeRoles = [];

            foreach($data as $unArray)
            {
                $unCargo = Cargo::CrearUnCargo($unArray);
                
                if(isset($unCargo))
                {
                    array_push($listaDeRoles,$unCargo);
                }
            }
        }

        return   $listaDeRoles;
    }
    //Setters
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
    private function SetDescripcion($descripcion)
    {
        $estado = false;
        if(isset($descripcion))
        {
            $this->descripcion = $descripcion;
            $estado = true;
        }

        return  $estado ;
    }

   

    //Getters

    public function GetId()
    {
        return  $this->id;
    }
    public function GetDescripcion()
    {
        return  $this->descripcion;
    }
    public function GetSector()
    {
        return   Sector::BuscarSectorPorIdBD($this->idDeSector);
    }

    
    public function ToString()
    {
        return "Cargo: ".$this->descripcion.'<br>'.
                $this->GetSector()->ToString();
    }

   
}


?>