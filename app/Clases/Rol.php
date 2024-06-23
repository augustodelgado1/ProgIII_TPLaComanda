

<?php

require_once './db/AccesoDatos.php';

require_once 'Sector.php';

class Rol 
{
    private $id;
    private $descripcion;
    public function __construct($descripcion) {
        $this->descripcion = $descripcion;
    }
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        $idDeEncuesta = null;
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Rol (descripcion) values (:descripcion)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $idDeEncuesta =  $objAccesoDatos->ObtenerUltimoID();
        }

        return $idDeEncuesta;
    }

    public static function ModificarUnoBD($id,$descripcion,$idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($id) && isset($descripcion)  && isset($idDeSector))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Rol as r
            SET `descripcion`= :descripcion,
            Where r.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
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
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Rol where id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
   
    public static function BuscarRolPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Rol as c where c.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unRol = Rol::CrearUnRol($consulta->fetch(PDO::FETCH_ASSOC));
            
        }

        return $unRol;
    }

    private static function BuscarRolPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unRol = null;

        if(isset($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM rol as r
             where LOWER(r.descripcion) = LOWER(:descripcion)");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $unRol = $consulta->fetch(PDO::FETCH_ASSOC);
            
        }

        return  $unRol;
    }

   
    private static function CrearUnRol($unArrayAsosiativo)
    {
        $unRol = null;
      
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unRol = new Rol($unArrayAsosiativo['descripcion']);
            $unRol->SetId($unArrayAsosiativo['id']);
            $unRol->SetDescripcion( $unArrayAsosiativo['descripcion']);
        }
        
        return $unRol ;
    }

    private static function CrearLista($data)
    {
        $listaDeRoles = null;
        if(isset($data))
        {
            $listaDeRoles = [];

            foreach($data as $unArray)
            {
                $unRol = Rol::CrearUnRol($unArray);
                
                if(isset($unRol))
                {
                    array_push($listaDeRoles,$unRol);
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
        if(Rol::ValidarDescrpcion($descripcion))
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

      #Validaciones

      public static function ObtenerUnoPorDescripcionBD($descripcion)
      {
          return  Rol::CrearUnRol(Rol::BuscarRolPorDescripcionBD($descripcion));
      }
      public static function VerificarDescripcionBD($descripcion)
      {
          return Rol::BuscarRolPorDescripcionBD($descripcion) !== false;
      }
  
      public static function Validador($data)
      {
          return  Rol::ValidarDescrpcion($data['descripcion']);
      }
  
      public static function ValidarDescrpcion($descripcion)
      {
        return Util::ValidadorDeNombre($descripcion);
      }
      
      #End
   


   
}


?>