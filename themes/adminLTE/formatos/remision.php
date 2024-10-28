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
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
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
        $this->Cell(162, 7, utf8_decode("REMISIÓN DE ENTREGA"), 0, 0, 'l', 0);
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
        $this->Cell(40, 5, utf8_decode($remision->fecha_entrega), 0, 0, 'J');
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
        $this->Line(10, 82, 10, 190);//x1,y1,x2,y2 
        //oc
        $this->Line(40, 82, 40, 190);//x1,y1,x2,y2   
        //tula
        $this->Line(66, 82, 66, 190);//x1,y1,x2,y2
        $ordendetalles = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$remision->idordenproduccion])->all();
        $nregistros = count($ordendetalles);
        if ($nregistros == 1) {        
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2                                                   
        }
        if ($nregistros == 2) {        
        $this->Line(81, 92, 81, 190);//x1,y1,x2,y2           
        $this->Line(185, 92, 185, 190);//x1,y1,x2,y2                                
        }
        if ($nregistros == 3) {        
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2           
        $this->Line(104, 82, 104, 190);//x1,y1,x2,y2                        
        $this->Line(127, 82, 127, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 4) {        
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2           
        $this->Line(98.5, 82, 98.5, 190);//x1,y1,x2,y2        
        $this->Line(116, 82, 116, 190);//x1,y1,x2,y2        
        $this->Line(133.5, 82, 133.5, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 5) {
        //xs
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2   
        //s
        $this->Line(95, 82, 95, 190);//x1,y1,x2,y2
        //m
        $this->Line(109, 82, 109, 190);//x1,y1,x2,y2
        //l
        $this->Line(123, 82, 123, 190);//x1,y1,x2,y2
        //xl
        $this->Line(137, 82, 137, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 6) {        
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2           
        $this->Line(92.6, 82, 92.6, 190);//x1,y1,x2,y2        
        $this->Line(104.2, 82, 104.2, 190);//x1,y1,x2,y2        
        $this->Line(115.8, 82, 115.8, 190);//x1,y1,x2,y2
        $this->Line(127.4, 82, 127.4, 190);//x1,y1,x2,y2        
        $this->Line(139, 82, 139, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 7) {        
        $this->Line(81, 82, 81, 190);//x1,y1,x2,y2           
        $this->Line(91, 82, 91, 190);//x1,y1,x2,y2        
        $this->Line(101, 82, 101, 190);//x1,y1,x2,y2        
        $this->Line(111, 82, 111, 190);//x1,y1,x2,y2
        $this->Line(121, 82, 121, 190);//x1,y1,x2,y2        
        $this->Line(131, 82, 131, 190);//x1,y1,x2,y2
        $this->Line(141, 82, 141, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 8) {        
        $this->Line(81, 92, 81, 190);//x1,y1,x2,y2           
        $this->Line(89.75, 92, 89.75, 190);//x1,y1,x2,y2        
        $this->Line(98.5, 92, 98.5, 190);//x1,y1,x2,y2        
        $this->Line(107.25, 92, 107.25, 190);//x1,y1,x2,y2
        $this->Line(116, 92, 116, 190);//x1,y1,x2,y2        
        $this->Line(124.75, 92, 124.75, 190);//x1,y1,x2,y2
        $this->Line(133.5, 92, 133.5, 190);//x1,y1,x2,y2
        $this->Line(142.25, 92, 142.25, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 9) {        
        $this->Line(81, 92, 81, 190);//x1,y1,x2,y2           
        $this->Line(88.777, 92, 88.777, 190);//x1,y1,x2,y2        
        $this->Line(96.554, 92, 96.554, 190);//x1,y1,x2,y2        
        $this->Line(104.331, 92, 104.331, 190);//x1,y1,x2,y2
        $this->Line(112.108, 92, 112.108, 190);//x1,y1,x2,y2        
        $this->Line(119.885, 92, 124.75, 190);//x1,y1,x2,y2
        $this->Line(127.662, 92, 127.662, 190);//x1,y1,x2,y2
        $this->Line(135.439, 92, 135.439, 190);//x1,y1,x2,y2
        $this->Line(143.216, 92, 143.216, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 10) {        
        $this->Line(81, 92, 81, 190);//x1,y1,x2,y2           
        $this->Line(88, 92, 88, 190);//x1,y1,x2,y2        
        $this->Line(95, 92, 95, 190);//x1,y1,x2,y2        
        $this->Line(102, 92, 102, 190);//x1,y1,x2,y2
        $this->Line(109, 92, 109, 190);//x1,y1,x2,y2        
        $this->Line(116, 92, 116, 190);//x1,y1,x2,y2
        $this->Line(123, 92, 123, 190);//x1,y1,x2,y2
        $this->Line(130, 92, 130, 190);//x1,y1,x2,y2
        $this->Line(137, 92, 137, 190);//x1,y1,x2,y2
        $this->Line(144, 92, 144, 190);//x1,y1,x2,y2
        }
        if ($nregistros == 3){
            //estado
            $this->Line(150, 82, 150, 190);//x1,y1,x2,y2   
            //unidad por tula
            $this->Line(179, 82, 179, 190);//x1,y1,x2,y2   
            //linea final vertical
            $this->Line(200, 82, 200, 190);//x1,y1,x2,y2
            //   
            //linea final 
            $this->Line(10, 190, 201, 190); //linea horizontal inferior x1,y1,x2,y2

            //Linea de la entrega
            $this->Line(10, 232, 10, 265); //linea vertical
            $this->Line(201, 232, 201, 265); //linea vertical
        }else{
            //estado
            $this->Line(151, 82, 151, 190);//x1,y1,x2,y2   
            //unidad por tula
            $this->Line(180, 82, 180, 190);//x1,y1,x2,y2   
            //linea final vertical
            $this->Line(201, 82, 201, 190);//x1,y1,x2,y2
            //   
            //linea final 
            $this->Line(10, 190, 201, 190); //linea horizontal inferior x1,y1,x2,y2

            //Linea de la entrega
            $this->Line(10, 232, 10, 265); //linea vertical
            $this->Line(201, 232, 201, 265); //linea vertical
        }
        //Detalle factura
        $this->EncabezadoDetalles($nregistros);
    }

    function EncabezadoDetalles($nregistros) {
        $this->Ln(7);
        $idremision = $GLOBALS['idremision'];
        $tallasremision = Remisiondetalle::find()->where(['=','id_remision',$idremision])->one();
         if ($tallasremision->txxs == 1){
                $datostallas[] = 'XXS';
        }
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
        if ($tallasremision->t18 == 1){
            $datostallas[] = '18';
        }
        if ($tallasremision->t20 == 1){
            $datostallas[] = '20';
        }
        if ($tallasremision->t22 == 1){
            $datostallas[] = '22';
        }
        if ($tallasremision->t28 == 1){
            $datostallas[] = '28';
        }
        if ($tallasremision->t30 == 1){
            $datostallas[] = '30';
        }
        if ($tallasremision->t32 == 1){
            $datostallas[] = '32';
        }
        if ($tallasremision->t34 == 1){
            $datostallas[] = '34';
        }
        if ($tallasremision->t36 == 1){
            $datostallas[] = '36';
        }
        if ($tallasremision->t38 == 1){
            $datostallas[] = '38';
        }
        if ($tallasremision->t40 == 1){
            $datostallas[] = '40';
        }
        if ($tallasremision->t42 == 1){
            $datostallas[] = '42';
        }
        $array1 = array('COLOR', 'OC', 'TULA');        
        $array2 = $datostallas;
        $array3 = array('ESTADO','UNID X TULA');
        $array4 = array_merge($array1, $array2, $array3);
        $header = $array4;
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        if ($nregistros == 1){
            $w = array(30, 26, 15, 70, 29, 21);
        }
        if ($nregistros == 2){
            $w = array(30, 26, 15, 35, 35, 29, 21);
        }
        if ($nregistros == 3){
            $w = array(30, 26, 15, 23, 23, 23, 29, 21);
        }
        if ($nregistros == 4){
            $w = array(30, 26, 15, 17.5, 17.5, 17.5, 17.5, 29, 21);
        }
        if ($nregistros == 5){
            $w = array(30, 26, 15, 14, 14, 14, 14, 14, 29, 21);
        }
        if ($nregistros == 6){
            $w = array(30, 26, 15, 11.6, 11.6, 11.6, 11.6, 11.6, 11.6, 29, 21);
        }
        if ($nregistros == 7){
            $w = array(30, 26, 15, 10, 10, 10, 10, 10, 10, 10, 29, 21);
        }
        if ($nregistros == 8){
            $w = array(30, 26, 15, 8.75, 8.75, 8.75, 8.75, 8.75, 8.75, 8.75, 8.75, 29, 21);
        }
        if ($nregistros == 9){
            $w = array(30, 26, 15, 7.777, 7.777, 7.777, 7.777, 7.777, 7.777, 7.777, 7.777, 7.777, 29, 21);
        }
        if ($nregistros == 10){
            $w = array(30, 26, 15, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 29, 21);
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
        $detalles = Remisiondetalle::find()->where(['=', 'id_remision', $model->id_remision])->all();
        $ordendetalles = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$model->idordenproduccion])->all();
        $nregistros = count($ordendetalles);
        if ($nregistros == 1) {
            $ancho = 70;
        }
        if ($nregistros == 2) {
            $ancho = 35;
        }
        if ($nregistros == 3) {
            $ancho = 23;
        }
        if ($nregistros == 4) {
            $ancho = 17.5;
        }
        if ($nregistros == 5) {
            $ancho = 14;
        }
        if ($nregistros == 6) {
            $ancho = 11.6;
        }
        if ($nregistros == 7) {
            $ancho = 10;
        }
        if ($nregistros == 8) {
            $ancho = 8.75;
        }
        if ($nregistros == 9) {
            $ancho = 7.777;
        }
        if ($nregistros == 10) {
            $ancho = 7;
        }
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'b', 10);
        $items = count($detalles);
        $sumaLineas = 0;
        $txxs = 0;$txs = 0; $ts = 0; $tm = 0; $tl = 0; $txl = 0; $txxl = 0;
        $t2 = 0;$t4 = 0;$t6 = 0;$t8 = 0;$t10 = 0;$t12 = 0;$t14 = 0;$t16 = 0;$t18 = 0;$t20 = 0;
        $t22 = 0;$t28 = 0;$t30 = 0;$t32 = 0;$t34 = 0;$t36 = 0;$t38 = 0;$t40 = 0;$t42 = 0;
        foreach ($detalles as $detalle) {
            $txxs = $txxs + $detalle->xxs;
            $txs = $txs + $detalle->xs;
            $ts = $ts + $detalle->s;
            $tm = $tm + $detalle->m;
            $tl = $tl + $detalle->l;
            $txl = $txl + $detalle->xl;
            $txxl = $txxl + $detalle->xxl;
            $t2 += $detalle->c2;
            $t4 += $detalle->c4;
            $t6 += $detalle->c6;
            $t8 += $detalle->c8;
            $t10 += $detalle->c10;
            $t12 += $detalle->c12;
            $t14 += $detalle->c14;
            $t16 += $detalle->c16;
            $t18 += $detalle->c18;
            $t20 += $detalle->c20;
            $t22 += $detalle->c22;
            $t28 += $detalle->c28;
            $t30 += $detalle->c30;
            $t32 += $detalle->c32;
            $t34 += $detalle->c34;
            $t36 += $detalle->c36;
            $t38 += $detalle->c38;
            $t40 += $detalle->c40;
            $t42 += $detalle->c42;
            if($detalle->oc == 0){
                $oc = 'Colombia';
            }else{
                $oc = 'Exportacion';
            }
            if($detalle->estado == 0){
                $estado = 'Primera';
            }else{
                $estado = 'Segunda';
            }
            $pdf->Cell(30, 4.5,  utf8_decode(mb_substr($detalle->color,0, 12)), 1, 0, 'J');
            if ($detalle->oc == 1){
                $pdf->Cell(26, 4.5, $oc, 1, 0, 'J',1);   
            }else{
                $pdf->Cell(26, 4.5, $oc, 1, 0, 'J',0);
            }            
            $pdf->Cell(15, 4.5, $detalle->tula, 1, 0, 'R');
            if ($detalle->oc == 1 || $detalle->estado == 1){
                if ($detalle->txxs == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xxs, 1, 0, 'R',1);
                }
                if ($detalle->txs == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xs, 1, 0, 'R',1);
                }
                if ($detalle->ts == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->s, 1, 0, 'R',1);
                }
                if ($detalle->tm == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->m, 1, 0, 'R',1);
                }
                if ($detalle->tl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->l, 1, 0, 'R',1);
                }
                if ($detalle->txl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xl, 1, 0, 'R',1);
                }
                 if ($detalle->txxl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xxl, 1, 0, 'R',1);
                }
                if ($detalle->t2 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c2'], 1, 0, 'R',1);
                }
                if ($detalle->t4 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c4'], 1, 0, 'R',1);
                }
                if ($detalle->t6 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c6'], 1, 0, 'R',1);
                }
                if ($detalle->t8 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c8'], 1, 0, 'R',1);
                }                
                if ($detalle->t10 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c10'], 1, 0, 'R',1);
                }
                if ($detalle->t12 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c12'], 1, 0, 'R',1);
                }
                if ($detalle->t14 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c14'], 1, 0, 'R',1);
                }
                if ($detalle->t16 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c16'], 1, 0, 'R',1);
                }
                if ($detalle->t18 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c18'], 1, 0, 'R',1);
                }
                if ($detalle->t20 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c20'], 1, 0, 'R',1);
                }
                if ($detalle->t22 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c22'], 1, 0, 'R',1);
                }                
                if ($detalle->t28 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c28'], 1, 0, 'R',1);
                }
                if ($detalle->t30 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c30'], 1, 0, 'R',1);
                }
                if ($detalle->t32 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c32'], 1, 0, 'R',1);
                }
                if ($detalle->t34 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c34'], 1, 0, 'R',1);
                }
                if ($detalle->t36 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c36'], 1, 0, 'R',1);
                }
                if ($detalle->t38 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c38'], 1, 0, 'R',1);
                }
                if ($detalle->t40 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c40'], 1, 0, 'R',1);
                }
                if ($detalle->t42 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c42'], 1, 0, 'R',1);
                }
                
                $pdf->Cell(29, 4.5, $estado, 1, 0, 'C',1);
            }else{
                 if ($detalle->txxs == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xxs, 1, 0, 'R', 0);
                }
                if ($detalle->txs == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xs, 1, 0, 'R', 0);
                }
                if ($detalle->ts == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->s, 1, 0, 'R', 0);
                }
                if ($detalle->tm == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->m, 1, 0, 'R', 0);
                }
                if ($detalle->tl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->l, 1, 0, 'R', 0);
                }
                if ($detalle->txl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xl, 1, 0, 'R', 0);
                }
                 if ($detalle->txxl == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle->xxl, 1, 0, 'R', 0);
                }
                if ($detalle->t2 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c2'], 1, 0, 'R', 0);
                }
                if ($detalle->t4 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c4'], 1, 0, 'R', 0);
                }
                if ($detalle->t6 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c6'], 1, 0, 'R', 0);
                }
                if ($detalle->t8 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c8'], 1, 0, 'R', 0);
                }                
                if ($detalle->t10 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c10'], 1, 0, 'R', 0);
                }
                if ($detalle->t12 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c12'], 1, 0, 'R', 0);
                }
                if ($detalle->t14 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c14'], 1, 0, 'R', 0);
                }
                if ($detalle->t16 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c16'], 1, 0, 'R', 0);
                }
                if ($detalle->t18 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c18'], 1, 0, 'R', 0);
                }
                if ($detalle->t20 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c20'], 1, 0, 'R', 0);
                }
                if ($detalle->t22 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c22'], 1, 0, 'R', 0);
                }                
                if ($detalle->t28 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c28'], 1, 0, 'R', 0);
                }
                if ($detalle->t30 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c30'], 1, 0, 'R', 0);
                }
                if ($detalle->t32 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c32'], 1, 0, 'R', 0);
                }
                if ($detalle->t34 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c34'], 1, 0, 'R', 0);
                }
                if ($detalle->t36 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c36'], 1, 0, 'R', 0);
                }
                if ($detalle->t38 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c38'], 1, 0, 'R', 0);
                }
                if ($detalle->t40 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c40'], 1, 0, 'R', 0);
                }
                if ($detalle->t42 == 1) {
                    $pdf->Cell($ancho, 4.5, $detalle['c42'], 1, 0, 'R', 0);
                }
                
                $pdf->Cell(29, 4.5, $estado, 1, 0, 'C');
            }                        
            $pdf->Cell(21, 4.5, $detalle->unidades, 1, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
            $sumaLineas += 1;
           
        }
        $cxxs = 0;$cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $ct = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;
        $c20 = 0; $c22 = 0; $c28 = 0; $c30 = 0; $c32 = 0; $c34 = 0; $c36 = 0; $c38 = 0;$c40 = 0; $c42 = 0;
        $cantidadesremision = Remisiondetalle::find()->where(['=','id_remision',$model->id_remision])->all();
        foreach ($cantidadesremision as $val){
            if ($val->txxs == 1){
                $cxxs = $cxxs + $val->xxs;
            }
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
                $c2 += $val['c2'];
            }
            if ($val->t4 == 1){
                $c4 += $val['c4'];
            }
            if ($val->t6 == 1){
                $c6 += $val['c6'];
            }
            if ($val->t8 == 1){
                $c8 += $val['c8'];
            }
            if ($val->t10 == 1){
                $c10 += $val['c10'];
            }
            if ($val->t12 == 1){
                $c12 += $val['c12'];
            }
            if ($val->t14 == 1){
                $c14 += $val['c14'];
            }
            if ($val->t16 == 1){
                $c16 += $val['c16'];
            }
            if ($val->t18 == 1){
                $c18 += $val['c18'];
            }
            if ($val->t20 == 1){
                $c20 = $c20 + $val['c20'];
            }
            if ($val->t22 == 1){
                $c22 = $c22 + $val['c22'];
            }
            if ($val->t28 == 1){
                $c28 = $c28 + $val['c28'];
            }
            if ($val->t30 == 1){
                $c30 = $c30 + $val['c30'];
            }
            if ($val->t32 == 1){
                $c32 = $c32 +$val['c32'];
            }
            if ($val->t34 == 1){
                $c34 = $c34 + $val['c34'];
            }
            if ($val->t36 == 1){
                $c36 = $c36 + $val['c36'];
            }
            if ($val->t38 == 1){
                $c38 = $c38 + $val['c38'];
            }
            if ($val->t40 == 1){
                $c40 = $c40 + $val['c40'];
            }
            if ($val->t42 == 1){
                $c42 = $c42 + $val['c42'];
            }
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 193);
        $this->SetFont('Arial', 'B', 10);
        $pdf->Cell(56, 6, 'CANT CONFECCION', 1, 0, 'J');
        $pdf->Cell(15, 6, '', 1, 0, 'R');
            
        $tallasremision = Remisiondetalle::find()->where(['=','id_remision',$model->id_remision])->one();
        if ($tallasremision->txxs == 1){
            $datostallas[] = 'XXS';
        }
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
        if ($tallasremision->t18 == 1){
            $datostallas[] = '18';
        }
        if ($tallasremision->t20 == 1){
            $datostallas[] = '20';
        }
        if ($tallasremision->t22 == 1){
            $datostallas[] = '22';
        }
        if ($tallasremision->t28 == 1){
            $datostallas[] = '28';
        }
        if ($tallasremision->t30 == 1){
            $datostallas[] = '30';
        }
        if ($tallasremision->t32 == 1){
            $datostallas[] = '32';
        }
        if ($tallasremision->t34 == 1){
            $datostallas[] = '34';
        }
        if ($tallasremision->t36 == 1){
            $datostallas[] = '36';
        }
        if ($tallasremision->t38 == 1){
            $datostallas[] = '38';
        }
        if ($tallasremision->t40 == 1){
            $datostallas[] = '40';
        }
        if ($tallasremision->t42 == 1){
            $datostallas[] = '42';
        }
        
        foreach ($datostallas as $val) {
            if ($val == 'xxs' or $val == 'XXS') {
                $pdf->Cell($ancho, 6, $cxxs, 1, 0, 'R');
            }
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
            if ($val == '18') {
                $pdf->Cell($ancho, 6, $c18, 1, 0, 'R');
            }
            if ($val == '20') {
                $pdf->Cell($ancho, 6, $c20, 1, 0, 'R');
            }
            if ($val == '22') {
                $pdf->Cell($ancho, 6, $c22, 1, 0, 'R');
            }
            if ($val == '28') {
                $pdf->Cell($ancho, 6, $c28, 1, 0, 'R');
            }
            if ($val == '30') {
                $pdf->Cell($ancho, 6, $c30, 1, 0, 'R');
            }
            if ($val == '32') {
                $pdf->Cell($ancho, 6, $c32, 1, 0, 'R');
            }
            if ($val == '34') {
                $pdf->Cell($ancho, 6, $c34, 1, 0, 'R');
            }
            if ($val == '36') {
                $pdf->Cell($ancho, 6, $c36, 1, 0, 'R');
            }
            if ($val == '38') {
                $pdf->Cell($ancho, 6, $c38, 1, 0, 'R');
            }
             if ($val == '40') {
                $pdf->Cell($ancho, 6, $c40, 1, 0, 'R');
            }
            if ($val == '42') {
                $pdf->Cell($ancho, 6, $c42, 1, 0, 'R');
            }
        }
        
        
        $total = 0;
        $pdf->Cell(29, 12, ' T. Despachadas ', 1, 0, 'C');
        $pdf->Cell(21, 12, $model->total_despachadas, 1, 0, 'R');
        $pdf->SetXY(10, 199);
        $this->SetFont('Arial', 'B', 10);
        $pdf->Cell(56, 6, 'CANT CLIENTE', 1, 0, 'J');
        $pdf->Cell(15, 6, '', 1, 0, 'R');        
        
        foreach ($datostallas as $val) {
            $ordendetalle = Ordenproducciondetalle::find()->where(['=','idordenproduccion',$model->idordenproduccion])->all();
            foreach ($ordendetalle as $val2){
                if ($val == $val2->productodetalle->prendatipo->talla->talla){
                    $total = $val2->cantidad;                                
                }
            }
            $pdf->Cell($ancho, 6, $total, 1, 0, 'R');
        }
            
        /*$pdf->Cell($ancho, 6, $cxs, 1, 0, 'R');
        $pdf->Cell($ancho, 6, $cs, 1, 0, 'R');
        $pdf->Cell($ancho, 6, $cm, 1, 0, 'R');
        $pdf->Cell($ancho, 6, $cl, 1, 0, 'R');
        $pdf->Cell($ancho, 6, $cxl, 1, 0, 'R');*/
        
        $pdf->SetXY(10, 205);
        $pdf->Cell(191, 6, 'RESUMEN DE LA ENTREGA', 1, 0, 'C',1);
        $pdf->SetXY(10, 211);
        $pdf->Cell(56, 6, 'TOTAL TULAS', 1, 0, 'J');
        $pdf->Cell(15, 6, $model->total_tulas, 1, 0, 'R');
        $pdf->Cell(14, 6, '', 1, 0, 'R');
        $pdf->Cell(56, 6, 'TOTAL COLOMBIA', 1, 0, 'J');
        $pdf->Cell(29, 6, $model->total_colombia, 1, 0, 'R');
        $pdf->Cell(21, 6, '', 1, 0, 'R');
        $pdf->SetXY(10, 217);
        $pdf->Cell(56, 6, 'TOTAL EXPORTACION', 1, 0, 'J');
        $pdf->Cell(15, 6, $model->total_exportacion, 1, 0, 'R');
        $pdf->Cell(14, 6, '', 1, 0, 'R');
        $pdf->Cell(56, 6, 'TOTAL CONFECCION', 1, 0, 'J');
        $pdf->Cell(29, 6, $model->total_confeccion, 1, 0, 'R');
        $pdf->Cell(21, 6, '', 1, 0, 'R');
        $pdf->SetXY(10, 223);
        $pdf->Cell(56, 6, 'TOTAL SEGUNDAS', 1, 0, 'J');
        $pdf->Cell(15, 6, $model->totalsegundas, 1, 0, 'R');
        $pdf->Cell(14, 6, '', 1, 0, 'R');
        $pdf->Cell(56, 6, '', 1, 0, 'J');
        $pdf->Cell(29, 6, '', 1, 0, 'R');
        $pdf->Cell(21, 6, '', 1, 0, 'R');
        $pdf->SetXY(10, 229);
        $pdf->Cell(191, 6, 'DATOS DE ENTREGA DE LA PRODUCCION', 1, 0, 'C',1);
        $pdf->SetXY(10, 245);
        $pdf->Cell(75, 6, '______________________________________', 0, 0, 'C');
        $pdf->SetXY(120, 245);
        $pdf->Cell(75, 6, '______________________________________', 0, 0, 'C');
        $pdf->SetXY(10, 251);
        $pdf->Cell(75, 6, $model->nombre_auditor, 0, 0, 'L');
        $pdf->SetXY(10, 256);
        $pdf->Cell(75, 6, 'AUDITOR CLIENTE', 0, 0, 'J');
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
if($model->cerrar_remision == 1){
    $pdf->SetFont('Arial','',15);
    $pdf->Cell(10,20);
    $pdf->Image('dist/images/logos/logoauditoria.png' , 50 ,89.7, 110 , 100,'PNG');
}    
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Remision$model->id_remision.pdf", 'D');

exit;
