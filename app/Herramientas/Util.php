

<?php

class Util 
{
    public static function CrearUnCodigoAlfaNumerico($cantidadDeCaracteres)
    {
        $codigoAlfaNumerico = null;
       
        if($cantidadDeCaracteres > 0)
        {
            $caraceteres =  array_merge(range('a','z'),range(0,9));
            $len = count($caraceteres);

            $codigoAlfaNumerico = "";

            for ($i=0; $i < $cantidadDeCaracteres; $i++) 
            { 
                $codigoAlfaNumerico .= $caraceteres[rand(0,$len-1)];
            }
        }

        return  $codigoAlfaNumerico ;
       
    }

    public static function VerificarQueContengaSoloLetras($string)
    {
        $estado = false;
        $caracteresInvalidos = range('A','Z');

        if(isset($string) && strlen($string) > 0)
        {
            $estado = true;
           foreach($caracteresInvalidos  as $unCaracter)
           {
                if(!str_contains($string,$unCaracter))
                {
                    $estado = false;
                    break;
                }
           }
        }

        return $estado;
    }
    public static function VerificarQueContengaSoloNumeros($string)
    {
        $estado = false;
        $caracteresInvalidos = range('0','9');

        if(isset($string) && strlen($string) > 0)
        {
            $estado = true;
           foreach($caracteresInvalidos  as $unCaracter)
           {
                if(!str_contains($string,$unCaracter))
                {
                    $estado = false;
                    break;
                }
           }
        }

        return $estado;
    }
}

?>