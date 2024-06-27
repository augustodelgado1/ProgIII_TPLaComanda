

<?php

require_once './fpdf/fpdf.php';
class EmpresaController 
{
  
    public static function DescargarLogoPorPDF($request, $response, array $args)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetTitle('Logo',true);
        $imageWidth = 200;  
        $imageHeight = 200;
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $imageX = ($pageWidth - $imageWidth) / 2;
        $imageY = ($pageHeight - $imageHeight) / 2;
        $pdf->Image('../logo/restaurant_logo.jpg', $imageX, $imageY, $imageWidth, $imageHeight);
        $pdfContent = $pdf->Output('S','logo.pdf',true);
        $response = $response->withHeader('Content-Type', 'application/pdf')
                         ->withHeader('Content-Disposition', 'attachment; filename="logo_empresa.pdf"');

        $response->getBody()->write($pdfContent);

        return $response;
    }

 
    
}

?>
