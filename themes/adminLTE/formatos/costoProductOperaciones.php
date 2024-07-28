<?php

use inquid\pdf\FPDF;
use app\models\SalidaBodega;
use app\models\SalidaBodegaOperaciones;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_salida_bodega = $GLOBALS['id_salida_bodega'];
        $salida = SalidaBodega::findOne($id_salida_bodega);
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
        $this->Cell(162, 7, utf8_decode("LISTADO DE OPERACIONES"), 0, 0, 'l', 0);
        $this->Cell(20, 7, utf8_decode('REF.: ' .$salida->codigo_producto), 0, 0, 'l', 0);
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
        $this->Line(100, 66, 100, 200);
        $this->Line(125, 66, 125, 200);
        $this->Line(161, 66, 161, 200);
        $this->Line(182, 66, 182, 200);
        $this->Line(203, 66, 203, 200);
       // $this->Line(10, 140, 201, 140); //linea horizontal inferior x1,y1,x2,y2
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'OPERACION', 'PROCESO', 'MAQUINA','SEGUNDOS.','MINUTOS');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(20, 70, 25, 36, 21, 21);
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
        $operacion = SalidaBodegaOperaciones::find()->where(['=', 'id_salida_bodega', $model->id_salida_bodega])->all();
        $producto = SalidaBodega::findOne($model->id_salida_bodega);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        $items = count($operacion);
        $total = 0;
        foreach ($operacion as $detalle) {
            $pdf->Cell(20, 5, $detalle->proceso->idproceso, 0, 0, 'J');          
            $pdf->Cell(70, 5, utf8_decode($detalle->proceso->proceso), 0, 0, 'J');
            $pdf->Cell(25, 4, utf8_decode($detalle->tipo->tipo), 0, 0, 'J');
            $pdf->Cell(36, 4, utf8_decode($detalle->tipoMaquinas->descripcion), 0, 0, 'J');
            $pdf->Cell(21, 4, number_format($detalle->segundos, 0), 0, 0, 'R');
            $pdf->Cell(21, 4, number_format($detalle->minutos, 2), 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 200);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(90, 8, 'ITEMS: '.$items, 1, 'J');
        //FIN
        $pdf->SetXY(100, 200);
        $pdf->MultiCell(61, 8, 'MIN. CONF. :'.$producto->tiempo_confeccion, 1, 'R');
        //FIN
        $pdf->SetXY(161, 200);
        $pdf->MultiCell(42, 8, 'MIN. TERM. : '.$producto->tiempo_terminacion,1 , 'R');
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

global $id_salida_bodega;
$id_salida_bodega = $model->id_salida_bodega;
//$pdf = new PDF('L','mm','letter'); para datos horizontal
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Operaciones_Referencia$model->numero_salida.pdf", 'D');

exit;


