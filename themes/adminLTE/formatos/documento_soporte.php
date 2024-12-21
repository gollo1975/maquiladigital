<?php
ob_start();
include "../vendor/phpqrcode/qrlib.php";
use inquid\pdf\FPDF;
use app\models\DocumentoSoporte;
use app\models\DocumentoSoporteDetalle;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {
    function Header() {
        $id_documento = $GLOBALS['id_documento'];
        $documento = DocumentoSoporte::findOne($id_documento);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','idresolucion', $documento->idresolucion])->one();
        $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 30);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(39, 9);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Empresa:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 0);
       
        //FIN
        $this->SetXY(39, 13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Nit:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 0);
        
        //FIN
        $this->SetXY(39, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Dirección"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 0);
        //FIN
         $this->SetXY(39, 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Telefono:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 0);
        //FIN
        $this->SetXY(39, 25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Municipio:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 0);
        //FIN
        $this->SetXY(39, 29);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("T. regimen:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->tipoRegimen->regimen), 0, 0, 'L', 0);
        
         //FIN
         $this->SetXY(39, 32);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 5, utf8_decode("Email:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($config->emailmatricula), 0, 0, 'L', 0);
         //FIN
        //DATOS DE LA FACTURA
        $this->SetXY(135, 7);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(162, 7, utf8_decode("Documento Soporte por compras"), 0, 0, 'l', 0);
        $this->SetXY(155, 12);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 7, utf8_decode('No '.$documento->consecutivo.' '.$documento->numero_soporte), 0, 0, 'l', 0);
        $this->SetXY(140, 18);
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 7, utf8_decode('Resolución Dian No: '.$resolucion->nroresolucion), 0, 0, 'l', 0);
         $this->SetXY(127, 22);
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 7, utf8_decode('Fecha formalización: '.$resolucion->fechacreacion. ' hasta el ' .$resolucion->fechavencimiento), 0, 0, 'l', 0);
        //
        $this->SetXY(145, 26);
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 7, utf8_decode('Habilita rango: '.$resolucion->inicio_rango. ' hasta el ' .$resolucion->final_rango), 0, 0, 'l', 0);
         //
        $this->SetXY(155, 30);
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 7, utf8_decode('Vigencia: '.$resolucion->vigencia.' Meses'), 0, 0, 'l', 0);
        //linea
        $this->SetXY(10, 32);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 32.5);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //comienza datos del cliente
        //fin
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 40);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Nit:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(77, 5, utf8_decode(''.number_format($documento->proveedor->cedulanit,0).'-'.$documento->proveedor->dv), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("Proveedor:"), 0, 0, 'c', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 5, utf8_decode($documento->proveedor->nombrecorto), 0, 0, 'c', 0);
        //FIN
        $this->SetXY(10, 44);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Dirección:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(77, 5, utf8_decode($documento->proveedor->direccionproveedor), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("Telefono:"), 0, 0, 'c', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($documento->proveedor->telefonoproveedor), 0, 0, 'c', 0);
        //FIN
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Departamento:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(77, 5, utf8_decode($documento->proveedor->departamento->departamento), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("Municipio:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($documento->proveedor->municipio->municipio), 0, 0, 'L', 0);
        //fin
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha compra:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(77, 5, utf8_decode($documento->fecha_elaboracion), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("Forma pago:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($documento->formaPago->concepto), 0, 0, 'L', 0);
        //FIN
         //Lineas del encabezado
        $this->Line(10,62,10,130);
        $this->Line(23,62,23,70);
        $this->Line(126,62,126,70);
        $this->Line(151,62,151,70);
        $this->Line(176,62,176,70);
        $this->Line(201,62,201,130);
        //Cuadro de la nota
     //   $this->Line(10,170,151,170);//linea horizontal superior
        $this->Line(10,70,201,70);//linea horizontal superior del detalle
        $this->Line(10,130,201,130);//linea horizontal inferior del detalle
      //  $this->Line(10,178,10,202);//linea vertical
        //lineas para los cuadros de nit/cc,fecha,firma        
      //  $this->Line(10,218,10,245);//linea vertical x1,y1,x2,y2   
       // $this->Line(74,218,74,245);//linea vertical x1,y1,x2,y2
       // $this->Line(138,218,138,245);//linea vertical x1,y1,x2,y2
       // $this->Line(201,210,201,245);//linea vertical x1,y1,x2,y2                
        //Detalle factura
        $this->EncabezadoDetalles();
    }   
    function EncabezadoDetalles() {
         $this->Ln(6);
        $header = array('ITEM', 'DESCRIPCION', 'CANTIDAD', 'VR. UNITARIO', 'VR. PAGAR');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 103, 25, 25, 25);
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
        $detalle = DocumentoSoporteDetalle::find()->where(['=','id_documento_soporte',$model->id_documento_soporte])->one();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(13, 4, '1', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(103, 4, $detalle->descripcion, 0, 0, 'L');
        $pdf->Cell(25, 4, $detalle->cantidad, 0, 0, 'R');
        $pdf->Cell(25, 4, number_format($detalle->valor_unitario, 2, '.', ','), 0, 0, 'R');
        $pdf->Cell(25, 4, number_format($detalle->total_pagar, 2, '.', ','), 0, 0, 'R');
        $pdf->Ln();
       // $pdf->SetAutoPageBreak(true, 20);
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 73);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(25, 3, 'Valor en letras:',0,'L');
        $pdf->SetXY(32, 73);
        $this->SetFont('Arial', '', 8);
        $pdf->MultiCell(146, 3, utf8_decode(valorEnLetras($model->valor_pagar)),0,'J');
        //fin
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 78);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(25, 3, 'Observacion:',0,'L');
        $pdf->SetXY(32, 78);
        $this->SetFont('Arial', '', 8);
        $pdf->MultiCell(146, 3, utf8_decode($model->observacion),0,'J');
        //fin
        $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(151, 80);
        $pdf->MultiCell(25, 8, 'Total bruto:',1,'L');
        $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 80);
        $pdf->MultiCell(25, 8, number_format($detalle->valor_unitario, 2, '.', ','),1,'R');
        
        //fin
        $pdf->SetXY(151, 88);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(25, 8, 'Retefuente:',1,'L');
        $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 88);
        $pdf->MultiCell(25, 8, number_format($detalle->valor_retencion, 2, '.', ','),1,'R');
         //fin
        $pdf->SetXY(151, 96);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(25, 8, 'Total a pagar:',1,'L');
        $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 96);
        $pdf->MultiCell(25, 8, number_format($detalle->total_pagar, 2, '.', ','),1,'R');
        //impresion del cuds
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 125);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(25, 3, 'Cuds:',0,'L');
        $pdf->SetXY(20, 125);
        $this->SetFont('Arial', '', 8);
        $pdf->MultiCell(170, 3, utf8_decode($model->cuds),0,'J');
        
        //creacion de la representacion grafica
        $this->SetFont('Arial', '', 8);
        $qrstr = utf8_decode($model->qrstr);
        $pdf->SetXY(120, 70); // Establece la posición donde aparecerá el QR
        QRcode::png($qrstr,"test.png");
        $pdf->Image("test.png", 88, 90, 38, 35, "png");
        $pdf->SetXY(74, 85);
        $this->SetFont('Arial', 'B', 6);
        $pdf->Cell(64, 8, utf8_decode($config->razonsocialmatricula.'-'.$config->nitmatricula.'-'.$config->dv. ' Software Propio '),0,'J',1);
        // Insertar la imagen base64 directamente en el PDF
        $pdf->SetXY(10, 88); // Establecer la posición
    }
    
}
global $id_documento;
$id_documento = $model->id_documento_soporte;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("DocumentoSoporte$model->numero_soporte.pdf", 'D');

