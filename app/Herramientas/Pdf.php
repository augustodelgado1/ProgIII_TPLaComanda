

<?php

class PDF 
{
    public static function EscribirLista($lista,$funcEscribirUno)
    {
        $retorno = false;
      
        if(isset($lista) && isset($funcEscribirUno))
        {
            $pdf = new FPDF();
            $pdf->SetFont('Times','B',20);
            $pdf->Image('logo/restaurant_logo.png',150,10,35);
            $pdf->Ln(40);
            $pdf->SetAutoPageBreak(true,20);

            foreach($lista as $unArray)
            {
                
                
                if(!call_user_func($funcEscribirUno,$unArray,$pdf))
                {
                    
                    break;
                }
            }

           
            $retorno = $pdf->Output();
        
        }

        return $retorno;
    }
}


?>