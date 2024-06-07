<?php

use inquid\pdf\FPDF;
use app\models\Notacredito;
use app\models\Notacreditodetalle;
use app\models\Facturaventa;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $idnotacredito = $GLOBALS['idnotacredito'];
        $notacredito = Notacredito::findOne($idnotacredito);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        $detallesnotacredito = Notacreditodetalle::find()->where(['=', 'idnotacredito', $idnotacredito])->all();
        foreach ($detallesnotacredito as $detalle) {
            $idfactura = $detalle->idfactura;
        }
        $factura = Facturaventa::findOne($idfactura);
        //Logo
        $this->SetXY(53, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFont('Arial', '', 10);
        $this->Cell(150, 7, utf8_decode($config->razonsocialmatricula. " NIT:" .$config->nitmatricula." - ".$config->dv), 0, 0, 'C', 0);
        $this->SetXY(53, 15);
        $this->Cell(150, 7, utf8_decode($config->direccionmatricula. "Teléfono:" .$config->telefonomatricula), 0, 0, 'C', 0);
        $this->SetXY(53, 20);
        $this->Cell(150, 7, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'C', 0);
        $this->SetXY(53, 25);
        $this->Cell(150, 7, utf8_decode($config->tipoRegimen->regimen), 0, 0, 'C', 0);
        $this->SetXY(53, 30);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(150, 7, utf8_decode("Autorización Numeración de Facturación: Res. Dian N° " .$config->resolucion->nroresolucion), 0, 0, 'C', 0);
        $this->SetXY(53, 35);
        $this->Cell(150, 7, utf8_decode("Fecha: ". date('d-m-Y', strtotime($config->resolucion->fechacreacion)). " Numeración: ". $config->resolucion->desde. " AL ". $config->resolucion->hasta), 0, 0, 'C', 0);
        $this->SetXY(53, 40);
        $this->Cell(150, 7, utf8_decode("Código Actividad: " .$config->resolucion->codigoactividad. " Descripción: ". $config->resolucion->descripcion), 0, 0, 'C', 0);
        $this->SetXY(10, 42);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //Factura
        $this->SetXY(10, 47);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(162, 7, utf8_decode("NOTA CRÉDITO"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. ' . str_pad($notacredito->numero, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 59); //FILA 1
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(29, 5, utf8_decode("FECHA FACTURA:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->fechainicio), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("FECHA VCTO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->fechavcto), 0, 0, 'c');
        $this->SetXY(10, 64); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(29, 5, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->cliente->cedulanit . '-' . $factura->cliente->dv), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CLIENTE:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->cliente->nombrecorto), 0, 0, 'c');
        $this->SetXY(10, 69); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(29, 5, utf8_decode("DIRECCIÓN:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->cliente->direccioncliente), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("TELÉFONO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($factura->cliente->telefonocliente), 0, 0, 'c');
        $this->SetXY(10, 74); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(29, 5, utf8_decode("FECHA PROCESO:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($notacredito->fechapago), 0, 0, 'L');
        $this->Cell(24, 5, utf8_decode(""), 0, 0, 'c');
        $this->Cell(70, 5, utf8_decode(""), 0, 0, 'c');
        //Lineas del encabezado
        $this->Line(10, 85, 10, 220);
        $this->Line(23, 85, 23, 220);
        $this->Line(136, 85, 136, 220);
        $this->Line(156, 85, 156, 220);
        $this->Line(176, 85, 176, 220);
        $this->Line(201, 85, 201, 220);
        $this->Line(10, 220, 201, 220); //linea vertical inferior x1,y1,x2,y2
        //Cuadro de la nota
        $this->Line(10, 230, 156, 230); //linea horizontal superior
        //$this->Line(10,182,10,190);//linea vertical
        $this->Line(10, 238, 156, 238); //linea horizontal inferior
        //Linea de las observacines
        $this->Line(10, 230, 10, 254); //linea vertical
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('ITEM', 'CONCEPTO', utf8_decode('N° FACTURA'), 'VLR UNIT', 'SUBTOTAL');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 113, 20, 20, 25);
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

    function Body($pdf, $model) {
        $detalles = Notacreditodetalle::find()->where(['=', 'idnotacredito', $model->idnotacredito])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        foreach ($detalles as $detalle) {
            $pdf->Cell(13, 4, 1, 0, 0, 'L');
            $pdf->Cell(113, 4, $model->conceptonota->concepto, 0, 0, 'L');
            $pdf->Cell(20, 4, $detalle->nrofactura, 0, 0, 'C');
            $pdf->Cell(20, 4, number_format($detalle->valor, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(25, 4, number_format($detalle->valor, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 230);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode(valorEnLetras($model->total)), 0, 'J');
        $pdf->SetXY(156, 230);
        $pdf->MultiCell(20, 8, 'SUBTOTAL:', 1, 'L');
        $pdf->SetXY(176, 230);
        $pdf->MultiCell(25, 8, number_format($model->valor, 2, '.', ','), 1, 'R');
        $pdf->SetXY(10, 238);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode('Observaciones: ' . $model->observacion), 0, 'J');
        $pdf->SetXY(156, 238);
        $pdf->MultiCell(20, 8, 'IVA:', 1, 'L');
        $pdf->SetXY(176, 238);
        $pdf->MultiCell(25, 8, number_format($model->iva, 2, '.', ','), 1, 'R');
        $pdf->SetXY(156, 246);
        $pdf->MultiCell(20, 8, 'RETE IVA:', 1, 'L');
        $pdf->SetXY(176, 246);
        $pdf->MultiCell(25, 8, number_format($model->reteiva, 2, '.', ','), 1, 'R');
        $pdf->SetXY(156, 254);
        $pdf->MultiCell(20, 8, 'RETE FTE:', 1, 'L');
        $pdf->SetXY(176, 254);
        $pdf->MultiCell(25, 8, number_format($model->retefuente, 2, '.', ','), 1, 'R');
        $pdf->SetXY(10, 254);
        $pdf->MultiCell(146, 16, 'FIRMA Y SELLO', 1, 'J');
        $pdf->SetXY(156, 262);
        $pdf->MultiCell(20, 8, 'TOTAL:', 1, 'L');
        $pdf->SetXY(176, 262);
        $pdf->MultiCell(25, 8, number_format($model->total, 2, '.', ','), 1, 'R');
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $idnotacredito;
$idnotacredito = $model->idnotacredito;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("NotaCredito$model->idnotacredito.pdf", 'D');

exit;

function zero_fill($valor, $long = 0) {
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
