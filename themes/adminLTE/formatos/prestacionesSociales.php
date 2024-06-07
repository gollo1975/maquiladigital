<?php

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
        $id_prestacion = $GLOBALS['id_prestacion'];
        $prestaciones = PrestacionesSociales::findOne($id_prestacion);
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
        $this->Cell(162, 7, utf8_decode("PRESTACIONES SOCIALES"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($prestaciones->nro_pago, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
       // $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DOCUMENTO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($prestaciones->empleado->identificacion), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("EMPLEADO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($prestaciones->empleado->nombrecorto), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CODIGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(10, 5, utf8_decode($prestaciones->id_prestacion), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(22, 5, utf8_decode("SALARIO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($prestaciones->salario, 0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("GRUPO PAGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($prestaciones->grupoPago->grupo_pago), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CARGO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($prestaciones->contrato->cargo->cargo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("DESDE:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($prestaciones->fecha_inicio_contrato), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("HASTA:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($prestaciones->fecha_termino_contrato), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 56);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("ULT. PAGO PRIMA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($prestaciones->ultimo_pago_prima), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("ULT. CESANTIA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($prestaciones->ultimo_pago_cesantias), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("ULT. VACACION:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($prestaciones->ultimo_pago_vacaciones), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("DEVENGADO:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($prestaciones->total_devengado,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 60);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("USUARIO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($prestaciones->usuariosistema), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("C. TRABAJO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($prestaciones->contrato->centroTrabajo->centro_trabajo), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("NRO CONTRATO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($prestaciones->id_contrato), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("DEDUCCION:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($prestaciones->total_deduccion,0), 0, 0, 'R', 1);
        //FIN
        $this->SetXY(10, 64);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("T. CUENTA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(20, 5, utf8_decode($prestaciones->empleado->tipo_cuenta), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("BANCO:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($prestaciones->empleado->bancoEmpleado->banco), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("N. CUENTA:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, utf8_decode($prestaciones->empleado->cuenta_bancaria), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(17, 5, utf8_decode("T. PAGAR:"), 0, 0, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(15, 5, '$ '. number_format($prestaciones->total_pagar,0), 0, 0, 'R', 1);
        //FIN
         $this->EncabezadoDetalles();
     
        //Lineas del encabezado
        $this->Line(10,80,10,102);
        $this->Line(63,80,63,102);
        $this->Line(86,80,86,102);
        $this->Line(109,80,109,102);
        $this->Line(121,80,121,102);
        $this->Line(133,80,133,102);
        $this->Line(145,80,145,102);
        $this->Line(164,80,164,102);
        $this->Line(183,80,183,102);
        $this->Line(202,80,202,102);        
        $this->Line(10,102,202,102);//linea horizontal inferior  
        //adiciones
		//Líneas adiciones
        $this->Line(10,113,10,152);
        $this->Line(23,113,23,152);
        $this->Line(176,113,176,152);
        $this->Line(202,113,202,152);                        
        $this->Line(10,152,202,152);//linea horizontal inferior	
        //Líneas creditos
        $this->Line(10,163,10,205);
        $this->Line(23,163,23,205);
        $this->Line(112,163,112,205);
        $this->Line(136,163,136,205);
        $this->Line(160,163,160,205);
        $this->Line(183,163,183,205);
        $this->Line(202,163,202,205);                        
        $this->Line(10,205,202,205);//linea horizontal inferior
        
      
    }
    function EncabezadoDetalles() {
        $this->Ln(12);
        $header = array('CONCEPTO', ('FECHA_INICIO'), ('FECHA_FINAL'), ('N_DIAS'), ('D_AUS'), ('T_DIA'),('S. PROM'),('A_TRANS'), ('TOTAL'));
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);

        //creamos la cabecera de la tabla.
        $w = array(53, 23, 23, 12, 12, 12, 19, 19, 19);
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
        $creditos = PrestacionesSocialesCreditos::find()->where(['=','id_prestacion',$model->id_prestacion])->orderBy('codigo_salario asc')->all();
        $adiciones = PrestacionesSocialesAdicion::find()->where(['=','id_prestacion',$model->id_prestacion])->orderBy('codigo_salario asc')->all();
        $detalle_prestacion = PrestacionesSocialesDetalle::find()->where(['=','id_prestacion', $model->id_prestacion])->all();		
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
		
        foreach ($detalle_prestacion as $detalle) {                                                           
            $pdf->Cell(53, 4, $detalle->codigoSalario->nombre_concepto, 0, 0, 'L');
			$pdf->Cell(23, 4, $detalle->fecha_inicio, 0, 0, 'C');
			$pdf->Cell(23, 4, $detalle->fecha_final, 0, 0, 'C');
			$pdf->Cell(12, 4, $detalle->nro_dias, 0, 0, 'C');
			$pdf->Cell(12, 4, $detalle->dias_ausentes, 0, 0, 'C');
			$pdf->Cell(12, 4, $detalle->total_dias, 0, 0, 'C');	
            $pdf->Cell(19, 4, '$ '.number_format($detalle->salario_promedio_prima, 2), 0, 0, 'R');
            $pdf->Cell(19, 4, '$ '.number_format($detalle->auxilio_transporte, 2), 0, 0, 'R');
            $pdf->Cell(19, 4, '$ '.number_format($detalle->valor_pagar, 2), 0, 0, 'R');            
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                              
        }
		$this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);
        //administradores
        $pdf->SetXY(10, 71);
        $pdf->Cell(192, 5, 'ADMINISTRADORAS', 1, 0, 'C',1);
		//adiciones
		$pdf->SetXY(10, 104);
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);               
        $pdf->Cell(192, 5, 'ADICIONES / DESCUENTOS', 1, 0, 'C',1);
        $pdf->SetXY(10, 109);
        $pdf->Cell(13, 4, 'ID', 1, 0, 'C',1);
        $pdf->Cell(153, 4, 'CONCEPTO', 1, 0, 'C',1);
        $pdf->Cell(26, 4, 'VALOR', 1, 0, 'C',1);
        $pdf->SetXY(10, 114);
        $pdf->SetFont('Arial', '', 7);
        foreach ($adiciones as $detalle) {                                    
            $pdf->Cell(13, 4, $detalle->id_adicion, 0, 0, 'C');            
            $pdf->Cell(153, 4, $detalle->codigoSalario->nombre_concepto, 0, 0, 'L');            
            $pdf->Cell(26, 4, '$ '.number_format($detalle->valor_adicion, 2), 0, 0, 'R');            
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                              
       }
        //creditos
        $pdf->SetXY(10, 154);
        $this->SetFont('', 'B', 7);
        $pdf->Cell(192, 5, utf8_decode('CRÉDITOS'), 1, 0, 'C',1);
        $pdf->SetXY(10, 159);
        $pdf->Cell(13, 4, 'ID', 1, 0, 'C',1);
        $pdf->Cell(89, 4, 'CONCEPTO', 1, 0, 'C',1);
        $pdf->Cell(24, 4, utf8_decode('VALOR CRÉDITO'), 1, 0, 'C',1);
        $pdf->Cell(24, 4, utf8_decode('SALDO CRÉDITO'), 1, 0, 'C',1);
        $pdf->Cell(23, 4, utf8_decode('DEDUCCIÓN'), 1, 0, 'C',1);
        $pdf->Cell(19, 4, 'FECHA INICIO', 1, 0, 'C',1);
        $pdf->SetXY(10, 164);
        $pdf->SetFont('Arial', '', 7);
        foreach ($creditos as $detalle) {                                    
            $pdf->Cell(13, 4, $detalle->id, 0, 0, 'C');            
            $pdf->Cell(89, 4, $detalle->codigoSalario->nombre_concepto, 0, 0, 'L');
            $pdf->Cell(24, 4, '$ '.number_format($detalle->valor_credito, 2), 0, 0, 'R');
            $pdf->Cell(24, 4, '$ '.number_format($detalle->saldo_credito, 2), 0, 0, 'R');
            $pdf->Cell(23, 4, '$ '.number_format($detalle->deduccion, 2), 0, 0, 'R');
            $pdf->Cell(19, 4, $detalle->fecha_inicio, 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);                              
        }
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
global $id_prestacion;
$id_prestacion = $model->id_prestacion;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Prestaciones_sociales$model->id_prestacion.pdf", 'D');

exit;