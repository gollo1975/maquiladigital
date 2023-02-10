<?php

use inquid\pdf\FPDF;
use app\models\Remision;
use app\models\Remisiondetalle;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\Producto;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $idremision = $GLOBALS['idremision'];
        $remision = Remision::findOne($idremision);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);        
        //Encabezado
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

        //ORDEN PRODUCCION
        $this->SetXY(10, 36);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(162, 7, utf8_decode("CLASIFICACION SEGUNDAS"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($remision->numero, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);        
        $this->SetFillColor(200, 200, 200);
        //inicio
        $this->SetXY(10, 42); //FILA 1
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->cedulanit . '-' . $remision->ordenproduccion->cliente->dv), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("FECHA LLEGADA:"), 0, 0, 'J');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->ordenproduccion->fechallegada), 0, 0, 'J');
        //fin
        $this->SetXY(10, 46); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CLIENTE:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->nombrecorto), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("FECHA ENTREGA:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->fechacreacion), 0, 0, 'J');
        //fin
        $this->SetXY(10, 50); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("TELÉFONO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->telefonocliente), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("ORDEN PRODUCCIÓN:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->ordenproduccion->ordenproduccion), 0, 0, 'J');
        $this->SetXY(10, 54); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("DIRECCIÓN:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->direccioncliente), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("CÓDIGO PRODUCTO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->ordenproduccion->codigoproducto), 0, 0, 'J');
        $this->SetXY(10, 58); //FILA 5
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("EMAIL:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->emailcliente), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("TIPO ORDEN:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->ordenproduccion->tipo->tipo), 0, 0, 'J');
        $this->SetXY(10, 62); //FILA 6
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CONTACTO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($remision->ordenproduccion->cliente->contacto), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("MUNICIPIO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($remision->ordenproduccion->cliente->municipio->municipio), 0, 0, 'J');
        //Lineas del encabezado
        //color
        $this->Line(10, 92, 10, 190);//x1,y1,x2,y2 
        $ordendetalles = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$remision->idordenproduccion])->all();
        $nregistros = count($ordendetalles);
       
        if ($nregistros == 5) {
        $this->Line(50, 70, 50, 190);//x1,y1,x2,y2   
        $this->Line(69, 70, 69, 190);//x1,y1,x2,y2
        $this->Line(88, 70, 88, 190);//x1,y1,x2,y2
        $this->Line(107, 70, 107, 190);//x1,y1,x2,y2
        $this->Line(126, 70, 126, 190);//x1,y1,x2,y2
        $this->Line(145, 70, 145, 190);//x1,y1,x2,y2
        $this->Line(180, 70, 180, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 6) {        
        $this->Line(81, 92, 81, 190);//x1,y1,x2,y2           
        $this->Line(92.6, 92, 92.6, 190);//x1,y1,x2,y2        
        $this->Line(104.2, 92, 104.2, 190);//x1,y1,x2,y2        
        $this->Line(115.8, 92, 115.8, 190);//x1,y1,x2,y2
        $this->Line(127.4, 92, 127.4, 190);//x1,y1,x2,y2        
        $this->Line(139, 92, 139, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 7) {        
        $this->Line(50, 70, 50, 190);//x1,y1,x2,y2           
        $this->Line(65, 70, 65, 190);//x1,y1,x2,y2        
        $this->Line(80, 70, 80, 190);//x1,y1,x2,y2        
        $this->Line(95, 70, 95, 190);//x1,y1,x2,y2
        $this->Line(110, 70, 110, 190);//x1,y1,x2,y2        
        $this->Line(125, 70, 125, 190);//x1,y1,x2,y2
        $this->Line(140, 70, 141, 190);//x1,y1,x2,y2
        $this->Line(155, 70, 155, 190);//x1,y1,x2,y2
        $this->Line(190, 70, 190, 190);//x1,y1,x2,y2
        }
        
        //Detalle factura
        $this->EncabezadoDetalles($nregistros);
    }

    function EncabezadoDetalles($nregistros) {
        $this->Ln(7);
        $idremision = $GLOBALS['idremision'];
        $tallasremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$idremision])->one();
        if ($tallasremision->txs == 1){
                $datostallas[] = 'XS';
            }
            if ($tallasremision->ts == 1){
                $datostallas[] = 'S';
            }
            if ($tallasremision->tm == 1){
                $datostallas[] = 'M';
            }
            if ($tallasremision->tl == 1){
                $datostallas[] = 'L';
            }
            if ($tallasremision->txl == 1){
                $datostallas[] = 'XL';
            }
             if ($tallasremision->txxl == 1){
                $datostallas[] = 'XXL';
            }
            if ($tallasremision->t2 == 1){
                $datostallas[] = '2';
            }
            if ($tallasremision->t4 == 1){
                $datostallas[] = '4';
            }
            if ($tallasremision->t6 == 1){
                $datostallas[] = '6';
            }
            if ($tallasremision->t8 == 1){
                $datostallas[] = '8';
            }
            if ($tallasremision->t10 == 1){
                $datostallas[] = '10';
            }
            if ($tallasremision->t12 == 1){
                $datostallas[] = '12';
            }
            if ($tallasremision->t14 == 1){
                $datostallas[] = '14';
            }
            if ($tallasremision->t16 == 1){
                $datostallas[] = '16';
            }
            
        $array1 = array('CONCEPTO');        
        $array2 = $datostallas;
        $array3 = array('TOTAL UNIDADES');
        $array4 = array_merge($array1, $array2, $array3);
        $header = $array4;
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        
        if ($nregistros == 5){
            $w = array(40, 19, 19, 19, 19, 19, 35);
        }
        if ($nregistros == 7){
            $w = array(40, 15, 15, 15, 15, 15, 15, 15, 35);
        }
        
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
        $this->SetFillColor(200, 200, 200);
        $detalles = \app\models\ClasificacionSegundas::find()->where(['=', 'id_remision', $model->id_remision])->all();
        $ordendetalles = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$model->idordenproduccion])->all();
        $nregistros = count($ordendetalles);
        if ($nregistros == 5) {
            $ancho = 19;
        }
        if ($nregistros == 6) {
            $ancho = 17;
        }
        if ($nregistros == 7) {
            $ancho = 15;
        }
       
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'b', 10);
        $items = count($detalles);
        $txs = 0; $ts = 0; $tm = 0; $tl = 0; $txl = 0; $txxl = 0;
        foreach ($detalles as $detalle) {
            $txs = $txs + $detalle->xs;
            $ts = $ts + $detalle->s;
            $tm = $tm + $detalle->m;
            $tl = $tl + $detalle->l;
            $txl = $txl + $detalle->xl;
            $txxl = $txxl + $detalle->xxl;
            $pdf->Cell(40, 4.5, $detalle->tipo->concepto, 1, 0, 'J');
           
           
            if ($detalle->txs == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->xs, 1, 0, 'R');
            }
            if ($detalle->ts == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->s, 1, 0, 'R');
            }
            if ($detalle->tm == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->m, 1, 0, 'R');
            }
            if ($detalle->tl == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->l, 1, 0, 'R');
            }
            if ($detalle->txl == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->xl, 1, 0, 'R');
            }
            if ($detalle->txxl == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->xxl, 1, 0, 'R');
            }
            if ($detalle->t2 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a2, 1, 0, 'R');
            }
            if ($detalle->t4 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a4, 1, 0, 'R');
            }
            if ($detalle->t6 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a6, 1, 0, 'R');
            }
            if ($detalle->t8 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a8, 1, 0, 'R');
            }                
            if ($detalle->t10 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a10, 1, 0, 'R');
            }
            if ($detalle->t12 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a12, 1, 0, 'R');
            }
            if ($detalle->t14 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a14, 1, 0, 'R');
            }
            if ($detalle->t16 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a16, 1, 0, 'R');
            }
             $pdf->Cell(35, 4.5, $detalle->unidades, 1, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $ct = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;

        $cantidadesremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$model->id_remision])->all();
        $total = 0;
        foreach ($cantidadesremision as $val){
            $total += $val->unidades;
            if ($val->txs == 1){
                $cxs = $cxs + $val->xs;
            }
            if ($val->ts == 1){
                $cs = $cs + $val->s;
            }
            if ($val->tm == 1){
                $cm = $cm + $val->m;
            }
            if ($val->tl == 1){
                $cl = $cl + $val->l;
            }
            if ($val->txl == 1){
                $cxl = $cxl + $val->xl;
            }
             if ($val->txxl == 1){
                $cxxl = $cxxl + $val->xxl;
            }
            if ($val->t2 == 1){
                $c2 = $c2 + $val->a2;
            }
            if ($val->t4 == 1){
                $c4 = $c4 + $val->a4;
            }
            if ($val->t6 == 1){
                $c6 = $c6 + $val->a6;
            }
            if ($val->t8 == 1){
                $c8 = $c8 + $val->a8;
            }
            if ($val->t10 == 1){
                $c10 = $c10 + $val->a10;
            }
            if ($val->t12 == 1){
                $c12 = $c12 + $val->a12;
            }
            if ($val->t14 == 1){
                $c14 = $c14 + $val->a14;
            }
            if ($val->t16 == 1){
                $c16 = $c16 + $val->a16;
            }
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 190);
        $this->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, '', 1, 0, 'J');
        
        
        $tallasremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$model->id_remision])->one();
        if ($tallasremision->txs == 1){
            $datostallas[] = 'XS';
        }
        if ($tallasremision->ts == 1){
            $datostallas[] = 'S';
        }
        if ($tallasremision->tm == 1){
            $datostallas[] = 'M';
        }
        if ($tallasremision->tl == 1){
            $datostallas[] = 'L';
        }
        if ($tallasremision->txl == 1){
            $datostallas[] = 'XL';
        }
        if ($tallasremision->txxl == 1){
            $datostallas[] = 'XXL';
        }
        if ($tallasremision->t2 == 1){
            $datostallas[] = '2';
        }
        if ($tallasremision->t4 == 1){
            $datostallas[] = '4';
        }
        if ($tallasremision->t6 == 1){
            $datostallas[] = '6';
        }
        if ($tallasremision->t8 == 1){
            $datostallas[] = '8';
        }
        if ($tallasremision->t10 == 1){
            $datostallas[] = '10';
        }
        if ($tallasremision->t12 == 1){
            $datostallas[] = '12';
        }
        if ($tallasremision->t14 == 1){
            $datostallas[] = '14';
        }
        if ($tallasremision->t16 == 1){
            $datostallas[] = '16';
        }
        
        foreach ($datostallas as $val) {
            if ($val == 'xs' or $val == 'XS') {
                $pdf->Cell($ancho, 6, $cxs, 1, 0, 'R');
            }
            if ($val == 's' or $val == 'S') {
                $pdf->Cell($ancho, 6, $cs, 1, 0, 'R');
            }
            if ($val == 'm' or $val == 'M') {
                $pdf->Cell($ancho, 6, $cm, 1, 0, 'R');
            }
            if ($val == 'l' or $val == 'L') {
                $pdf->Cell($ancho, 6, $cl, 1, 0, 'R');
            }
            if ($val == 'xl' or $val == 'XL') {
                $pdf->Cell($ancho, 6, $cxl, 1, 0, 'R');
            }
            if ($val == 'xxl' or $val == 'XXL') {
                $pdf->Cell($ancho, 6, $cxxl, 1, 0, 'R');
            }
            if ($val == '2') {
                $pdf->Cell($ancho, 6, $c2, 1, 0, 'R');
            }
            if ($val == '4') {
                $pdf->Cell($ancho, 6, $c4, 1, 0, 'R');
            }
            if ($val == '6') {
                $pdf->Cell($ancho, 6, $c6, 1, 0, 'R');
            }
            if ($val == '8') {
                $pdf->Cell($ancho, 6, $c8, 1, 0, 'R');
            }
            if ($val == '10') {
                $pdf->Cell($ancho, 6, $c10, 1, 0, 'R');
            }
            if ($val == '12') {
                $pdf->Cell($ancho, 6, $c12, 1, 0, 'R');
            }
            if ($val == '14') {
                $pdf->Cell($ancho, 6, $c14, 1, 0, 'R');
            }
            if ($val == '16') {
                $pdf->Cell($ancho, 6, $c16, 1, 0, 'R');
            }
        }
        
        
        $pdf->Cell(20, 6, ' TOTAL ', 1, 0, 'C');
        $pdf->Cell(15, 6, $total, 1, 0, 'R');
        $this->SetFont('Arial', 'B', 10);
       
             
        if($nregistros == 7){
           $pdf->SetXY(10, 202);
           $pdf->Cell(180, 6, 'FIRMAS Y RESPONSABLES DEL PROCESO', 1, 0, 'C',1);
        }else{
           $pdf->SetXY(10, 202);
           $pdf->Cell(170, 6, 'FIRMAS Y RESPONSABLES DEL PROCESO', 1, 0, 'C',1);
        }  
        $pdf->SetXY(10, 245);
        $pdf->Cell(75, 6, '______________________________________', 0, 0, 'C');
        $pdf->SetXY(120, 245);
        $pdf->Cell(75, 6, '______________________________________', 0, 0, 'C');
        $pdf->SetXY(10, 251);
        $pdf->Cell(75, 6, 'AUDITORIA CLIENTE', 0, 0, 'J');
        $pdf->SetXY(120, 251);
        $pdf->Cell(75, 6, 'PERSONAL QUIEN DESPACHA', 0, 0, 'J');
        $pdf->SetXY(120, 256);
        $pdf->Cell(75, 6, 'C.C.:', 0, 0, 'J');
        $pdf->SetXY(10, 265);
        $pdf->Cell(30, 12, 'NOTA:', 1, 0, 'J',1);
        $pdf->Cell(161, 6, 'Favor verificar que las referencias esten completas y en buenas condiciones.', 1, 0, 'J',1);
        $pdf->SetXY(40, 271);
        $pdf->Cell(161, 6, 'Despues de tres(3) dias no se aceptan devoluciones.', 1, 0, 'J',1);
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $idremision;
global $idordenproduccion;
$idremision = $model->id_remision;
$idordenproduccion = $model->idordenproduccion;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Clasificar_Segundas$model->id_remision.pdf", 'D');

exit;
