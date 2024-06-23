

<?php

require_once './db/AccesoDatos.php';
require_once 'TipoDeProducto.php';
require_once './interfaces/IFileManejadorCSV.php';
require_once './Herramientas/Util.php';
class Producto implements IFileManejadorCSV 
{
    private $id;
    private $nombre;
    private $tipoDeProducto;
    private $precio;

    public function __construct($nombre,$tipoDeProducto,$precio) 
    {
        $this->nombre = $nombre;
        $this->tipoDeProducto = $tipoDeProducto;
        $this->precio = $precio;
    }

    public function ToString()
    {
        return 
        "Nombre: ".$this->nombre.'<br>'.
        "Precio: ".$this->precio.'<br>'
        ."TipoDeProducto: ".$this->GetTipo()->GetDescripcion().'<br>';
    }

    public function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
         
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Producto (nombre,idDeTipo,precio) 
            values (:nombre,:tipoDeProducto,:precio)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':tipoDeProducto',$this->tipoDeProducto,PDO::PARAM_INT);
            $consulta->bindValue(':precio',$this->precio);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    public static function ModificarUnoBD($id,$nombre,$tipoDeProducto,$precio)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
       
        if(isset($nombre) && isset($tipoDeProducto) && isset($precio) && isset($id))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("UPDATE Producto as p
            SET `nombre`= :nombre,
            `tipoDeProducto`= :tipoDeProducto,
            `precio`= :precio,
            Where p.id=:id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->bindValue(':tipoDeProducto',$tipoDeProducto,PDO::PARAM_INT);
            $consulta->bindValue(':precio',$precio);
            $estado = $consulta->execute();
        }

        return  $estado;
    }

    public static function BorrarUnoPorIdBD($idDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $estado = false;
        
        if(isset($idDeProducto))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("DELETE FROM Producto where id = :id");
            $consulta->bindValue(':id',$idDeProducto,PDO::PARAM_INT);
            $estado = $consulta->execute();
        }

        return  $estado;
    }
    public static function FiltrarPorTipoDeProductoBD($tipo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeProductos = null;
      
        if(isset($tipo))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto as p where p.idDeTipo = :tipo");
            $consulta->bindValue(':tipo',$tipo);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeProductos = Producto::CrearLista($data);
        }

        return  $listaDeProductos;
    }
   
    public static function BuscarProductoPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unProducto = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto as p where p.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_STR);
            $consulta->execute();
            $unProducto = Producto::CrearUnProducto($consulta->fetch(PDo::FETCH_ASSOC));
        }

        return $unProducto;
    }

    public static function ObtenerListaBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeProductos = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeProductos = Producto::CrearLista($data);
        }

        return  $listaDeProductos;
    }

    protected static function CrearLista($data)
    {
        $listaDeProductos = null;
        if(isset($data))
        {
            $listaDeProductos = [];

            foreach($data as $unArray)
            {
                $unProducto = Producto::CrearUnProducto($unArray);
                if(isset($unProducto))
                {
                    array_push($listaDeProductos,$unProducto);
                }
            }
        }

        return   $listaDeProductos;
    }
    private static function CrearUnProducto($unArrayAsosiativo)
    {
        $unProducto = null;
     
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {

            $unProducto = new Producto($unArrayAsosiativo['nombre'],
            $unArrayAsosiativo['idDeTipo'],$unArrayAsosiativo['precio']);
            $unProducto->SetId($unArrayAsosiativo['id']);
        }
        
        return $unProducto ;
    }

    public static function BuscarPorNombreTipoBD($nombre,$tipoDeProducto)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unProducto = false;

        if(isset($unObjetoAccesoDato))
        {
          
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto 
            as p 
            JOIN TipoDeProducto t ON t.id = p.idDeTipo  
            where LOWER(p.nombre) = LOWER(:nombre) and LOWER(t.nombre) = LOWER(:tipoDeProducto) ");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->bindValue(':tipoDeProducto',$tipoDeProducto,PDO::PARAM_STR);
            $consulta->execute();
            $unProducto = $consulta->fetch(PDO::FETCH_ASSOC);
          
           
        }

        return  $unProducto;
    }

    public static function BuscarPorNombre($listaDeProductos,$nombre)
    {
        $unProducto = null;
       
        if(isset($listaDeProductos)  && isset($nombre))
        {
          
            foreach($listaDeProductos as $unProductoDeLaLista)
            {
            
                if(strnatcasecmp($unProductoDeLaLista->nombre,$nombre ) === 0)
                {
                    $unProducto = $unProductoDeLaLista;
                    break;
                }
            }
        }

        return $unProducto;
    }

    public function Equals($unProducto)
    {
        $estado = false;
 
        if(isset($unProducto))
        {
            $estado =  strcasecmp($unProducto->GetTipo()->GetDescripcion(),$this->GetTipo()->GetDescripcion())  === 0 &&
                       strcasecmp($unProducto->nombre,$this->nombre) === 0;
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

    protected function SetTipoDeProducto($tipoDeProducto)
    {
        $estado = false;
        if(isset($tipoDeProducto))
        {
            $this->tipoDeProducto = $tipoDeProducto;
            $estado = true;
        }

        return  $estado ;
    }

    protected function SetNombre($nombre)
    {
        $estado = false;
       
        if(isset($nombre) && Util::VerificarQueContengaSoloLetras($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }

  
    protected function SetPrecio($precio)
    {
        $estado = false;
        if(Producto::ValidadorPrecio($precio))
        {
            $this->precio = $precio;
            $estado = true;
        }

        return  $estado ;
    }

    //Getters
    public function GetId()
    {
        return  $this->id;
    }
    public function GetNombre()
    {
        return  $this->nombre;
    }

    public function GetPrecio()
    {
        return  $this->precio;
    }

    public function GetTipo()
    {
        return TipoDeProducto::BuscarTipoDeProductoPorIdBD($this->tipoDeProducto);
    }

    public static function ToStringList($listaDeProducto)
    {
        $strLista = null; 

        if(isset($listaDeProducto) )
        {
            $strLista  = "Producto".'<br>';
            foreach($listaDeProducto as $unProducto)
            {
                $strLista .= $unProducto->ToString().'<br>';
            }
        }

        return   $strLista;
    }

   

    public static function EscribirCsv($nombreDeArchivo,$listaDeProductos)
    {
        $estado = false;
        
        if(isset($nombreDeArchivo) && isset($listaDeProductos))
        {
            $estado = File::EscribirGenerico($listaDeProductos,$nombreDeArchivo,array(__CLASS__,'EscribirUnoCsv'));
        }

        return   $estado;
    }
    public static function EscribirUnoCsv($unProducto,$unArchivo)
    {
        $estado = false;
        
        if(isset($unProducto))
        {
            $unArray = array($unProducto->id,$unProducto->nombre,$unProducto->tipoDeProducto,$unProducto->precio);
            $estado = fputcsv($unArchivo,$unArray);
        }

        return   $estado;
    }
   
    public static function LeerCsv($nombreDeArchivo)
    {
        $listaDeProductos = null;
        $data = File::LeerArchivoCsv($nombreDeArchivo);
        
        if(isset($data))
        {
            $listaDeProductos = Producto::DeserializarListaCsv($data);
        }

        return   $listaDeProductos;
    }
    private static function DeserializarListaCsv($listaDeRenglones)
    {
        $listaDeProductos = null;
        if(isset($listaDeRenglones))
        {
            $listaDeProductos = [];

            foreach($listaDeRenglones as $unRenglon)
            {
                
                $unProducto = Producto::CrearUnProducto(array('id' => $unRenglon[0],'nombre' => $unRenglon[1]
                                                        ,'idDeTipo' => $unRenglon[2],'precio' => $unRenglon[3]));
                if(isset($unProducto))
                {
                    array_push($listaDeProductos,$unProducto);
                }
            }
        }

        return   $listaDeProductos;
    }

    public static function Validador($data)
    {
        return     Producto::ValidadorPrecio($data['precio']) 
                && Producto::ValidarTipo($data['tipo'])
                && Util::ValidadorDeNombre($data['nombre']);
    }

    public static function VerificarUno($data)
    {
        return Producto::BuscarProductoPorIdBD($data['id']) !== false;
    }
    private static function ValidadorPrecio($precio)
    {
        return  isset($precio) && $precio > 0;
    }

    public static function ValidarTipo($descripcion)
    {
        return  isset($descripcion) 
        && TipoDeProducto::BuscarPorNombreBD($descripcion) !== false;
    }
    public static function VerificarPorNombre($tipoDeProducto,$descripcion)
    {
        return Producto::BuscarPorNombreTipoBD($descripcion,$tipoDeProducto) !== false;
    }
   
}


?>