<?php


class AccesoDatos
{
    private static $conneccionStr;
    private $objetoPdo;
    private static $objetoAccesoDatos;

    private function __construct() 
    {
        try {
            self::$conneccionStr = 'mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'];
            $this->objetoPdo = new PDO(self::$conneccionStr,'root','');
           
        } catch (PDOException $th) {
            echo "Error: ".$th->getMessage();
            die();
        }
    }
    public static function ObtenerUnObjetoPdo()
    {
        if(!isset(self::$objetoAccesoDatos))
        {
            self::$objetoAccesoDatos = new AccesoDatos();
        }

        return self::$objetoAccesoDatos;
    }

    public function RealizarConsulta($strConsulta)
    {
        $consulta = null;
        if(isset(self::$objetoAccesoDatos))
        {
            $consulta = self::$objetoAccesoDatos->objetoPdo->prepare($strConsulta);
            // var_dump($consulta);
        }
        
        return $consulta;
    }

    public function ObtenerUltimoID()
    {
        return $this->objetoPdo->lastInsertId(); 
    }

}

?>