exit;

function zero_fill ($valor, $long = 0)
{
    return str_pad($valor, $long, '0', STR_PAD_LEFT);
}

function valorEnLetras($x) {
    if ($x < 0) {
        $signo = "menos ";
    } else {
        $signo = "";
    }
    $x = abs($x);
    $C1 = $x;

    $G6 = floor($x / (1000000));  // 7 y mas 

    $E7 = floor($x / (100000));
    $G7 = $E7 - $G6 * 10;   // 6 

    $E8 = floor($x / 1000);
    $G8 = $E8 - $E7 * 100;   // 5 y 4 

    $E9 = floor($x / 100);
    $G9 = $E9 - $E8 * 10;  //  3 

    $E10 = floor($x);
    $G10 = $E10 - $E9 * 100;  // 2 y 1 


    $G11 = round(($x - $E10) * 100);  // Decimales 
////////////////////// 

    $H6 = unidades($G6);

    if ($G7 == 1 AND $G8 == 0) {
        $H7 = "Cien ";
    } else {
        $H7 = decenas($G7);
    }

    $H8 = unidades($G8);

    if ($G9 == 1 AND $G10 == 0) {
        $H9 = "Cien ";
    } else {
        $H9 = decenas($G9);
    }

    $H10 = unidades($G10);

    if ($G11 < 10) {
        $H11 = "" . $G11;
    } else {
        $H11 = $G11;
    }

///////////////////////////// 
    if ($G6 == 0) {
        $I6 = " ";
    } elseif ($G6 == 1) {
        $I6 = "Millón ";
    } else {
        $I6 = "Millones ";
    }

    if ($G8 == 0 AND $G7 == 0) {
        $I8 = " ";
    } else {
        $I8 = "Mil ";
    }

    $I10 = "Pesos ";
    $I11 = "M.L ";

    $C3 = $signo . $H6 . $I6 . $H7 . $H8 . $I8 . $H9 . $H10 . $I10 . $H11 . $I11;

    return $C3; //Retornar el resultado 
}

