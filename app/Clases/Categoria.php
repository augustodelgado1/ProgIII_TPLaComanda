

<?php

require_once './db/AccesoDatos.php';

class Categoria 
{
    private $id;
    private $descripcion;
   
    public function __construct($descripcion) 
    {
        $this->SetDescripcion($descripcion);
    }

    public static function DarDeAltaUnCategoria($descripcion)
    {
        $estado = false;
        $unCategoria = new Categoria($descripcion);
      
        if(empty($unCategoria->descripcion) == false )
        {
            $estado = $unCategoria->AgregarBD();
        }

        return $estado;
    }

    #BaseDeDatos
    private function AgregarBD()
    {
        $estado = false;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into Categoria (descripcion) values (:descripcion)");
            $consulta->bindValue(':descripcion',$this->descripcion,PDO::PARAM_STR);
            $estado = $consulta->execute();
        }

        return $estado;
    }

    
    public static function BuscarCategoriaPorIdBD($idDeCategoria)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCategoria = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Categoria as s where s.id = :idDeCategoria");
            $consulta->bindValue(':idDeCategoria',$idDeCategoria,PDO::PARAM_STR);
            $consulta->execute();
            $unCategoria = Categoria::CrearUnCategoria($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unCategoria;
    }

    public static function BuscarPordescripcionBD($descripcion)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unCategoria = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Categoria as s where s.descripcion = :descripcion");
            $consulta->bindValue(':descripcion',$descripcion,PDO::PARAM_STR);
            $consulta->execute();
            $unCategoria = Categoria::CrearUnCategoria($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unCategoria;
    }

    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeCategoriaes= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM Categoria");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            
            
            $listaDeCategoriaes = Categoria::CrearLista($data);
        }

        return  $listaDeCategoriaes;
    }

    #end


    private static function CrearUnCategoria($unArrayAsosiativo)
    {
        $unCategoria = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
           
            $unCategoria = new Categoria($unArrayAsosiativo['descripcion']);
            $unCategoria->SetId($unArrayAsosiativo['id']);
        }
        
        return $unCategoria ;
    }

    private static function CrearLista($data)
    {
        $listaDeCategoriaes = null;
        if(isset($data))
        {
            $listaDeCategoriaes = [];

            foreach($data as $unArray)
            {
                $unCategoria = Categoria::CrearUnCategoria($unArray);
                
                
                if(isset($unCategoria))
                {
                    array_push($listaDeCategoriaes,$unCategoria);
                }
            }
        }

        return   $listaDeCategoriaes;
    }

    public static function BuscarCategoriaPorId($listaDeCategorias,$id)
    {
        $unaCategoriaABuscar = null; 
        $index = Categoria::ObtenerIndicePorId($listaDeCategorias,$id);
        if($index > 0 )
        {
            $unaCategoriaABuscar = $listaDeCategorias[$index];
        }

        return  $unaCategoriaABuscar;
    }

     public static function ObtenerIndicePorId($listaDeCategorias,$id)
    {
        $index = -1;
       
        if(isset($listaDeCategorias)  && isset($id))
        {
            $leght = count($listaDeCategorias); 
            for ($i=0; $i < $leght; $i++) { 
         
                if($listaDeCategorias[$i]->id == $id)
                {
                    $index = $i;
                    break;
                }
            }
        }

        return $index;
    }

    public function Equals($unCategoria)
    {
        $estado = false;
 
        if(isset($unCategoria))
        {
            $estado =  $unCategoria->id === $this->id;
        }
        return  $estado ;
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

    public function SetDescripcion($descripcion)
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

    #Mostrar
     public static function ToStringList($listaDeCategoriaes)
    {
        $strLista = null; 

        if(isset($listaDeCategoriaes) )
        {
            $strLista = "Categoriaes".'<br>';
            foreach($listaDeCategoriaes as $unCategoria)
            {
                $strLista .= $unCategoria->ToString().'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return "descripcion: ".$this->descripcion.'<br>';
    }

    //  public static function EscribirJson($listaDeCategoria,$claveDeArchivo)
    //  {
    //      $estado = false; 
 
    //      if(isset($listaDeCategoria))
    //      {
    //          $estado =  Json::EscribirEnArrayJson($listaDeCategoria,$claveDeArchivo,JSON_PRETTY_PRINT);
    //      }
    //      return  $estado;
    //  }
 
    //  public static function LeerJson($claveDeArchivo)
    //  {
    //      return Categoria::DeserializarListaJson(Json::LeerListaJson($claveDeArchivo,true));
    //  }
 
    //  private static function DeserializarListaJson($listaDeArrayAsosiativos)
    //  {
    //      $listaDeCategoria = null; 
    //      $unCategoria = null;
    //      if(isset($listaDeArrayAsosiativos))
    //      {
    //          $listaDeCategoria = [];
 
    //          foreach($listaDeArrayAsosiativos as $unArrayAsosiativo)
    //          {
    //              $unCategoria = Categoria::DeserializarUnCategoriaPorArrayAsosiativo($unArrayAsosiativo);
    //              if(isset($unCategoria))
    //              {
    //                  array_push($listaDeCategoria,$unCategoria);
    //              }
                 
    //          }
    //      }
 
    //      return  $listaDeCategoria ;
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

    
   


   

    // public static function CompararPorclave($unCategoria,$otroCategoria)
    // {
    //     $retorno = 0;
    //     $comparacion = strcmp($unCategoria->clave,$otroCategoria->clave);

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

    // public static function BuscarCategoriaPorId($listaDeCategoria,$id)
    // {
    //     $unaCategoriaABuscar = null; 

    //     if(isset($listaDeCategoria) )
    //     {
    //         foreach($listaDeCategoria as $unaCategoria)
    //         {
    //             if($unaCategoria->id == $id)
    //             {
    //                 $unaCategoriaABuscar = $unaCategoria; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCategoriaABuscar;
    // }

    // public function __construct($mail,$unProducto,$clave,$unCategoria,$ruta = null,$claveDeLaImagen = null) {
    //     $this->clave = $clave;
    //     $this->unCategoria = $unCategoria;
    //     $this->mail = $mail;
    //     $this->unProducto = $unProducto;
    //     $this->fechaDeRegistro = date("Y-m-d");
    //     $this->SetId(Categoria::ObtenerIdAutoIncremental());
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

   

    // public static function BuscarCategoriaPorId($listaDeCategorias,$id)
    // {
    //     $unaCategoriaABuscar = null; 

    //     if(isset($listaDeCategorias)  
    //     && isset($id) )
    //     {
    //         foreach($listaDeCategorias as $unaCategoria)
    //         {
    //             if($unaCategoria->id == $id)
    //             {
    //                 $unaCategoriaABuscar = $unaCategoria; 
    //                 break;
    //             }
    //         }
    //     }

    //     return  $unaCategoriaABuscar;
    // }
  
    // public static function ToStringList($listaDeCategorias)
    // {
    //     $strLista = null; 

    //     if(isset($listaDeCategorias) )
    //     {
    //         foreach($listaDeCategorias as $unaCategoria)
    //         {
    //             $strLista = $unaCategoria->ToString().'<br>';
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
 
    //  public static function ContarPorUnaFecha($listaDeCategoria,$fecha)
    //  {
    //      $filtraPorUnaFecha = null;
    //      $cantidad = -1;
 
    //      if(isset($listaDeCategoria) && isset($fecha))
    //      {
    //          $cantidad = 0;
 
    //          foreach($listaDeCategoria as $unaCategoria)
    //          {
    //              if($unaCategoria::$fechaDeCategoria == $fecha)
    //              {
    //                  $cantidad++;
    //              }
    //          }
    //      }
 
    //      return  $filtraPorUnaFecha;
    //  }

   
}


?>