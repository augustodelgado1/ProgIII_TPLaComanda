<?php
interface IFileManejadorCSV
{
   
    public static function LeerCSV(string $rutaArchivo);
    public static function EscribirCSV(string $rutaArchivo, array $datos);
    public static function EscribirUnoCSV($datos,$unArchivo);

}

?>