

<?php

require_once './fpdf/fpdf.php';

class PDF extends FPDF
{

    public function EscribirLista($lista,$funcEscribirUno)
    {
        $retorno = false;
      
        if(isset($lista) && isset($funcEscribirUno))
        {
            $retorno = true;
            foreach($lista as $unArray)
            {
                
                if(!call_user_func($funcEscribirUno,$unArray,$this))
                {
                    $retorno = false;
                    break;
                }
            }
        }

        return $retorno;
    }
}


?>