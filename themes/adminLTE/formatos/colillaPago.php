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
        $id_programacion = $GLOBALS['id_programacion'];
        $programacionNomina = ProgramacionNomina::findOne($id_programacion);
        $empleado = Empleado::findOne($programacionNomina->id_empleado);
        $banco_empleado = Banco::findOne($empleado->id_banco_empleado);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        $periodo_pago = PeriodoPagoNomina::findOne($programacionNomina->id_periodo_pago_nomina);
        $periodo_pago_nomina = PeriodoPago::findOne($periodo_pago->id_periodo_pago);
        $tipo_pago = app\models\TipoNomina::findOne($periodo_pago->id_tipo_nomina);
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
        $this->Cell(162, 7, utf8_decode("COMPROBANTE DE PAGO"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($programacionNomina->nro_pago, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
       // $this->SetFillColor(300, 300, 300);
        $this->SetXY(10, 44);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DOCUMENTO:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->empleado->identificacion), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("FECHA DESDE:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($programacionNomina->fecha_desde), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(40, 5, utf8_decode("FECHA HASTA:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, utf8_decode($programacionNomina->fecha_hasta), 0, 0, 'R', 1);
      //BLOQUE
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("EMPLEADO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->empleado->nombrecorto), 0, 0, 'L',1);
         $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("TIPO PAGO:"), 0, 0, 'L', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($tipo_pago->tipo_pago), 0, 0, 'L', 1);
         $this->SetFont('Arial', 'B', 7);
         $this->Cell(40, 5, utf8_decode("TIPO CUENTA:"), 0, 0, 'R', 1);
          $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, utf8_decode($empleado->tipoCuenta), 0, 0, 'R', 1);  
        
       //FIN BLOQUE
        //INICIO
         $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("NRO CUENTA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($empleado->cuenta_bancaria), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("BANCO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($empleado->bancoEmpleado->banco), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(40, 5, utf8_decode("SALARIO PROMEDIO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, '$'. number_format($programacionNomina->salario_promedio, 0), 0, 0, 'R', 1);
        //FIN  
        $this->SetFont('Arial', '', 7);
        $this->SetXY(10, 56);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("GRUPO PAGO:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->grupoPago->grupo_pago), 0, 0, 'L', 1);
         $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("CONTRATO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($programacionNomina->id_contrato), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(40, 5, utf8_decode("SALARIO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);        
        $this->Cell(30, 5, '$ '. number_format($programacionNomina->salario_contrato, 0), 0, 0, 'R', 1);        
        
        $this->SetXY(10, 60);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("NRO PERIODO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->id_periodo_pago_nomina), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DÍAS PERIODO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($programacionNomina->dias_pago), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7); 
        $this->Cell(40, 5, utf8_decode("TOTAL DEVENGADO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, '$ '. number_format($programacionNomina->total_devengado, 0), 0, 0, 'R', 1);                        
       
        $this->SetXY(10, 64);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DÍAS REALES:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->dia_real_pagado), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("TIPO PAGO:"), 0, 0, 'L', 1); ///OJOOOOOOOOOOOO
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($periodo_pago->periodoPago->nombre_periodo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(40, 5, utf8_decode("TOTAL DEDUCCIÓN:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, '$ '. number_format($programacionNomina->total_deduccion, 0), 0, 0, 'R', 1);
        
        $this->SetXY(10, 68);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("INICIO CONTRATO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($programacionNomina->fecha_inicio_contrato), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("USUARIO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(23, 5, utf8_decode($programacionNomina->usuariosistema), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(37, 5, utf8_decode("NETO A PAGAR:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(30, 5, '$ '. number_format($programacionNomina->total_pagar, 0), 0, 0, 'R', 1);
        
        //Lineas del encabezado
        $this->Line(10,78,10,140);
        $this->Line(23,78,23,140);
        $this->Line(91,78,91,140);
        $this->Line(100,78,100,140);
        $this->Line(116,78,116,140);
        $this->Line(132,78,132,140);
        $this->Line(145,78,145,140);
        $this->Line(160,78,160,140);
        $this->Line(180,78,180,140);
        $this->Line(200,78,200,140);        
        $this->Line(10,140,200,140);//linea horizontal inferior        
        $this->EncabezadoDetalles();                
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array(utf8_decode('CÓDIGO'), 'CONCEPTO', '%', utf8_decode('N° HORAS'), 'VLR HORA', utf8_decode('N° DIAS'), 'VLR DIA', utf8_decode('DEDUCCIÓN'), 'DEVENGADO');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 68, 9, 16, 16, 13, 15, 20, 20);
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
        $detalles = ProgramacionNominaDetalle::find()->where(['=','id_programacion',$model->id_programacion])->orderBy('vlr_deduccion ASC')->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
        
        foreach ($detalles as $detalle) {
            $codigo_salario = $detalle->codigo_salario;
            $concepto = ConceptoSalarios::find()->where(['=','codigo_salario', $codigo_salario])->one();
            if($concepto->auxilio_transporte == 1){
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(13, 4, $codigo_salario, 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(68, 4, $concepto->nombre_concepto, 0, 0, 'L');
                $pdf->Cell(9, 4, $detalle->porcentaje, 0, 0, 'R');
                $pdf->Cell(16, 4, $detalle->horas_periodo_reales, 0, 0, 'R');
                $pdf->Cell(16, 4, number_format($detalle->vlr_hora, 2), 0, 0, 'R');
                $pdf->Cell(13, 4, $detalle->dias_transporte, 0, 0, 'R');
                $pdf->Cell(15, 4, number_format($detalle->vlr_dia, 2), 0, 0, 'R');
                $pdf->Cell(20, 4, number_format($detalle->vlr_deduccion, 2), 0, 0, 'R');
                $pdf->Cell(20, 4, number_format($detalle->auxilio_transporte, 2), 0, 0, 'R');
                $pdf->Ln();
                $pdf->SetAutoPageBreak(true, 20);                
            }else{
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(13, 4, $codigo_salario, 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(68, 4, $concepto->nombre_concepto, 0, 0, 'L');
                $pdf->Cell(9, 4, $detalle->porcentaje, 0, 0, 'R');
                $pdf->Cell(16, 4, $detalle->horas_periodo_reales, 0, 0, 'R');
                $pdf->Cell(16, 4, number_format($detalle->vlr_hora, 2), 0, 0, 'R');
                $pdf->Cell(13, 4, $detalle->dias_reales, 0, 0, 'R');
                $pdf->Cell(15, 4, number_format($detalle->vlr_dia, 2), 0, 0, 'R');
                $pdf->Cell(20, 4, number_format($detalle->vlr_deduccion, 2), 0, 0, 'R');
                $pdf->Cell(20, 4, number_format($detalle->vlr_devengado, 2), 0, 0, 'R');
                $pdf->Ln();
                $pdf->SetAutoPageBreak(true, 20);                               
            }    
        }        
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_programacion;
$id_programacion = $model->id_programacion;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Colilla$model->id_programacion.pdf", 'D');

exit;