

<?php

require_once './db/AccesoDatos.php';

class Sector 
{
    private $id;
    private $descripcion;

    public function __construct($descripcion) 
    {
        $this->descripcion = $descripcion;
    }

    #BaseDeDatos
    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Sector (descripcion) values (:descripcion)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ModificarUnoBD($id,$descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($descripcion) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE `Sector` SET descripcion = :descripcion WHERE id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Sector where id = :id");
            $consulta->bindValue(':id',$idDeSector,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    
    private static function BuscarSectorPorIdBD($idDeSector)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = false;

        if(isset($idDeSector))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM sector as s where s.id = :idDeSector");
            $consulta->bindValue(':idDeSector',$idDeSector,PDO::PARAM_INT);
            $consulta->execute();
            $unSector = $consulta->fetch(PDO::FETCH_ASSOC);
        }

        return  $unSector;
    }
    public static function ObtenerUnoPorIdBD($idDeSector)
    {
        return Sector::CrearUnSector(Sector::BuscarSectorPorIdBD($idDeSector));;
    }
    public function ObtenerListaDePedidos()
    {
        return Pedido::FiltrarPorIdDeSectorBD($this->id);;
    }

    public static function BuscarPorDescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = false;

        if(isset($unObjetoAccesoDato)  && isset($descripcion))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Sector as s where s.descripcion = :descripcion ");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $unSector = Sector::CrearUnSector($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unSector;
    }

    public static function ObternerListaBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeSectores= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Sector");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            
            
            $listaDeSectores = Sector::CrearLista($data);
        }

        return  $listaDeSectores;
    }
  
   

    #end

   
    public function ObtenerListaDeEmpelados()
    {
        return Usuario::ObternerListaDeEmpledosPorSectorBD($this->id);
    }

    private static function CrearUnSector($unArrayAsosiativo)
    {
        $unSector = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unSector = new Sector($unArrayAsosiativo['descripcion']);
            $unSector->SetId($unArrayAsosiativo['id']);
        }
        
        return $unSector ;
    }

    private static function CrearLista($data)
    {
        $listaDeSectores = null;
        if(isset($data))
        {
            $listaDeSectores = [];

            foreach($data as $unArray)
            {
                $unSector = Sector::CrearUnSector($unArray);
                
                
                if(isset($unSector))
                {
                    array_push($listaDeSectores,$unSector);
                }
            }
        }

        return   $listaDeSectores;
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

    protected function SetDescripcion($descripcion)
    {
        $estado = false;
        if(isset($descripcion) )
        {
            $this->descripcion = $descripcion;
            $estado = true;
        }

        return  $estado ;
    }

    #Getters
    public function Getdescripcion()
    {
        return  $this->descripcion;
    }

    public function GetId()
    {
        return  $this->id;
    }

    public function CantidadDeOperaciones()
    {
        $listaDeEmpleados = $this->ObtenerListaDeEmpelados();
        return  Usuario::CantidadDeOperacionesDeUnaLista($listaDeEmpleados);;
    }

    public function GetStrCantidadDeOpereaciones()
    {
        $mensaje = "No se realizaron operaciones";
        $cantidad = $this->CantidadDeOperaciones();

        if($cantidad > 0)
        {
            $mensaje = "Cantidad: ".$cantidad;
        }

        return  $mensaje;
    }

    #Mostrar
     public static function ToStringList($listaDeSectores)
    {
        $strLista = null; 

        if(isset($listaDeSectores) )
        {
            $strLista = "Sectores".'<br>';
            foreach($listaDeSectores as $unSector)
            {
                $strLista .= $unSector->ToString().'<br>';
            }
        }

        return   $strLista;
    }
    public static function MostrarCantidadDeOperaciones($listaDeSectores)
    {
        $strLista = null; 

        if(isset($listaDeSectores) )
        {
            $strLista = "Sectores".'<br>';
            foreach($listaDeSectores as $unSector)
            {
                $strLista .= $unSector->ToString().'<br>'.
                "Cantidad De Operaciones: ".$unSector->GetStrCantidadDeOpereaciones().'<br>'.'<br>';
            }
        }

        return   $strLista;
    }
    public static function MostrarCantidadDeOperacionesPorCadaEmpleado($listaDeSectores)
    {
        $strLista = null; 

        if(isset($listaDeSectores) )
        {
            $strLista = "";
            foreach($listaDeSectores as $unSector)
            {
                $strLista .= $unSector->ToString().'<br>'.
                Usuario::MostarCantidadDeOperaciones($unSector->ObtenerListaDeEmpelados());
            }
        }

        return   $strLista;
    }
 
    public function ToString()
    {
        return "Sector: ".$this->descripcion;
    }

    public static function Validador($data)
    {
        return  Sector::ValidadorDescripcion($data['descripcion']);
    }

    public static function VerificarUno($data)
    {
        return Sector::BuscarSectorPorIdBD($data['id']) !== false;
    }
    private static function ValidadorDescripcion($descripcion)
    {
        return  isset($descripcion) && Util::ValidadorDeNombre($descripcion);
    }

}


?>