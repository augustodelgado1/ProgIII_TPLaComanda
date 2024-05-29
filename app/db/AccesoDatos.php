<?php


class AccesoDatos
{
    private static $conneccionStr;
    private $objetoPdo;
    private static $objetoAccesoDatos;

    private function __construct() 
    {
        try {
            self::$conneccionStr = 'mysql:host=localhost;dbname=tp_comanda';
            $this->objetoPdo = new PDO(self::$conneccionStr,'root','');
            // var_dump($objetoPdo);
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