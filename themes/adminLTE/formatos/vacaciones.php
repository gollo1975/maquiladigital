<?php

use inquid\pdf\FPDF;
use app\models\Empleado;
use app\models\Vacaciones;
use app\models\VacacionesAdicion;
use app\models\ConceptoSalarios;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_vacacion = $GLOBALS['id_vacacion'];
        $vacaciones = Vacaciones::findOne($id_vacacion);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        //Logo
       $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(53, 9);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("EMPRESA:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(53, 13);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("NIT:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 17);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("DIRECCION:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 21);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("TELEFONO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 25);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 5, utf8_decode("MUNICIPIO:"), 0, 0, 'l', 1);
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
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(162, 7, utf8_decode("VACACIONES"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($vacaciones->nro_pago, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
       // $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DOCUMENTO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->documento), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("EMPLEADO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->empleado->nombrecorto), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CODIGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, utf8_decode($vacaciones->id_vacacion), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(22, 5, utf8_decode("SALARIO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->salario_contrato, 0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("GRUPO PAGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->grupoPago->grupo_pago), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CARGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->contrato->cargo->cargo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DESDE:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($vacaciones->fecha_desde_disfrute), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("HASTA:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($vacaciones->fecha_hasta_disfrute), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 56);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("INICIO PERIODO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->fecha_inicio_periodo), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("FINAL PERIODO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->fecha_final_periodo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("INI. CONTRATO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($vacaciones->fecha_ingreso), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("PROMEDIO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->salario_promedio,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 60);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DIAS VAC.:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->dias_total_vacacion), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
         $this->Cell(25, 5, utf8_decode("DIAS DISF:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->dias_real_disfrutados), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("VLR PAGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->vlr_vacacion_disfrute,0), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("T. VACACION:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->total_pago_vacacion,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 64);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DIAS EN DINERO.:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->dias_pagados), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("VLR PAGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, '$ '. number_format($vacaciones->vlr_vacacion_dinero,0), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DCTO EPS"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->descuento_eps,0), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("DEDUCCION:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->total_descuentos,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 68);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DCTO PENSION:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, '$ '. number_format($vacaciones->descuento_pension,0), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("GRUPO PAGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->grupoPago->grupo_pago), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("NRO CONT."), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($vacaciones->id_contrato), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("BONIFICACION:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->total_bonificaciones,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 72);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("NRO CTA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($vacaciones->empleado->cuenta_bancaria), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("BANCO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($vacaciones->empleado->bancoEmpleado->banco), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("D. AUSENT."), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($vacaciones->dias_ausentismo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("NETO PAGAR:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($vacaciones->total_pagar,0), 0, 0, 'R', 1);
      //   $this->EncabezadoDetalles();
     
	//Líneas adiciones
        $this->Line(10,87,10,126);// la linea 126 mueve el width
        $this->Line(23,87,23,126);
        $this->Line(176,87,176,126);
        $this->Line(143,87,143,126);
        $this->Line(202,87,202,126);                        
        $this->Line(10,126,202,126);//linea horizontal inferior	la la linea 126 y 126 mueven height de la tabla
    }
    function Body($pdf,$model) {        
        $adiciones = VacacionesAdicion::find()->where(['=','id_vacacion',$model->id_vacacion])->orderBy('tipo_adicion')->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);
        //administradores
       
        $this->SetFont('', 'B', 7);               
      
        $pdf->SetXY(10, 83);
        $pdf->Cell(13, 4, 'ID', 1, 0, 'C',1);
        $pdf->Cell(120, 4, 'CONCEPTO', 1, 0, 'C',1);
        $pdf->Cell(33, 4, 'TIPO ADICION', 1, 0, 'C',1);
        $pdf->Cell(26, 4, 'VALOR', 1, 0, 'C',1);
        $pdf->SetXY(10, 88);
        
        $pdf->SetFont('Arial', '', 7);
        $tipo_adicion = '';
        foreach ($adiciones as $detalle) {                                    
            $pdf->Cell(13, 4, $detalle->id_adicion, 0, 0, 'C');            
            $pdf->Cell(120, 4, $detalle->codigoSalario->nombre_concepto, 0, 0, 'L');            
            if($detalle->tipo_adicion == 1){
                $pdf->Cell(33, 4, 'BONIFICACION', 0, 0, 'L');
            } else {
                $pdf->Cell(33, 4, 'DESCUENTO', 0, 0, 'L');
            }
            $pdf->Cell(26, 4, '$ '.number_format($detalle->valor_adicion, 2), 0, 0, 'R');            
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20); 
            
        }
         $pdf->SetXY(10, 190);//firma trabajador
          $this->SetFont('', 'B', 9);
         $pdf->MultiCell(146, 4, utf8_decode('NOTA: '.$model->observacion),0,'J');
               
		//TOTALES
        $pdf->SetXY(141, 140);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL VACACION:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_pago_vacacion, 2), 1, 0, 'R',0);
        $pdf->SetXY(141, 145);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL DEDUCCIONES:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_descuentos, 2), 1, 0, 'R',0);
        $pdf->SetXY(141, 150);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'BONIFICACIONES:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_bonificaciones, 2), 1, 0, 'R',0);
        $pdf->SetXY(141, 155);
        $this->SetFont('', 'B', 8);
        $pdf->Cell(35, 5, 'TOTAL A PAGAR:', 1, 0, 'R',1);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(26, 5, '$ '.number_format($model->total_pagar, 2), 1, 0, 'R',0);
		//firma trabajador
        $pdf->SetXY(10, 240);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA EMPLEADO: ___________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 245);
        $pdf->Cell(35, 5, 'C.C.:', 0, 0, 'L',0);
        //firma empresa
        $pdf->SetXY(10, 265);//firma trabajador
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA EMPRESA: ____________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 270);
        $pdf->Cell(35, 5, 'NIT/CC.:', 0, 0, 'L',0);
    }

    function Footer() {

        $this->SetFont('Arial', '', 7);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_vacacion;
$id_vacacion = $model->id_vacacion;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Vacaciones$model->id_vacacion.pdf", 'D');

exit;