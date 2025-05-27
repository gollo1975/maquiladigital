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
        $this->Line(10, 70, 10, 190);//x1,y1,x2,y2 
        $registros = \app\models\ClasificacionSegundas::find()->where(['=','id_remision', $remision->id_remision])->one();
        $nregistros = 0;
        if($registros->xxs == 1){
            $nregistros = 1;
        }
        if($registros->xs == 1){
            $nregistros += 1;
        }
        if($registros->s == 1){
            $nregistros += 1;
        }
         if($registros->m == 1){
            $nregistros += 1;
        }
        if($registros->l == 1){
            $nregistros += 1;
        }
        if($registros->xl == 1){
            $nregistros += 1;
        }
        if($registros->xxl == 1){
            $nregistros += 1;
        }
        if($registros->t2 == 1){
            $nregistros += 1;
        }
        if($registros->t4 == 1){
            $nregistros += 1;
        }
        if($registros->t6 == 1){
            $nregistros += 1;
        }
        if($registros->t8 == 1){
            $nregistros += 1;
        }if($registros->t10 == 1){
            $nregistros += 1;
        }
        if($registros->t12 == 1){
            $nregistros += 1;
        }
        if($registros->t14 == 1){
            $nregistros += 1;
        }
        if($registros->t16 == 1){
            $nregistros += 1;
        }
        if($registros->t18 == 1){
            $nregistros += 1;
        }
        if($registros->t20 == 1){
            $nregistros += 1;
        }
        if($registros->t22 == 1){
            $nregistros += 1;
        }
        if($registros->t28 == 1){
            $nregistros += 1;
        }
        if($registros->t30 == 1){
            $nregistros += 1;
        }
        if($registros->t31 == 1){
            $nregistros += 1;
        }
        if($registros->t32 == 1){
            $nregistros += 1;
        }
        if($registros->t33 == 1){
            $nregistros += 1;
        }
        if($registros->t34 == 1){
            $nregistros += 1;
        }
        if($registros->t36 == 1){
            $nregistros += 1;
        }
        if($registros->t38 == 1){
            $nregistros += 1;
        }
        if($registros->t40 == 1){
            $nregistros += 1;
        }
        if($registros->t42 == 1){
            $nregistros += 1;
        }
            
        
        //lineas 
        if ($nregistros == 1) {
            $this->Line(112, 78, 112, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 2) {
            $this->Line(135, 78, 135, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 3) {
            $this->Line(154, 78, 154, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 4) {
            $this->Line(169, 78, 169, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 5) {
            $this->Line(180, 78, 180, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 6) {
            $this->Line(187, 78, 187, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 7) {
            $this->Line(190, 78, 190, 190);//x1,y1,x2,y2 
        }
        if ($nregistros == 8) {
            $this->Line(190, 78, 190, 190);//x1,y1,x2,y2 
        }
      
     
        
        //Detalle factura
        $this->EncabezadoDetalles($nregistros);
    }

    function EncabezadoDetalles($nregistros) {
        $this->Ln(7);
        $idremision = $GLOBALS['idremision'];
        $tallasremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$idremision])->one();
           if ($tallasremision->xxs == 1){
                $datostallas[] = 'XXS';
            }
            if ($tallasremision->xs == 1){
                $datostallas[] = 'XS';
            }
             if ($tallasremision->s == 1){
                $datostallas[] = 'S';
            }
            if ($tallasremision->m == 1){
                $datostallas[] = 'M';
            }
            if ($tallasremision->l == 1){
                $datostallas[] = 'L';
            }
            if ($tallasremision->xl == 1){
                $datostallas[] = 'XL';
            }
             if ($tallasremision->xxl == 1){
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
            if ($tallasremision->t31 == 1){
                $datostallas[] = '31';
            }
            if ($tallasremision->t32 == 1){
                $datostallas[] = '32';
            }
            if ($tallasremision->t33 == 1){
                $datostallas[] = '33';
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
        
        if ($nregistros == 1){
            $w = array(40, 27, 35);
        }
        if ($nregistros == 2){
            $w = array(40, 25, 25, 35);
        }
        if ($nregistros == 3){
            $w = array(40, 23, 23, 23,  35);
        }
        if ($nregistros == 4){
            $w = array(40, 21, 21, 21, 21, 35);
        }
        if ($nregistros == 5){
            $w = array(40, 19, 19, 19, 19, 19, 35);
        }
        if ($nregistros == 6){
            $w = array(40, 17, 17, 17, 17, 17, 17, 35);
        }
        if ($nregistros == 7){
            $w = array(40, 15, 15, 15, 15, 15, 15, 15, 35);
        }
        if ($nregistros == 8){
            $w = array(40, 13, 13, 13, 13, 13, 13, 13,13, 35);
        }
        if ($nregistros == 9){
            $w = array(40, 11, 11, 11, 11, 11, 11, 11, 11, 11, 35);
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
        $registros = \app\models\ClasificacionSegundas::find()->where(['=','id_remision', $model->id_remision])->one();
        $nregistros = 0;
        $ancho = 0;
        if($registros->xxs == 1){
                $nregistros += 1;
            }
            if($registros->xs == 1){
                $nregistros += 1;
            }
            if($registros->s == 1){
                $nregistros += 1;
            }
             if($registros->m == 1){
                $nregistros += 1;
            }
            if($registros->l == 1){
                $nregistros += 1;
            }
            if($registros->xl == 1){
                $nregistros += 1;
            }
            if($registros->xxl == 1){
                $nregistros += 1;
            }
            if($registros->t2 == 1){
                $nregistros += 1;
            }
            if($registros->t4 == 1){
                $nregistros += 1;
            }
            if($registros->t6 == 1){
                $nregistros += 1;
            }
            if($registros->t8 == 1){
                $nregistros += 1;
            }
            if($registros->t10 == 1){
                $nregistros += 1;
            }
            if($registros->t12 == 1){
                $nregistros += 1;
            }
            if($registros->t14 == 1){
                $nregistros += 1;
            }
            if($registros->t16 == 1){
                $nregistros += 1;
            }
            if($registros->t18 == 1){
                $nregistros += 1;
            }
            if($registros->t20 == 1){
                $nregistros += 1;
            }
            if($registros->t22 == 1){
                $nregistros += 1;
            }
            if($registros->t28 == 1){
                $nregistros += 1;
            }
            if($registros->t30 == 1){
                $nregistros += 1;
            }
            if($registros->t31 == 1){
                $nregistros += 1;
            }
             if($registros->t32 == 1){
                $nregistros += 1;
            }
            if($registros->t33 == 1){
                $nregistros += 1;
            }
             if($registros->t34 == 1){
                $nregistros += 1;
            }
             if($registros->t36 == 1){
                $nregistros += 1;
            }
             if($registros->t38 == 1){
                $nregistros += 1;
            }
             if($registros->t40 == 1){
                $nregistros += 1;
            }
            if($registros->t42 == 1){
                $nregistros += 1;
            }
            
        
       
        if ($nregistros == 1) {
            $ancho = 27;
        }
        if ($nregistros == 2) {
            $ancho = 25;
        }
        if ($nregistros == 3) {
            $ancho = 23;
        }
        if ($nregistros == 4) {
            $ancho = 21;
        }
        if ($nregistros == 5) {
            $ancho = 19;
        }
        if ($nregistros == 6) {
            $ancho = 17;
        }
        if ($nregistros == 7) {
            $ancho = 15;
        }
        if ($nregistros == 8) {
            $ancho = 13;
        }
        if ($nregistros == 9) {
            $ancho = 11;
        }
       
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'b', 10);
        $items = count($detalles);
        $txs = 0; $ts = 0; $tm = 0; $tl = 0; $txl = 0; $txxl = 0; $txxs = 0;
        $t2 = 0;$t4 = 0;$t6 = 0;$t8 = 0;$t10 = 0;$t12 = 0;$t14 = 0;$t16 = 0;
        $t18 = 0;$t20 = 0;$t22 = 0;$t28 = 0;$t30 = 0; $t31 = 0; $t32 = 0; $t33 = 0; $t34 = 0;$t36 = 0;$t38 = 0;
        $t40 = 0;$t42 = 0;
        foreach ($detalles as $detalle) {
            $txxs = $txxs + $detalle->xxs;
            $txs = $txs + $detalle->xs;
            $ts = $ts + $detalle->s;
            $tm = $tm + $detalle->m;
            $tl = $tl + $detalle->l;
            $txl = $txl + $detalle->xl;
            $txxl = $txxl + $detalle->xxl;
            
            $pdf->Cell(40, 4.5, utf8_decode($detalle->tipo->concepto), 1, 0, 'J');
            if ($detalle->xxs == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->txxs, 1, 0, 'R');
            }
            if ($detalle->xs == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->txs, 1, 0, 'R');
            }
            if ($detalle->s == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->ts, 1, 0, 'R');
            }
            if ($detalle->m == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->tm, 1, 0, 'R');
            }
            if ($detalle->l == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->tl, 1, 0, 'R');
            }
            if ($detalle->xl == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->txl, 1, 0, 'R');
            }
            if ($detalle->xxl == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->txxl, 1, 0, 'R');
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
            if ($detalle->t18 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a18, 1, 0, 'R');
            }
            if ($detalle->t20 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a20, 1, 0, 'R');
            }
            if ($detalle->t22 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a22, 1, 0, 'R');
            }
            if ($detalle->a28 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a28, 1, 0, 'R');
            }
            if ($detalle->t30 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a30, 1, 0, 'R');
            }
            if ($detalle->t31 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a31, 1, 0, 'R');
            }
            if ($detalle->t32 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a32, 1, 0, 'R');
            }
            if ($detalle->t33 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a33, 1, 0, 'R');
            }
            if ($detalle->t34 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a34, 1, 0, 'R');
            }
            if ($detalle->t36 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a36, 1, 0, 'R');
            }
            if ($detalle->t38 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a38, 1, 0, 'R');
            }
            if ($detalle->t40 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a40, 1, 0, 'R');
            }
            if ($detalle->t42 == 1) {
                $pdf->Cell($ancho, 4.5, $detalle->a42, 1, 0, 'R');
            }
            
            $pdf->Cell(35, 4.5, $detalle->unidades, 1, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $cxxs = 0; $cxs = 0; $cs = 0; $cm = 0; $cl = 0; $cxl = 0; $cxxl = 0; $c2 = 0; $c4 = 0; $c6 = 0; $c8 = 0; $c10 = 0; $c12 = 0; $c14 = 0; $c16 = 0; $c18 = 0;
        $c20 = 0;$c22 = 0;$c28 = 0;$c30 = 0; $c31 = 0; $c32 = 0; $c33 = 0; $c34 = 0;$c36 = 0;$c38 = 0;$c40 = 0;$c42 = 0;

        $cantidadesremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$model->id_remision])->all();
        $total = 0;
        foreach ($cantidadesremision as $val){
            $total += $val->unidades;
            if ($val->xxs == 1){
                $cxxs += $val->txxs;
            }
            if ($val->xs == 1){
                $cxs += $val->txs;
            }
            if ($val->s == 1){
                $cs += $val->ts;
            }
            if ($val->m == 1){
                $cm += $val->tm;
            }
            if ($val->l == 1){
                $cl += $val->tl;
            }
            if ($val->xl == 1){
                $cxl += $val->txl;
            }
             if ($val->xxl == 1){
                $cxxl += $val->txxl;
            }
            if ($val->t2 == 1){
                $c2 += $val->a2;
            }
            if ($val->t4 == 1){
                $c4 += $val->a4;
            }
            if ($val->t6 == 1){
                $c6 += $val->a6;
            }
            if ($val->t8 == 1){
                $c8 += $val->a8;
            }
            if ($val->t10 == 1){
                $c10 += $val->a10;
            }
            if ($val->t12 == 1){
                $c12 += $val->a12;
            }
            if ($val->t14 == 1){
                $c14 += $val->a14;
            }
            if ($val->t16 == 1){
                $c16 += $val->a16;
            }
            if ($val->t18 == 1){
                $c18 += $val->a18;
            }
            if ($val->t20 == 1){
                $c20 += $val->a20;
            }
            if ($val->t22 == 1){
                $c22 += $val->a22;
            }
            if ($val->t28 == 1){
                $c28 += $val->a28;
            }
            if ($val->t30 == 1){
                $c30 += $val->a30;
            }
             if ($val->t31 == 1){
                $c31 += $val->a31;
            }
            if ($val->t32 == 1){
                $c32 += $val->a32;
            }
             if ($val->t33 == 1){
                $c33 += $val->a33;
            }
            if ($val->t34== 1){
                $c34 += $val->a34;
            }
            if ($val->t36 == 1){
                $c36 += $val->a36;
            }
            if ($val->t38 == 1){
                $c38 += $val->a38;
            }
            if ($val->t40 == 1){
                $c40 += $val->a40;
            }
            if ($val->t42 == 1){
                $c42 += $val->a42;
            }
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 190);
        $this->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, '', 1, 0, 'J');
        
        
        $tallasremision = \app\models\ClasificacionSegundas::find()->where(['=','id_remision',$model->id_remision])->one();
        if ($tallasremision->xxs == 1){
            $datostallas[] = 'XXS';
        }
        if ($tallasremision->xs == 1){
            $datostallas[] = 'XS';
        }
        if ($tallasremision->s == 1){
            $datostallas[] = 'S';
        }
        if ($tallasremision->m == 1){
            $datostallas[] = 'M';
        }
        if ($tallasremision->l == 1){
            $datostallas[] = 'L';
        }
        if ($tallasremision->xl == 1){
            $datostallas[] = 'XL';
        }
        if ($tallasremision->xxl == 1){
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
        if ($tallasremision->t31 == 1){
            $datostallas[] = '31';
        }
        if ($tallasremision->t32 == 1){
            $datostallas[] = '32';
        }
        if ($tallasremision->t33 == 1){
            $datostallas[] = '33';
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
            if ($val == '31') {
                $pdf->Cell($ancho, 6, $c31, 1, 0, 'R');
            }
            if ($val == '32') {
                $pdf->Cell($ancho, 6, $c32, 1, 0, 'R');
            }
            if ($val == '33') {
                $pdf->Cell($ancho, 6, $c33, 1, 0, 'R');
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
            if ($val == '38') {
                $pdf->Cell($ancho, 6, $c40, 1, 0, 'R');
            }
            if ($val == '42') {
                $pdf->Cell($ancho, 6, $c42, 1, 0, 'R');
            }
        }
        if($nregistros == 1){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }   
        if($nregistros == 2){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }   
        if($nregistros == 3){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }   
        if($nregistros == 4){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }
        if($nregistros == 5){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }
        if($nregistros == 6){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        } 
        if($nregistros == 7){
            $pdf->Cell(17, 6, ' TOTAL ', 1, 0, 'C');
            $pdf->Cell(18, 6, $total, 1, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
        }   
                                                                                                                                                                        
       
             
        if($nregistros == 7){
           $pdf->SetXY(10, 202);
           $pdf->Cell(180, 6, 'FIRMAS Y RESPONSABLES DEL PROCESO', 1, 0, 'C',1);
        }else{
           $pdf->SetXY(10, 202);
           $pdf->Cell(180, 6, 'FIRMAS Y RESPONSABLES DEL PROCESO', 1, 0, 'C',1);
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
