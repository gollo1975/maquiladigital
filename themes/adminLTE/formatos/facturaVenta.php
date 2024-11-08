<?php

use inquid\pdf\FPDF;
use app\models\Facturaventa;
use app\models\Facturaventadetalle;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $idfactura = $GLOBALS['idfactura'];
        $factura = Facturaventa::findOne($idfactura);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        $resolucion = \app\models\Resolucion::find()->where(['=','activo', 0])->andWhere(['=','abreviatura', 'FE'])->one();
        //Logo
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
        $this->SetXY(135, 7);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(162, 7, utf8_decode("FACTURA ELECTRONICA DE VENTA"), 0, 0, 'l', 0);
        $this->SetXY(155, 12);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 7, utf8_decode('No '.$factura->consecutivo.' '.$factura->nrofactura), 0, 0, 'l', 0);
        $this->SetXY(140, 18);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 7, utf8_decode('Resolución Dian No: '.$resolucion->nroresolucion), 0, 0, 'l', 0);
        //fin
         //
        $this->SetXY(127, 22);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 7, utf8_decode('Fecha formalización: '.$resolucion->fechacreacion. ' hasta el ' .$resolucion->fechavencimiento), 0, 0, 'l', 0);
        //
        $this->SetXY(145, 26);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 7, utf8_decode('Habilita rango: '.$resolucion->inicio_rango. ' hasta el ' .$resolucion->final_rango), 0, 0, 'l', 0);
         //
        $this->SetXY(155, 30);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 7, utf8_decode('Vigencia: '.$resolucion->vigencia.' Meses'), 0, 0, 'l', 0);
        //linea
        $this->SetXY(10, 32);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 32.5);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //comienza datos del cliente
          //FIN
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 40);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Nit:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(82, 5, utf8_decode($factura->cliente->cedulanit.'-'.$factura->cliente->dv), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Cliente:"), 0, 0, 'c', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($factura->cliente->nombrecorto), 0, 0, 'c', 0);
        //FIN
        $this->SetXY(10, 44);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Dirección:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(82, 5, utf8_decode($factura->cliente->direccioncliente), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Telefono:"), 0, 0, 'c', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($factura->cliente->telefonocliente), 0, 0, 'c', 0);
        //FIN
        $this->SetXY(10, 48);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Departamento:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(82, 5, utf8_decode($factura->cliente->departamento->departamento), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Municipio:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($factura->cliente->municipio->municipio), 0, 0, 'L', 0);
        //fin
        $this->SetXY(10, 52);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha expedición:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(82, 5, utf8_decode($factura->fecha_inicio), 0, 0, 'L',0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Fecha vcto:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($factura->fecha_vencimiento), 0, 0, 'L', 0);
        //FIN
        $this->SetXY(10, 56);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Email.:"), 0, 0, 'L', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(82, 5, utf8_decode($factura->cliente->emailcliente), 0, 0, 'l', 0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Forma pago:"), 0, 0, 'J', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(65, 5, utf8_decode($factura->formaPago->concepto.' ('.$factura->plazopago.')  Dias'), 0, 0, 'L', 0);
        //FIN
      
        //Lineas del encabezado
        $this->Line(10,67,10,170);
        $this->Line(23,67,23,170);
        $this->Line(126,67,126,170);
        $this->Line(151,67,151,170);
        $this->Line(176,67,176,170);
        $this->Line(201,67,201,170);
        //Cuadro de la nota
        $this->Line(10,170,156,170);//linea horizontal superior
        $this->Line(10,170,10,178);//linea vertical
        $this->Line(10,178,156,178);//linea horizontal inferior
        //Linea de las observacines
        $this->Line(10,178,10,202);//linea vertical
        //lineas para los cuadros de nit/cc,fecha,firma        
        $this->Line(10,218,10,245);//linea vertical x1,y1,x2,y2   
        $this->Line(74,218,74,245);//linea vertical x1,y1,x2,y2
        $this->Line(138,218,138,245);//linea vertical x1,y1,x2,y2
        $this->Line(201,210,201,245);//linea vertical x1,y1,x2,y2                
        //Detalle factura
        $this->EncabezadoDetalles();
        
        
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'DESCRIPCION DEL SERVICIO', 'CANTIDAD', 'VR. UNITARIO', 'TOTAL');
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
        $detalles = Facturaventadetalle::find()->where(['=','idfactura',$model->idfactura])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        $cant = 0;
        foreach ($detalles as $detalle) { 
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(13, 4, $detalle->codigoproducto, 0, 0, 'L');
            $pdf->SetFont('Arial', '', 8);
            if($detalle->idproductodetalle == ''){
                $pdf->Cell(103, 4, $detalle->conceptoFactura->concepto, 0, 0, 'L');
            }else{
                $pdf->Cell(103, 4, $detalle->productodetalle->prendatipo->prenda.' / '.$detalle->productodetalle->prendatipo->talla->talla, 0, 0, 'L');
            }
            
            $pdf->Cell(25, 4, $detalle->cantidad, 0, 0, 'R');
            $pdf->Cell(25, 4, number_format($detalle->preciounitario, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(25, 4, number_format($detalle->total, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
            $cant = $cant + $detalle->cantidad;
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 170);
        $this->SetFont('Arial', '', 8);
        $pdf->MultiCell(146, 4, utf8_decode(valorEnLetras($model->totalpagar)),0,'J');
        $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(156, 170);
        $pdf->MultiCell(20, 8, 'SUBTOTAL:',1,'C');
        $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 170);
        $pdf->MultiCell(25, 8, number_format($model->subtotal, 0, '.', ','),1,'R');
        $pdf->SetXY(10, 178);
        $this->SetFont('Arial', '', 8);
        $pdf->MultiCell(146, 4, utf8_decode('Observaciones: '.$model->observacion),0,'J');
        //fin
        $pdf->SetXY(10, 197);
        $this->SetFont('Arial', '', 7);
        $pdf->MultiCell(146, 4, utf8_decode('Cufe: '.$model->cufe),0,'J'); //CUFE
        //fin
         $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(156, 178);
        $pdf->MultiCell(20, 8, 'RETE FTE:',1,'C');
         $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 178);
        $pdf->MultiCell(25, 8, number_format($model->retencionfuente, 0, '.', ','),1,'R');
         $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(156, 186);
        $pdf->MultiCell(20, 8, 'IVA:',1,'C');
         $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 186);
        $pdf->MultiCell(25, 8, number_format($model->impuestoiva, 0, '.', ','),1,'R');
         $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(156, 194);
        $pdf->MultiCell(20, 8, 'RETE IVA:',1,'C');
         $this->SetFont('Arial', '', 8);
        $pdf->SetXY(176, 194);
        $pdf->MultiCell(25, 8, number_format($model->retencioniva, 0, '.', ','),1,'R');
        $pdf->SetXY(10, 202);
        $pdf->MultiCell(108, 8, '',1,'R',0);
         $this->SetFont('Arial', 'B', 8);
        $pdf->SetXY(118, 202);
        $pdf->MultiCell(38, 8, 'TOTAL CANTIDAD: '.$cant,1,'C',0);
        $pdf->SetXY(156, 202);           
        $pdf->MultiCell(20, 8, 'TOTAL:',1,'C',0);
         $this->SetFont('Arial', '',9);
        $pdf->SetXY(176, 202);
        $pdf->MultiCell(25, 8, number_format($model->totalpagar, 0, '.', ','),1,'R',0);
        $pdf->SetXY(10, 210);//recibido,aceptado        
        $pdf->Cell(64, 8, 'RECIBIDO POR',1,'J',1);        
        $pdf->SetXY(74, 210);//recibido,aceptado 
        $pdf->Cell(64, 8, 'ACEPTADO POR',1,'J',1);
        $pdf->SetXY(138, 210);//recibido,aceptado 
        $this->SetFont('Arial', '', 8);
        $pdf->SetXY(10, 245);//nit,fecha,fecha,firma  
        $pdf->MultiCell(191, 4, utf8_decode($config->declaracion),1,'J');
        $pdf->SetXY(10, 266);//tipo cuenta
        $pdf->Cell(191, 5, 'Tipo de cuenta: '.$config->bancoFactura->tipoCuenta.'  - Numero de cuenta: '.$config->bancoFactura->numerocuenta.' - Entidad bancaria: '.$config->bancoFactura->entidad,1,'C');
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $idfactura;
$idfactura = $model->idfactura;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Factura$model->nrofactura.pdf", 'D');

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

