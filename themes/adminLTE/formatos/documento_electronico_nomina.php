<?php
ob_start();
include "../vendor/phpqrcode/qrlib.php";
use inquid\pdf\FPDF;
use app\models\Empleado;
use app\models\PrestacionesSociales;
use app\models\PrestacionesSocialesDetalle;
use app\models\PrestacionesSocialesAdicion;
use app\models\PrestacionesSocialesCreditos;
use app\models\ConceptoSalarios;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_nomina_electronica = $GLOBALS['id_nomina_electronica'];
        $nomina = \app\models\NominaElectronica::findOne($id_nomina_electronica);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        //Logo
       $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 30);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(63, 9);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("Empresa:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(63, 13);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("Nit:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(63, 17);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("Direccion:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(63, 21);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("Teléfono:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(63, 25);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("Municipio:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 1);
        $this->SetXY(40, 5);

        //FIN
        $this->SetXY(10, 32);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 32.5);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //Prestaciones sociales
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(10, 39);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(162, 7, utf8_decode("DOCUMENTO SOPORTE DE NOMINA ELECTRONICA"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('No '.$nomina->consecutivo. '-' .$nomina->numero_nomina_electronica), 0, 0, 'l', 0);
       // $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("Documento:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(18, 5, utf8_decode($nomina->documento_empleado), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("Empleado:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(45, 5, utf8_decode($nomina->nombre_completo), 0, 0, 'L', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("Cargo:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(25, 5, utf8_decode(substr($nomina->contrato->cargo->cargo, 0, 29)), 0, 0, 'L', '0');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(28, 5, utf8_decode("Salario:"), 0, 0, 'R', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($nomina->salario_contrato, 0), 0, 0, 'R', 0);
        //FIN
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("No contrato:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(18, 5, utf8_decode($nomina->id_contrato), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("F. envio:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(45, 5, utf8_decode($nomina->fecha_recepcion_dian), 0, 0, 'L', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(20, 5, utf8_decode("Inicio periodo:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(25, 5, utf8_decode($nomina->fecha_inicio_nomina), 0, 0, 'L', 0);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(28, 5, utf8_decode("Final periodo:"), 0, 0, 'R', 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($nomina->fecha_final_nomina), 0, 0, 'R', 0);
        //FIN
                $this->EncabezadoDetalles();
     
        //Lineas del encabezado
        $this->Line(10,63,10,133);
        $this->Line(63,63,63,133);
        $this->Line(103,63,103,133);
        $this->Line(121,63,121,133);
        $this->Line(139,63,139,133);
        $this->Line(157,63,157,133);
        $this->Line(179,63,179,133);
        $this->Line(202,63,202,133);        
        $this->Line(10,133,202,133);//linea horizontal inferior  
        //adiciones
		//Líneas adiciones
        $this->Line(10,135,10,195);
        $this->Line(73,135,73,195);
        $this->Line(122,135,122,195);
        $this->Line(161,135,161,195);     
        $this->Line(202,135,202,195);
        $this->Line(10,195,202,195);//linea horizontal inferior	
       
        
      
    }
    function EncabezadoDetalles() {
        $this->Ln(12);
        $header = array('Concepto', ('Agrupado') ,('Inicio'), ('Final'), ('No dias'), ('Porcentaje'), ('Devengado'));
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);

        //creamos la cabecera de la tabla.
        $w = array(53, 40, 18, 18, 18, 22, 23);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1)
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            else
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);

        //Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(5);
    }        
                      

    function Body($pdf,$model) {    
        $config = Matriculaempresa::findOne(1);
        $nota = \app\models\PeriodoNominaElectronica::findOne($model->id_periodo_electronico);
        $ingresos = \app\models\NominaElectronicaDetalle::find()->where(['=','id_nomina_electronica',$model->id_nomina_electronica])->andWhere(['=','devengado_deduccion', 1])->orderBy('codigo_salario asc')->all();
        $deducciones = \app\models\NominaElectronicaDetalle::find()->where(['=','id_nomina_electronica',$model->id_nomina_electronica])->andWhere(['=','devengado_deduccion', 2])->orderBy('codigo_salario asc')->all();
        
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 6);
		
        foreach ($ingresos as $detalle) {                                                           
            $pdf->Cell(53, 4, $detalle->descripcion, 0, 0, 'L');
            $pdf->Cell(40, 4, $detalle->agrupado->concepto, 0, 0, 'L');
            $pdf->Cell(18, 4, $detalle->inicio_incapacidad, 0, 0, 'C');
            $pdf->Cell(18, 4, $detalle->final_incapacidad, 0, 0, 'C');
            $pdf->Cell(18, 4, $detalle->total_dias, 0, 0, 'R');
            $pdf->Cell(22, 4, ''.number_format($detalle->porcentaje, 2), 0, 0, 'C');
            $pdf->Cell(23, 4, ''.number_format($detalle->devengado,2), 0, 0, 'R');	
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                              
        }
		$this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);
        //administradores
        $pdf->SetXY(10, 59);
        $pdf->Cell(192, 5, 'DEVENGADOS', 1, 0, 'C',1);
		//adiciones
       $pdf->SetXY(10, 135);
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);               
        $pdf->Cell(192, 5, 'DESCUENTOS', 1, 0, 'C',1);
        $pdf->SetXY(10, 140);
        $pdf->Cell(63, 4, 'Concepto', 1, 0, 'C',1);
        $pdf->Cell(49, 4, 'Agrupado', 1, 0, 'C',1);
        $pdf->Cell(39, 4, 'Porcentaje', 1, 0, 'C',1);
        $pdf->Cell(41, 4, 'Deduccion', 1, 0, 'C',1);
        $pdf->SetXY(10, 145);
        $pdf->SetFont('Arial', '', 6);
        foreach ($deducciones as $detalles) {                                    
            $pdf->Cell(63, 4, $detalles->descripcion, 0, 0, 'L');            
            $pdf->Cell(49, 4, $detalles->agrupado->concepto, 0, 0, 'L');            
            $pdf->Cell(39, 4, ' '. number_format($detalles->porcentaje, 2), 0, 0, 'C');   
            $pdf->Cell(41, 4, '$ '.number_format($detalles->deduccion, 2), 0, 0, 'C'); 
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                              
       }
        //creditos
        
		//TOTALES
        $pdf->SetXY(141, 210);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL DEVENGADO:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_devengado, 2), 1, 0, 'R',0);
        $pdf->SetXY(141, 215);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL DEDUCCIONES:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_deduccion, 2), 1, 0, 'R',0);
        $pdf->SetXY(141, 220);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL A PAGAR:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_pagar, 2), 1, 0, 'R',0);
		//firma trabajador
        $pdf->SetXY(10, 240);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(15, 5, 'Cune:',  0, 0, 'L',0);
        $this->SetFont('', '', 8);
        $pdf->SetXY(10, 240);
        $pdf->Cell(169, 5, $model->cune, 0, 0, 'R',0);
        //observaciones
        $pdf->SetXY(10, 244);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(15, 5, 'Observacion:',  0, 0, 'L',0);
        $this->SetFont('', '', 8);
        $pdf->SetXY(10, 244);
        $pdf->Cell(66, 5, $nota->nota, 0, 0, 'R',0);
        //firma empresa de la representacion grafica
        $this->SetFont('Arial', '', 8);
        $qrstr = utf8_decode($model->qrstr);
        $pdf->SetXY(120, 70); // Establece la posición donde aparecerá el QR
        QRcode::png($qrstr,"test.png");
        $pdf->Image("test.png", 80, 198, 38, 35, "png");
        $pdf->SetXY(74, 230);
        $this->SetFont('Arial', 'B', 6);
        $pdf->Cell(64, 8, utf8_decode($config->razonsocialmatricula.'-'.$config->nitmatricula.'-'.$config->dv. ' Software Propio '),0,'J',1);
        // Insertar la imagen base64 directamente en el PDF
        $pdf->SetXY(10, 145); // Establecer la posición
    }

    function Footer() {

        $this->SetFont('Arial', '', 7);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_nomina_electronica;
$id_nomina_electronica = $model->id_nomina_electronica;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("DocumentoSoporteNomina$model->id_nomina_electronica.pdf", 'D');

exit;