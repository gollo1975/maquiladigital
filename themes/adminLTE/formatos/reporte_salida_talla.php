<?php

use inquid\pdf\FPDF;
use app\models\EficienciaModuloDiarioDetalle;
use app\models\EficienciaModuloDiario;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        
        $id_entrada = $GLOBALS['id_entrada'];
        $entrada = EficienciaModuloDiarioDetalle::findOne($id_entrada);
        $config = Matriculaempresa::findOne(1);
        $orden = app\models\Ordenproduccion::findOne($entrada->idordenproduccion);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
                //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(20, 9);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 5, utf8_decode("Empresa:"), 0, 0, '1', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 0);
        //FIN
        $this->SetXY(10, 11);
         $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 12);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //fin
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(8, 18);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(53, 7, utf8_decode("Pedido No"), 0, 0, 'l', 0);
        $this->Cell(15, 7, utf8_decode($entrada->ordenproduccion->ordenproduccion), 0, 0, 'l', 0);
      //  $this->SetFillColor(200, 200, 200);
        //fin
        $this->SetXY(8, 27); //FILA 1
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Prenda:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 9);        
        $this->Cell(60, 5, utf8_decode($entrada->detalleorden->productodetalle->prendatipo->prenda ), 0, 0, 'L','0');                
        //fin
         $this->SetXY(8, 36); //FILA 1
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Talla:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 10);        
        $this->Cell(60, 5, utf8_decode($entrada->detalleorden->productodetalle->prendatipo->talla->talla), 0, 0, 'L','0');                
        //fin
        $this->SetXY(8, 45); //FILA 1
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Referencia:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 5, utf8_decode('R'.$entrada->ordenproduccion->codigoproducto), 0, 0, 'L','0');
        //fin
        $this->SetXY(8, 54); //FILA 3
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Observacion:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 8);   
        $this->MultiCell(60, 5, utf8_decode($entrada->ordenproduccion->observacion), 0, 0, 'L','0');          
        //fin
        
        $this->SetXY(8, 64); //FILA 3
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Cantidad:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 5, utf8_decode($entrada->unidades_confeccionadas), 0, 0, 'L','0');
        //fin
        $this->SetXY(8, 73); //FILA 2
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(25, 5, utf8_decode("Fecha salida:"), 0, 0, 'L','0');
        $this->SetFont('Arial', '', 10);        
        $this->Cell(60, 5, utf8_decode($entrada->fecha_dia_confeccion), 0, 0, 'L','0'); 
        //fin
       
       
        //fin
//        $this->Line(7,50,95,50);//linea superior horizontal
//        $this->Line(7,170,7,50);//primera linea en y
//        $this->Line(95,170,95,50);//primera linea en y
//        $this->Line(7,170,95,170);//linea inferior horizontal
//         //Lineas del encabezado
//        $this->Line(10,120,10,83); //la primera con la tercera es para la la raya iguales, la segunda es el lago o corto y la tercera es la tamaño de largo
//        $this->Line(42,120,42,83);
//        $this->Line(54,120,54,83);
//        $this->Line(69,120,69,83);
//        $this->Line(93,120,93,83);
//        $this->Line(10,120,93,120);//linea inferior horizontal
        $this->EncabezadoDetalles();             
        //Detalle factura
    }   
    function EncabezadoDetalles() {
       
    
       
    }
    function Body($pdf, $model) {
        
    }
    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(8, 100, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(250, 205, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }
}

global $id_entrada;
$id_entrada = $model->id_entrada;
$pdf = new PDF('P', 'mm', array(100,110));
//$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("SalidaTallasNo:$model->id_entrada.pdf", 'D');

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
