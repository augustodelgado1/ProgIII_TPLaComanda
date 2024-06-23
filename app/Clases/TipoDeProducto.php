

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';
class TipoDeProducto 
{
    private $id;
    private $nombre;
    private $idDeSector;
   
    public function __construct($nombre,$sector) {
        $this->SetNombre($nombre);
        $this->idDeSector = $sector;
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
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into TipoDeProducto (nombre,idDeSector) values (:nombre,:idDeSector)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$this->idDeSector,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ModificarUnoBD($id,$descripcion,$idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE TipoDeProducto as t
            SET `descripcion`= :descripcion,
            `idDeSector`= :idDeSector,
            Where t.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeTipoDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM TipoDeProducto where id = :id");
            $consulta->bindValue(':id',$idDeTipoDeProducto,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BuscarTipoDeProductoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unTipoDeProducto = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where t.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $unTipoDeProducto = TipoDeProducto::CrearUnTipoDeProducto($data);
        }

        return $unTipoDeProducto;
    }

    public static function FiltrarTipoDeProductoPorSectorBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeTipos= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where t.idDeSector = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $listaDeTipos= TipoDeProducto::CrearLista($data);
        }

        return $listaDeTipos;
    }

    private static function CrearUnTipoDeProducto($unArrayAsosiativo)
    {
        $unTipoDeProducto = null;
   
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unTipoDeProducto = new TipoDeProducto($unArrayAsosiativo['nombre'],$unArrayAsosiativo['idDeSector']);
            $unTipoDeProducto->SetId($unArrayAsosiativo['id']);
            $unTipoDeProducto->SetSector($unArrayAsosiativo['idDeSector']);
        }
        
        return $unTipoDeProducto ;
    }

    private static function CrearLista($data)
    {
        $listaDeTipoDeProducto = null;
        if(isset($data))
        {
            $listaDeTipoDeProducto = [];

            foreach($data as $unArray)
            {
                $unTipoDeProducto = TipoDeProducto::CrearUnTipoDeProducto($unArray);
                
                
                if(isset($unTipoDeProducto))
                {
                    array_push($listaDeTipoDeProducto,$unTipoDeProducto);
                }
            }
        }

        return   $listaDeTipoDeProducto;
    }
    public static function BuscarPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unTipoDeProducto = null;

        if(isset($unObjetoAccesoDato) && isset($nombre))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM TipoDeProducto as t where LOWER(t.nombre) = LOWER(:nombre)");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unTipoDeProducto = $consulta->fetch(PDO::FETCH_ASSOC);
       
        }

        return  $unTipoDeProducto;
    }
    public static function ObtenerUnoPorNombreBD($nombre)
    {
        return  TipoDeProducto::CrearUnTipoDeProducto(TipoDeProducto::BuscarPorNombreBD($nombre));
    }

    

     public static function ObtenerIndicePorId($listaDeTipoDeProductos,$id)
    {
        $index = -1;
       
        if(isset($listaDeTipoDeProductos)  && isset($id))
        {
            $leght = count($listaDeTipoDeProductos); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeTipoDeProductos[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

   

    public function Equals($unTipoDeProducto)
    {
        $estado = false;
 
        if(isset($unTipoDeProducto))
        {
            $estado =  $unTipoDeProducto->id === $this->id;
        }
        return  $estado ;
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

    public function SetNombre($nombre)
    {
        $estado = false;
        if(isset($nombre) )
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }
  
    //Getters
    public function GetDescripcion()
    {
        return  $this->nombre;
    }
    public function GetIdSector()
    {
        return  $this->idDeSector;
    }

    public function GetId()
    {
        return  $this->id;
    }

    #Mostrar
    public static function ToStringList($listaDeTipoDeProductos)
    {
        $strLista = null; 

        if(isset($listaDeTipoDeProductos) )
        {
            $strLista = "TipoDeProductos".'<br>';
            foreach($listaDeTipoDeProductos as $unSector)
            {
                $strLista .= $unSector->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "tipo: ".$this->nombre.'<br>';
    }
        

   
}


?>