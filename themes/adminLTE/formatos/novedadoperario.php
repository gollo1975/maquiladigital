<?php

use inquid\pdf\FPDF;
use app\models\NovedadOperario;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_novedad = $GLOBALS['id_novedad'];
        $novedad = NovedadOperario::findOne($id_novedad);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
//Logo
        $this->SetXY(43, 10);
        $this->Image('dist/images/logos/logomaquila.png', 10, 10, 30, 19);
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
        $this->SetXY(10, 29);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 30);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);

        //Recibo caja
        $this->SetXY(10, 36);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(162, 7, utf8_decode("REPORTE DE NOVEDADES"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($novedad->nro_novedad, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);        
        $this->SetFillColor(200, 200, 200);
      
        $this->SetXY(10, 45); //FILA 1
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("DOCUMENTO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);        
        $this->Cell(80, 6, utf8_decode($novedad->documento), 0, 0, 'L');                
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 6, utf8_decode("OPERARIO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(65, 6, utf8_decode($novedad->operario->nombrecompleto), 0, 'L');
        //fin
        $this->SetXY(10, 49); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 6, utf8_decode("FECHA INICIO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);        
        $this->Cell(80, 6, utf8_decode($novedad->fecha_inicio_permiso), 0, 0, 'L');                
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 6, utf8_decode("FECHA FINAL:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 6, utf8_decode($novedad->fecha_final_permiso), 0, 0, 'L');
        //fin
       $this->SetXY(10, 53); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 6, utf8_decode("HORA INICIO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);        
        $this->Cell(80, 6, utf8_decode($novedad->hora_inicio_permiso), 0, 0, 'L');                
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 6, utf8_decode("HORA FINAL:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 6, utf8_decode($novedad->hora_final_permiso), 0, 0, 'L');
        //fin
        $this->SetXY(10, 57); //FILA 5
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 6, utf8_decode("USUARIO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);        
        $this->Cell(80, 6, utf8_decode($novedad->usuario), 0, 0, 'L');                
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 6, utf8_decode("AUTORIZADO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 6, utf8_decode($novedad->estadoAutorizado), 0, 0, 'L');
        //fin
        $this->SetXY(10, 61); //FILA 6
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 6, utf8_decode("NOVEDAD:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);        
        $this->Cell(80, 6, utf8_decode($novedad->tipoNovedad->novedad), 0, 0, 'L');                
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 6, utf8_decode("CERRADO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 6, utf8_decode($novedad->procesoCerrado), 0, 0, 'L');
        //fin
        
        $this->SetXY(10, 69); //FILA 6
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 6, utf8_decode("OBSERVACION:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);        
        $this->Cell(180, 6, utf8_decode($novedad->observacion), 0, 0, 'L');                
       
    }
    function Body($pdf, $model){
        $pdf->SetXY(10, 94);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 6, 'FIRMA OPERARIO: ___________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 98);
        $pdf->Cell(35, 6, 'C.C.:', 0, 0, 'L',0);
        //firma empresa
        $pdf->SetXY(10, 119);//firma trabajador
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 6, 'FIRMA EMPRESA: ____________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 123);
        $pdf->Cell(35, 6, 'NIT/CC.:', 0, 0, 'L',0);
    }
    function Footer() {

        $this->SetFont('Arial', '', 8);
        //$this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        //$this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $id_novedad;
$id_novedad = $model->id_novedad;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("NovedadOperario$model->nro_novedad.pdf", 'D');

exit;