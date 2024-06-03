

<?php

require_once './db/AccesoDatos.php';
require_once 'Sector.php';

class Producto 
{
    private $id;
    private $nombre;
    private $tipoDeProducto;
    private $precio;

    public function ToString()
    {
        return 
        "Nombre: ".$this->nombre.'<br>'.
        "Precio: ".$this->precio.'<br>'
        ."TipoDeProducto: ".$this->tipoDeProducto->GetNombre().'<br>';
    }

    
    protected function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();

        if(isset($objAccesoDatos))
        {
         
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Producto (nombre,idDeTipo,precio) values (:nombre,:tipoDeProducto,:precio)");
            $consulta->bindValue(':nombre',$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(':tipoDeProducto',$this->tipoDeProducto->GetId(),PDO::PARAM_INT);
            $consulta->bindValue(':precio',$this->precio);
            $estado = $consulta->execute();
        }

        return $estado;
    }
    public static function FiltrarPorTipoDeProductoBD($tipo)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeProductos = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto as p where p.idDeTipo = :tipo");
            $consulta->bindValue(':tipo',$tipo->GetId());
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeProductos = Producto::CrearLista($data);
        }

        return  $listaDeProductos;
    }
    
    private function SetIdTipoDeProducto($idDeTipoDeProducto)
    {
        $unaTipoDeProducto =  TipoDeProducto::BuscarTipoDeProductoPorIdBD($idDeTipoDeProducto);
        $estado  = Producto::SetTipoDeProducto($unaTipoDeProducto);
        return $estado;
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
     
        if(isset($unArrayAsosiativo))
        {
            $unProducto = new Producto();
            $unProducto->SetId($unArrayAsosiativo['id']);
            $unProducto->SetNombre($unArrayAsosiativo['nombre']);
            $unProducto->SetPrecio($unArrayAsosiativo['precio']);
            $unProducto->SetIdTipoDeProducto($unArrayAsosiativo['idDeTipo']);
        }
        
        return $unProducto ;
    }

    public static function BuscarProductoPorNombreBD($nombre)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unSector = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Producto as p where LOWER(p.nombre) = LOWER(:nombre)");
            $consulta->bindValue(':nombre',$nombre,PDO::PARAM_STR);
            $consulta->execute();
            $unSector = $consulta->fetch(PDO::FETCH_ASSOC);
            $unProducto = Producto::CrearUnProducto($consulta->fetch(PDo::FETCH_ASSOC));
         
        }

        return  $unSector;
    }
  
     public static function ObtenerIndicePorId($listaDeProductos,$id)
    {
        $index = -1;
       
        if(isset($listaDeProductos)  && isset($id))
        {
            $leght = count($listaDeProductos); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeProductos[$i]->id === $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public static function BuscarPorNombre($listaDeProductos,$nombre)
    {
        $unProducto = null;
       
        if(isset($listaDeProductos)  && isset($nombre))
        {
          
            foreach($listaDeProductos as $unProductoDeLaLista)
            {
              
                if( strnatcasecmp($unProductoDeLaLista->nombre,$nombre ) === 0)
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
            $estado =  $unProducto->id === $this->id;
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
       
        if(isset($nombre) && Usuario::VerificarQueContengaSoloLetras($nombre))
        {
            $this->nombre = $nombre;
            $estado = true;
        }

        return  $estado ;
    }

  
    protected function SetPrecio($precio)
    {
        $estado = false;
        if(isset($precio) && $precio > 0)
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
        return  $this->tipoDeProducto;
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

   
    

    //  public static function EscribirJson($listaDeProducto,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeProducto))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeProducto,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Producto::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeProducto = null; 
    //      $unProducto = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeProducto = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unProducto = Producto::DeserializarUnProductoPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unProducto))
    //              {
    //                  array_push($listaDeProducto,$unProducto);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeProducto ;
    //  }

    


    // public function SetCuponDeDescuento($cuponDeDescuento)
    // {
    //     $estado = false;
    //     if(isset($cuponDeDescuento))
    //     {
    //         $this->cuponDeDescuento = $cuponDeDescuento;
    //         $estado = true;
    //     }

    //     return  $estado ;
    // }

    // public function GetCuponDeDescuento()
    // {
    //     return  $this->cuponDeDescuento;
    // }

    
   


   

    // public static function CompararPorclave($unProducto,$otroProducto)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unProducto->clave,$otroProducto->clave);

    //     if( $comparacion  > 0)
    //     {
    //         $retorno = 1;
    //     }else{

    //         if( $comparacion < 0)
    //         {
    //             $retorno = -1;
    //         }
    //     }

    //     return $retorno ;
    // }

    // public static function BuscarProductoPorId($listaDeProducto,$id)
    // {
    //     $unaProductoABuscar = null; 

    //     if(isset($listaDeProducto) )
    //     {
    //         foreach($listaDeProducto as $unaProducto)
    //         {
    //             if($unaProducto->id == $id)
    //             {
    //                 $unaProductoABuscar = $unaProducto; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaProductoABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unProducto,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unProducto = $unProducto;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Producto::ObtenerIdAutoIncremental());
    //     $this->SetImagen($ruta,$claveDeLaImagen);
    // }
    
   

   
    


    // public function CambiarRutaDeLaImagen($nuevaRuta)
    // {
    //     $estado = false;

    //     if(rename($this->rutaDeLaImagen.$this->claveDeLaImagen,$nuevaRuta.$this->claveDeLaImagen))
    //     {
    //         $this->rutaDeLaImagen = $nuevaRuta;
    //         $estado = true;
    //     }

    //     return $estado;
    // }

   

    // public static function BuscarProductoPorId($listaDeProductos,$id)
    // {
    //     $unaProductoABuscar = null; 

    //     if(isset($listaDeProductos)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeProductos as $unaProducto)
    //         {
    //             if($unaProducto->id == $id)
    //             {
    //                 $unaProductoABuscar = $unaProducto; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaProductoABuscar;
    // }
  
    // public static function ToStringList($listaDeProductos)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeProductos) )
    //     {
    //         foreach($listaDeProductos as $unaProducto)
    //         {
    //             $strLista = $unaProducto->ToString().'<br>';
    //         }
    //     }

    //     return   $strLista;
    // }

//Filtrar

    // public static function FiltrarPizzaPorTipo($listaDePizzas,$tipo)
    // {
    //     $listaDeTipoDePizza = null;

    //     if(isset($listaDePizzas) && isset($tipo) && count($listaDePizzas) > 0)
    //     {
    //         $listaDeTipoDePizza =  [];

    //         foreach($listaDePizzas as $unaPizza)
    //         {
    //             if($unaPizza->tipo == $tipo)
    //             {
    //                 array_push($listaDeTipoDePizza,$unaPizza);
    //             }
    //         }
    //     }

    //     return  $listaDeTipoDePizza;
    // }


     //  //Contar
 
    //  public static function ContarPorUnaFecha($listaDeProducto,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeProducto) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeProducto as $unaProducto)
    //          {
    //              if($unaProducto::$fechaDeProducto == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>