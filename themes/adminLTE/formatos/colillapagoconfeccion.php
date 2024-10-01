<?php

use inquid\pdf\FPDF;
use app\models\Empleado;
use app\models\ProgramacionNomina;
use app\models\ProgramacionNominaDetalle;
use app\models\ConceptoSalarios;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;
use app\models\PeriodoPagoNomina;
use app\models\Banco;
use app\models\PeriodoPago;

class PDF extends FPDF {

    function Header() {
        $id_pago = $GLOBALS['id_pago'];
        $programacionNomina = app\models\PagoNominaServicios::findOne($id_pago);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        //Logo
        $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(70, 9);
        $this->SetFont('Arial', '', 10);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("EMPRESA:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(70, 13);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("NIT:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 17);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DIRECCION:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 21);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("TELEFONO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 25);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("MUNICIPIO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN

        $this->SetXY(10, 29);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 30);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //Programación Nomina
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(10, 36);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(162, 7, utf8_decode("PAGO SERVICIOS (CONFECCION-TERMINACION)"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($programacionNomina->id_pago, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
       // $this->SetFillColor(300, 300, 300);
        $this->SetXY(10, 44);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DOCUMENTO:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(35, 5, utf8_decode($programacionNomina->documento), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("PRESTADOR:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(51, 5, utf8_decode($programacionNomina->operario), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(39, 5, utf8_decode("DEVENGADO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, '$'. number_format($programacionNomina->devengado,0), 0, 0, 'R', 1);
      //BLOQUE
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("FECHA INICIO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(35, 5, utf8_decode($programacionNomina->fecha_inicio), 0, 0, 'L',1);
         $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("FECHA CORTE:"), 0, 0, 'L', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(51, 5, utf8_decode($programacionNomina->fecha_corte), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(39, 5, utf8_decode("DEDUCCION:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, '$'. number_format($programacionNomina->deduccion,0), 0, 0, 'R', 1);
       //FIN BLOQUE
       
        //FIN  
        $this->SetFont('Arial', '', 7);
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("No DIAS:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(35, 5, utf8_decode($programacionNomina->total_dias), 0, 0, 'L', 1);
         $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("OBSERVACION:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(51, 5, utf8_decode($programacionNomina->observacion), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(39, 5, utf8_decode("PAGAR:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);        
        $this->Cell(10, 5, '$ '. number_format($programacionNomina->total_pagar, 0), 0, 0, 'R', 1);        
        
        
        
        //Lineas del encabezado
        $this->Line(10,63,10,130);
        $this->Line(23,63,23,130);
        $this->Line(130,63,130,130);
        $this->Line(165,63,165,130);
        $this->Line(200,63,200,130);
        $this->Line(10,130,200,130);//linea horizontal inferior        
        $this->EncabezadoDetalles();                
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array(utf8_decode('CÓDIGO'), 'CONCEPTO', utf8_decode('DEDUCCIÓN'), 'DEVENGADO');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 107, 35, 35);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1)
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            else
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);

        //Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(4);
    }

    function Body($pdf,$model) {        
        $detalles = app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago',$model->id_pago])->orderBy('deduccion ASC')->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
        
        foreach ($detalles as $detalle) {
            $codigo_salario = $detalle->codigo_salario;
            $concepto = ConceptoSalarios::find()->where(['=','codigo_salario', $codigo_salario])->one();
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(13, 4, $codigo_salario, 0, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(107, 4, $concepto->nombre_concepto, 0, 0, 'L');
            $pdf->Cell(35, 4, number_format($detalle->deduccion, 2), 0, 0, 'R');
            $pdf->Cell(35, 4, number_format($detalle->devengado, 2), 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                
            
        }        
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_pago;
$id_pago = $model->id_pago;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Colilla$model->id_pago.pdf", 'D');

exit;