function unidades($u) {
    if ($u == 0) {
        $ru = " ";
    } elseif ($u == 1) {
        $ru = "Un ";
    } elseif ($u == 2) {
        $ru = "Dos ";
    } elseif ($u == 3) {
        $ru = "Tres ";
    } elseif ($u == 4) {
        $ru = "Cuatro ";
    } elseif ($u == 5) {
        $ru = "Cinco ";
    } elseif ($u == 6) {
        $ru = "Seis ";
    } elseif ($u == 7) {
        $ru = "Siete ";
    } elseif ($u == 8) {
        $ru = "Ocho ";
    } elseif ($u == 9) {
        $ru = "Nueve ";
    } elseif ($u == 10) {
        $ru = "Diez ";
    } elseif ($u == 11) {
        $ru = "Once ";
    } elseif ($u == 12) {
        $ru = "Doce ";
    } elseif ($u == 13) {
        $ru = "Trece ";
    } elseif ($u == 14) {
        $ru = "Catorce ";
    } elseif ($u == 15) {
        $ru = "Quince ";
    } elseif ($u == 16) {
        $ru = "Dieciseis ";
    } elseif ($u == 17) {
        $ru = "Decisiete ";
    } elseif ($u == 18) {
        $ru = "Dieciocho ";
    } elseif ($u == 19) {
        $ru = "Diecinueve ";
    } elseif ($u == 20) {
        $ru = "Veinte ";
    } elseif ($u == 21) {
        $ru = "Veinti un ";
    } elseif ($u == 22) {
        $ru = "Veinti dos ";
    } elseif ($u == 23) {
        $ru = "Veinti tres ";
    } elseif ($u == 24) {
        $ru = "Veinti cuatro ";
    } elseif ($u == 25) {
        $ru = "Veinti cinco ";
    } elseif ($u == 26) {
        $ru = "Veinti seis ";
    } elseif ($u == 27) {
        $ru = "Veinti siente ";
    } elseif ($u == 28) {
        $ru = "Veintio cho ";
    } elseif ($u == 29) {
        $ru = "Veinti nueve ";
    } elseif ($u == 30) {
        $ru = "Treinta ";
    } elseif ($u == 31) {
        $ru = "Treinta y un ";
    } elseif ($u == 32) {
        $ru = "Treinta y dos ";
    } elseif ($u == 33) {
        $ru = "Treinta y tres ";
    } elseif ($u == 34) {
        $ru = "Treinta y cuatro ";
    } elseif ($u == 35) {
        $ru = "Treinta y cinco ";
    } elseif ($u == 36) {
        $ru = "Treinta y seis ";
    } elseif ($u == 37) {
        $ru = "Treinta y siete ";
    } elseif ($u == 38) {
        $ru = "Treinta y ocho ";
    } elseif ($u == 39) {
        $ru = "Treinta y nueve ";
    } elseif ($u == 40) {
        $ru = "Cuarenta ";
    } elseif ($u == 41) {
        $ru = "Cuarenta y un ";
    } elseif ($u == 42) {
        $ru = "Cuarenta y dos ";
    } elseif ($u == 43) {
        $ru = "Cuarenta y tres ";
    } elseif ($u == 44) {
        $ru = "Cuarenta y cuatro ";
    } elseif ($u == 45) {
        $ru = "Cuarenta y cinco ";
    } elseif ($u == 46) {
        $ru = "Cuarenta y seis ";
    } elseif ($u == 47) {
        $ru = "Cuarenta y siete ";
    } elseif ($u == 48) {
        $ru = "Cuarenta y ocho ";
    } elseif ($u == 49) {
        $ru = "Cuarenta y nueve ";
    } elseif ($u == 50) {
        $ru = "Cincuenta ";
    } elseif ($u == 51) {
        $ru = "Cincuenta y un ";
    } elseif ($u == 52) {
        $ru = "Cincuenta y dos ";
    } elseif ($u == 53) {
        $ru = "Cincuenta y tres ";
    } elseif ($u == 54) {
        $ru = "Cincuenta y cuatro ";
    } elseif ($u == 55) {
        $ru = "Cincuenta y cinco ";
    } elseif ($u == 56) {
        $ru = "Cincuenta y seis ";
    } elseif ($u == 57) {
        $ru = "Cincuenta y siete ";
    } elseif ($u == 58) {
        $ru = "Cincuenta y ocho ";
    } elseif ($u == 59) {
        $ru = "Cincuenta y nueve ";
    } elseif ($u == 60) {
        $ru = "Sesenta ";
    } elseif ($u == 61) {
        $ru = "Sesenta y un ";
    } elseif ($u == 62) {
        $ru = "Sesenta y dos ";
    } elseif ($u == 63) {
        $ru = "Sesenta y tres ";
    } elseif ($u == 64) {
        $ru = "Sesenta y cuatro ";
    } elseif ($u == 65) {
        $ru = "Sesenta y cinco ";
    } elseif ($u == 66) {
        $ru = "Sesenta y seis ";
    } elseif ($u == 67) {
        $ru = "Sesenta y siete ";
    } elseif ($u == 68) {
        $ru = "Sesenta y ocho ";
    } elseif ($u == 69) {
        $ru = "Sesenta y nueve ";
    } elseif ($u == 70) {
        $ru = "Setenta ";
    } elseif ($u == 71) {
        $ru = "Setenta y un ";
    } elseif ($u == 72) {
        $ru = "Setenta y dos ";
    } elseif ($u == 73) {
        $ru = "Setenta y tres ";
    } elseif ($u == 74) {
        $ru = "Setenta y cuatro ";
    } elseif ($u == 75) {
        $ru = "Setentaycinco ";
    } elseif ($u == 76) {
        $ru = "Setenta y seis ";
    } elseif ($u == 77) {
        $ru = "Setenta y siete ";
    } elseif ($u == 78) {
        $ru = "Setenta y ocho ";
    } elseif ($u == 79) {
        $ru = "Setenta y nueve ";
    } elseif ($u == 80) {
        $ru = "Ochenta ";
    } elseif ($u == 81) {
        $ru = "Ochenta y un ";
    } elseif ($u == 82) {
        $ru = "Ochenta y dos ";
    } elseif ($u == 83) {
        $ru = "Ochenta y tres ";
    } elseif ($u == 84) {
        $ru = "Ochenta y cuatro ";
    } elseif ($u == 85) {
        $ru = "Ochenta y cinco ";
    } elseif ($u == 86) {
        $ru = "Ochenta y seis ";
    } elseif ($u == 87) {
        $ru = "Ochenta y siete ";
    } elseif ($u == 88) {
        $ru = "Ochenta y ocho ";
    } elseif ($u == 89) {
        $ru = "Ochenta y nueve ";
    } elseif ($u == 90) {
        $ru = "Noventa ";
    } elseif ($u == 91) {
        $ru = "Noventa y un ";
    } elseif ($u == 92) {
        $ru = "Noventa y dos ";
    } elseif ($u == 93) {
        $ru = "Noventa y tres ";
    } elseif ($u == 94) {
        $ru = "Noventa y cuatro ";
    } elseif ($u == 95) {
        $ru = "Noventa y cinco ";
    } elseif ($u == 96) {
        $ru = "Noventa y seis ";
    } elseif ($u == 97) {
        $ru = "Noventaysiete ";
    } elseif ($u == 98) {
        $ru = "Noventa y ocho ";
    } else {
        $ru = "Noventa y nueve ";
    }
    return $ru; //Retornar el resultado 
}

function decenas($d) {
    if ($d == 0) {
        $rd = "";
    } elseif ($d == 1) {
        $rd = "Ciento ";
    } elseif ($d == 2) {
        $rd = "Doscientos ";
    } elseif ($d == 3) {
        $rd = "Trescientos ";
    } elseif ($d == 4) {
        $rd = "Cuatrocientos ";
    } elseif ($d == 5) {
        $rd = "Quinientos ";
    } elseif ($d == 6) {
        $rd = "Seiscientos ";
    } elseif ($d == 7) {
        $rd = "Setecientos ";
    } elseif ($d == 8) {
        $rd = "Ochocientos ";
    } else {
        $rd = "Novecientos ";
    }
    return $rd; //Retornar el resultado 
}