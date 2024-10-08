

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
        $len = strlen($string);

        $caracteresValidos = range('a','z');

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
    public static function ValidarDosFechas($data)
    {
        return     Util::ValidarUnaFecha($data['fechaInicial']) && 
                    Util::ValidarUnaFecha($data['fechaFinal']);
    }
    public static function ValidarUnaFecha($fecha)
    {
        return   isset($fecha);
    }
}

?>