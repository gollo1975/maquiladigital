<?php
ob_start();
include "../vendor/phpqrcode/qrlib.php";
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
        $detalle = Notacreditodetalle::find()->where(['=','idnotacredito', $idnotacredito])->one();
      //INICIO
        $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 30);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(53, 9);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Empresa:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 0);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(53, 13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Nit:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Dirección"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
         $this->SetXY(53, 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Telefono:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Municipio:"), 0, 0, 'l', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 29);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Tipo regimen:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->tipoRegimen->regimen), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
         //FIN
         $this->SetXY(53, 32);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("Email:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->emailmatricula), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
         //FIN
         //DATOS DE LA FACTURA
        $this->SetXY(140, 10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(162, 7, utf8_decode("NOTA CREDITO A FACTURA"), 0, 0, 'l', 0);
        $this->SetXY(159, 16);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 7, utf8_decode('No '.$notacredito->numero), 0, 0, 'l', 0);
        //linea
        $this->SetXY(10, 32);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 32.5);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________"), 0, 0, 'C', 0);
        //comienza datos del cliente
        //nta credito
        
         $this->SetFillColor(200, 200, 200);
         //INICIO
        $this->SetXY(10, 40); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16, 5, utf8_decode("Cliente:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(90, 5, utf8_decode($notacredito->cliente->nombrecorto), 1, 0, 'L');
        //fin
        //proceso nota
        $this->SetXY(120, 40); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(36, 5, utf8_decode("Fecha nota credito:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(44, 5, utf8_decode($notacredito->fechapago), 1, 0, 'L');
        //fin
        $this->SetXY(10, 46); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16, 5, utf8_decode("Nit:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(19, 5, utf8_decode($notacredito->cliente->cedulanit . '-' . $notacredito->cliente->dv), 1, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Direccion:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(51, 5, utf8_decode($notacredito->cliente->direccioncliente), 1, 0, 'L');
        //fin
         //proceso nota
        $this->SetXY(120, 46); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(36, 5, utf8_decode("Numero de factura:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(44, 5, utf8_decode($detalle->nrofactura), 1, 0, 'L');
        //fin
        $this->SetXY(10, 52); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16, 5, utf8_decode("Telefono:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(19, 5, utf8_decode($notacredito->cliente->telefonocliente), 1, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Email:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(51, 5, utf8_decode($notacredito->cliente->email_envio_factura_dian), 1, 0, 'L');
        //fin
         //proceso nota
        $this->SetXY(120, 52); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(36, 5, utf8_decode("Fecha hora creacion:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(44, 5, utf8_decode($notacredito->fecha), 1, 0, 'L');
        //finn
         $this->SetXY(10, 58); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16, 5, utf8_decode("Municipio:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 7);
        $this->Cell(19, 5, utf8_decode(substr($notacredito->cliente->municipio->municipio,0, 10)), 1, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Depto:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(51, 5, utf8_decode($notacredito->cliente->departamento->departamento), 1, 0, 'L');
        //fin
         //proceso nota
        $this->SetXY(120, 58); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(36, 5, utf8_decode("Fecha recepcion dian:"), 1, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(44, 5, utf8_decode($notacredito->fecha_recepcion_dian), 1, 0, 'L');
        //finn
        //informacion del cufe facture
        $this->SetXY(10, 64); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(16, 5, utf8_decode("Cufe:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(10, 5, utf8_decode($notacredito->cufe), 0, 0, 'L');
        //Lineas del encabezado
        $this->Line(10, 75, 10, 200);
        $this->Line(23, 75, 23, 200);
        $this->Line(122, 75, 122, 200);
        $this->Line(136, 75, 136, 200);
        $this->Line(156, 75, 156, 200);
        $this->Line(176, 75, 176, 200);
        $this->Line(200, 75, 200, 200);
        $this->Line(10, 200, 200, 200); //linea vertical inferior x1,y1,x2,y2
        //Cuadro de la nota
        $this->Line(10, 204, 156, 204); //linea horizontal superior
        //$this->Line(10,182,10,190);//linea vertical
        $this->Line(10, 212, 156, 212); //linea horizontal inferior
        //Linea de las observacines
        $this->Line(10, 204, 10, 244); //linea vertical
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'CONCEPTO', 'CANT.','VLR UNIT.', 'IVA', 'TOTAL');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 99, 14, 20, 20, 24);
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
        $config = Matriculaempresa::findOne(1);
        $detalles = Notacreditodetalle::find()->where(['=', 'idnotacredito', $model->idnotacredito])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        foreach ($detalles as $detalle) {
            $pdf->Cell(13, 4, $detalle->id, 0, 0, 'L');
            $pdf->Cell(99, 4, $detalle->conceptoFacturacion->concepto, 0, 0, 'L');
            $pdf->Cell(14, 4, $detalle->cantidad, 0, 0, 'C');
            $pdf->Cell(20, 4, number_format($detalle->precio_unitario, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($detalle->valor_iva, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(24, 4, number_format($detalle->total_nota, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 13);
        }
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 204);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode(valorEnLetras($model->total)), 0, 'J');
        $pdf->SetXY(156, 204);
        $pdf->MultiCell(20, 8, 'Subtotal:', 1, 'L');
        $pdf->SetXY(176, 204);
        $pdf->MultiCell(25, 8, number_format($model->valor, 2, '.', ','), 1, 'R');
        $pdf->SetXY(10, 212);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode('Observaciones: ' . $model->observacion), 0, 'J');
        $pdf->SetXY(156, 212);
        $pdf->MultiCell(20, 8, 'Impuesto:', 1, 'L');
        $pdf->SetXY(176, 212);
        $pdf->MultiCell(25, 8, number_format($model->iva, 2, '.', ','), 1, 'R');
        $pdf->SetXY(156, 220);
        $pdf->MultiCell(20, 8, 'Rete iva:', 1, 'L');
        $pdf->SetXY(176, 220);
        $pdf->MultiCell(25, 8, number_format($model->reteiva, 2, '.', ','), 1, 'R');
        $pdf->SetXY(156, 228);
        $pdf->MultiCell(20, 8, 'Rete fuente:', 1, 'L');
        $pdf->SetXY(176, 228);
        $pdf->MultiCell(25, 8, number_format($model->retefuente, 2, '.', ','), 1, 'R');
        $pdf->SetXY(10, 228);
        $pdf->MultiCell(146, 16, 'FIRMA Y SELLO', 1, 'J');
        $pdf->SetXY(156, 236);
        $pdf->MultiCell(20, 8, 'Total devo.:', 1, 'L');
        $pdf->SetXY(176, 236);
        $pdf->MultiCell(25, 8, number_format($model->total, 2, '.', ','), 1, 'R');
        
        //representacion grafica
        $this->SetFont('Arial', '', 8);
        $qrstr = utf8_decode($model->qrstr);
        $pdf->SetXY(120, 70); // Establece la posición donde aparecerá el QR
        QRcode::png($qrstr,"test.png");
        $pdf->Image("test.png", 153, 244.5, 38, 35, "png");
        $pdf->SetXY(140, 276);
        $this->SetFont('Arial', 'B', 6);
        $pdf->Cell(64, 8, utf8_decode($config->razonsocialmatricula.'-'.$config->nitmatricula.'-'.$config->dv. ' Software Propio '),0,'J',1);
        // Insertar la imagen base64 directamente en el PDF
        $pdf->SetXY(10, 245); // Establecer la posición
        
        //colocacion de cufe
        $pdf->SetXY(10, 255);
        $this->SetFont('Arial', '', 7);
        $pdf->MultiCell(146, 4, utf8_decode('CUDE: '.$model->cude),0,'J'); //CUFE
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
$pdf->Output("NotaCredito$model->numero.pdf", 'D');

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
