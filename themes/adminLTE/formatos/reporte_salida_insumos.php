<?php

use inquid\pdf\FPDF;
use app\models\SalidaBodega;
use app\models\SalidaBodegaDetalle;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_salida = $GLOBALS['id_salida'];
        $salida = SalidaBodega::findOne($id_salida);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);        
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
            $this->Cell(40, 5, utf8_decode($config->telefonomatricula." - ". $config->celularmatricula), 0, 0, 'L', 1);
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
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(162, 7, utf8_decode("SALIDA DE INSUMOS"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. ' . str_pad($salida->numero_salida, 5, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
           // $this->SetFillColor(200, 200, 200);
        //ORDEN PRODUCCION
         $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CODIGO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(24, 5, utf8_decode($salida->codigo_producto), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("REFERENCIA:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($salida->orden->referencia->referencia), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("AUTORIZADO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($salida->autorizadoSalida), 0, 0, 'L');
        //fin
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("FECHA SALIDA:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(24, 5, utf8_decode($salida->fecha_salida), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("USUARIO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($salida->user_name), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("CERRADO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(40  , 5, utf8_decode($salida->cerradoSalida), 0, 0, 'L');
            //FIN
        $this->SetXY(10, 56);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("EXPORTADO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(24, 5, utf8_decode($salida->insumosExportado), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("RESPONSABLE:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(50, 5, utf8_decode($salida->responsable), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("NOTA"), 0, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(40  , 5, utf8_decode($salida->observacion), 0, 0, 'R');
            //FIN
        //Lineas del encabezado
        $this->Line(10, 66, 10, 200);//x1,y1,x2,y2        
        $this->Line(30, 66, 30, 200);
        $this->Line(110, 66, 110, 200);
        $this->Line(140, 66, 140, 200);
        $this->Line(201, 66, 201, 200);
        $this->Line(201, 66, 201, 200);
       // $this->Line(10, 140, 201, 140); //linea horizontal in ferior x1,y1,x2,y2
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'INSUMO', 'CANTIDAD', 'NOTA');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(20, 80, 30, 61);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1)
                $this->Cell($w[$i], 5, $header[$i], 1, 0, 'C', 1);
            else
                $this->Cell($w[$i], 5, $header[$i], 1, 0, 'C', 1);

        //Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(5);
    }

    function Body($pdf, $model) {
        $insumos = SalidaBodegaDetalle::find()->where(['=', 'id_salida_bodega', $model->id_salida_bodega])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
        $items = count($insumos);
        $total = 0;
        foreach ($insumos as $detalle) {
            $pdf->Cell(20, 5, $detalle->codigo_insumo, 0, 0, 'J');          
            $pdf->Cell(80, 5, $detalle->nombre_insumo, 0, 0, 'J');
            $pdf->Cell(30, 4, number_format($detalle->cantidad_despachar, 0), 0, 0, 'R');
            $pdf->Cell(61, 5, $detalle->nota, 0, 0, 'J');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 200);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(100, 8, 'ITEMS: '.$items, 1, 'J');
        //FIN
        $pdf->SetXY(110, 200);
        $pdf->MultiCell(55, 8, 'TOTAL INSUMOS: ',1, 'R');
        //FIN
        $pdf->SetXY(165, 200);
        $pdf->MultiCell(36, 8, ' '. number_format($model->unidades ),1 , 'R');
        //fin
        
        $pdf->SetXY(10, 255);//firma trabajador
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA AUTORIZADA: ____________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 260);
        $pdf->Cell(35, 5, 'NIT/CC.:', 0, 0, 'L',0);
      
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $id_salida;
$id_salida = $model->id_salida_bodega;
//$pdf = new PDF('L','mm','letter'); para datos horizontal
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Salida_Insumos_$model->numero_salida.pdf", 'D');

exit;


