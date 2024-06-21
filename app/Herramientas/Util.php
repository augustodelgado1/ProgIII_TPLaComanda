

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
        $caracteresValidos = range('a','z');
        $len = strlen($string);

        if(isset($string) && $len  > 0)
        {
            $estado = true;
            $strLower = strtolower($string);
           
            for ($i=0; $i < $len; $i++) 
            { 
                if(!in_array($strLower[$i],$caracteresValidos))
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
        $caracteresValidos = range('0','9');
        $len = strlen($string);

        if(isset($string) &&  $len  > 0)
        {
           $estado = true;
           
           for ($i=0; $i < $len; $i++) 
           { 
               if(!in_array($string[$i],$caracteresValidos))
               {
                   $estado = false;
                   break;
               }
           }
        }

        return $estado;
    }
    public static function ValidadorDeNombre($string)
    {
        return    isset($string) 
               && Util::VerificarQueContengaSoloLetras($string);
    }
}

?>