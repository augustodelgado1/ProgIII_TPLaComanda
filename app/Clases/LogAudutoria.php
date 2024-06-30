


<?php

class LogDeAuditoria
{
    private $id;
    private $idDeUsuario;
    private $accion;
    private $fechaDeEntrada;
   
    public function __construct($idDeUsuario,$accion,$fechaDeEntrada = new DateTime('now')) 
    {
        $this->idDeUsuario = $idDeUsuario;
        $this->accion = $accion;
        $this->fechaDeEntrada = $fechaDeEntrada;
    }

    #BaseDeDatos

    public function AgregarBD()
    {
        $idDeAuditoria = null;
        $objAccesoDatos = AccesoDatos::ObtenerUnObjetoPdo();
        if(isset($objAccesoDatos))
        {
           
            $consulta = $objAccesoDatos->RealizarConsulta("Insert into LogDeAuditoria (idDeUsuario,accion,fechaDeEntrada) values (:idDeUsuario,:accion,:fechaDeEntrada)");
            $consulta->bindValue(':idDeUsuario',$this->idDeUsuario,PDO::PARAM_INT);
            $consulta->bindValue(':accion',$this->accion,PDO::PARAM_STR);
            $consulta->bindValue(':fechaDeEntrada',$this->fechaDeEntrada->format('y-m-d-h-i-s'),PDO::PARAM_STR);
            $consulta->execute();
            $idDeAuditoria = $objAccesoDatos->ObtenerUltimoID();
            $this->SetId($idDeAuditoria);
        }

        return $idDeAuditoria;
    }

    
    public static function BuscarLogDeAuditoriaPorIdBD($id)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $unLogDeAuditoria = null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM LogDeAuditoria as l where l.id = :id");
            $consulta->bindValue(':id',$id,PDO::PARAM_INT);
            $consulta->execute();
            $unLogDeAuditoria = LogDeAuditoria::CrearUnLogDeAuditoria($consulta->fetch(PDO::FETCH_ASSOC));
        }

        return  $unLogDeAuditoria;
    }

    public static function ListarBD()
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeLogs= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT * FROM LogDeAuditoria");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeLogs= LogDeAuditoria::CrearLista($data);
        }

        return  $listaDeLogs;
    }
    public static function FiltrarPorIdDeUsuarioBD($idDeUsuario)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $listaDeLogs= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT l.id, l.accion,l.idDeUsuario,l.fechaDeEntrada,l.fechaDeSalida
            FROM LogDeAuditoria l
            JOIN Usuario u on l.idDeUsuario = e.id
            WHERE u.id = :idDeUsuario");
            $consulta->bindValue(':idDeUsuario',$idDeUsuario,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $listaDeLogs = LogDeAuditoria::CrearLista($data);
        }

        return  $listaDeLogs;
    }
    public static function ObternerCantidadDeAccionesDeUnUsuarioBD($idDeUsuario)
    {
        $unObjetoAccesoDato = AccesoDatos::ObtenerUnObjetoPdo();
        $cantidad= null;

        if(isset($unObjetoAccesoDato))
        {
            $consulta = $unObjetoAccesoDato->RealizarConsulta("SELECT COUNT(l.id) as cantidad
            FROM LogDeAuditoria l
            JOIN Usuario e on l.idDeUsuario = e.id
            WHERE e.id = :idDeEmpelado");
            $consulta->bindValue(':idDeEmpelado',$idDeUsuario,PDO::PARAM_INT);
            $consulta->execute();
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            $cantidad = $data['cantidad'];
        }

        return  $cantidad;
    }

    #end
    private static function CrearUnLogDeAuditoria($unArrayAsosiativo)
    {
        $unLogDeAuditoria = null;
        
        if(isset($unArrayAsosiativo) && $unArrayAsosiativo !== false)
        {
            $unLogDeAuditoria = new LogDeAuditoria($unArrayAsosiativo['idDeUsuario'],$unArrayAsosiativo['accion'],
            new DateTime($unArrayAsosiativo['fechaDeEntrada']));
            $unLogDeAuditoria->SetId($unArrayAsosiativo['id']);
            $unLogDeAuditoria->SetFechaDeEntrada(new DateTime($unArrayAsosiativo['fechaDeEntrada']));
        }
        
        return $unLogDeAuditoria ;
    }

    private static function CrearLista($data)
    {
        $listaDeLogs = null;
        if(isset($data))
        {
            $listaDeLogs = [];

            foreach($data as $unArray)
            {
                $unLogDeAuditoria = LogDeAuditoria::CrearUnLogDeAuditoria($unArray);
                
                if(isset($unLogDeAuditoria))
                {
                    array_push($listaDeLogs,$unLogDeAuditoria);
                }
            }
        }

        return   $listaDeLogs;
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

    public function SetAccion($accion)
    {
        $estado = false;
        if(isset($accion) )
        {
            $this->accion = $accion;
            $estado = true;
        }

        return  $estado ;
    }

    public function SetIdDeUsuario($idDeUsuario)
    {
        $estado = false;
        if(isset($idDeUsuario) )
        {
            $this->idDeUsuario = $idDeUsuario;
            $estado = true;
        }

        return  $estado ;
    }
    public function SetFechaDeEntrada($fechaDeEntrada)
    {
        $estado = false;
        if(isset($fechaDeEntrada) )
        {
            $this->fechaDeEntrada = $fechaDeEntrada;
            $estado = true;
        }

        return  $estado ;
    }

    #Getters
    public function GetIdDeUsuario()
    {
        return  $this->idDeUsuario;
    }
    public function GetUsuario()
    {
        return  Usuario::ObtenerUnoPorIdBD($this->idDeUsuario);
    }

    public function GetId()
    {
        return  $this->id;
    }
   
    public function GetAccion()
    {
        return  $this->accion;
    }
    public function GetFechaDeEntrada()
    {
        return  $this->fechaDeEntrada;
    }

    #Mostrar
     public static function ToStringList($listaDeLogDeAuditoriaes)
    {
        $strLista = null; 

        if(isset($listaDeLogDeAuditoriaes) )
        {
            $strLista = "Logs De Auditorias".'<br>';
            foreach($listaDeLogDeAuditoriaes as $unLogDeAuditoria)
            {
                $strLista .= "Log De Auditoria".'<br>'.$unLogDeAuditoria->ToString().'<br>'.'<br>';
            }
        }

        return   $strLista;
    }

    public function ToString()
    {
        return 
        "Accion: ".$this->accion.'<br>'.
        "fecha De Entrada: ".$this->fechaDeEntrada->format('y-m-d').'<br>'
        ."Usuario: ".$this->GetUsuario()->ToString();
    }



}