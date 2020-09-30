<?php

use inquid\pdf\FPDF;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
use app\models\Producto;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;
use app\models\Ordenproducciondetalleproceso;

class PDF extends FPDF {

    function Header() {
        $idordenproduccion = $GLOBALS['idordenproduccion'];
        $iddetalleorden = $GLOBALS['iddetalle'];
        $ordenproduccion = Ordenproduccion::findOne($idordenproduccion);
        $detalleorden = Ordenproducciondetalle::findOne($iddetalleorden);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);        
        //Logo
        $this->SetXY(53, 10);
        $this->Image('dist/images/logos/logomaquila.png', 10, 10, 40, 29);
        //Encabezado
        $this->SetFont('Arial', '', 12);
        $this->SetXY(53, 9);
        $this->Cell(150, 7, utf8_decode($config->razonsocialmatricula), 0, 0, 'C', 0);
        $this->SetXY(53, 13.5);
        $this->Cell(150, 7, utf8_decode(" NIT:" .$config->nitmatricula." - ".$config->dv), 0, 0, 'C', 0);
        $this->SetXY(53, 18);
        $this->Cell(150, 7, utf8_decode($config->direccionmatricula. " Teléfono: " .$config->telefonomatricula), 0, 0, 'C', 0);
        $this->SetXY(53, 23);
        $this->Cell(150, 7, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'C', 0);
        $this->SetXY(53, 28);
        $this->Cell(150, 7, utf8_decode($config->tipoRegimen->regimen), 0, 0, 'C', 0);
        $this->SetXY(53, 32);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(150, 7, utf8_decode("Autorización Numeración de Facturación: Res. Dian N° " .$config->resolucion->nroresolucion), 0, 0, 'C', 0);
        $this->SetXY(53, 36);
        $this->Cell(150, 7, utf8_decode("Fecha: ". date('d-m-Y', strtotime($config->resolucion->fechacreacion)). " Numeración: ". $config->resolucion->desde. " AL ". $config->resolucion->hasta), 0, 0, 'C', 0);
        $this->SetXY(53, 40);
        $this->Cell(150, 7, utf8_decode("Código Actividad: " .$config->resolucion->codigoactividad. " Descripción: ". $config->resolucion->descripcion), 0, 0, 'C', 0);
        $this->SetXY(10, 42);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //ORDEN PRODUCCION
        $this->SetXY(10, 47);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(162, 7, utf8_decode("FICHA DE OPERACIONES"), 0, 0, 'l', 0);        
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 59); //FILA 1
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($ordenproduccion->cliente->cedulanit . '-' . $ordenproduccion->cliente->dv), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("FECHA LLEGADA:"), 0, 0, 'J');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($ordenproduccion->fechallegada), 0, 0, 'J');
        $this->SetXY(10, 64); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CLIENTE:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($ordenproduccion->cliente->nombrecorto), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("FECHA ENTREGA:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($ordenproduccion->fechaentrega), 0, 0, 'J');
        $this->SetXY(10, 69); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CODIGO PROD:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($ordenproduccion->codigoproducto), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("ORDEN PRODUCCIÓN:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($ordenproduccion->ordenproduccion), 0, 0, 'J');
        $this->SetXY(10, 74); //FILA 4
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("PRODUCTO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($detalleorden->productodetalle->prendatipo->prenda.' / '.$detalleorden->productodetalle->prendatipo->talla->talla), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("TIPO ORDEN:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($ordenproduccion->tipo->tipo), 0, 0, 'J');        
        //Lineas del encabezado
        $this->Line(10, 86, 10, 269);//x1,y1,x2,y2        
        $this->Line(20, 86, 20, 269);
        $this->Line(113, 86, 113, 269);
        $this->Line(133, 86, 133, 269);
        $this->Line(153, 86, 153, 269);
        $this->Line(183, 86, 183, 269);
        $this->Line(201, 86, 201, 269);
        //$this->Line(10, 200, 201, 140); //linea horizontal inferior x1,y1,x2,y2
                
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('ID','PROCESO', utf8_decode('DUR(SEG)'),utf8_decode('TOTAL OPER'), utf8_decode('PONDERACIÓN(SEG)'), utf8_decode('TOTAL(SEG)'),  utf8_decode('MAQUINA'));
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(10,  70, 20,20, 30, 18, 30);
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

    function Body($pdf, $procesos) {
        //$detalles = Ordenproducciondetalle::find()->where(['=', 'idordenproduccion', $model->idordenproduccion])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 9);
        $items = count($procesos);
        $totalsegundos = 0;
        foreach ($procesos as $detalle) {
            $totalsegundos = $totalsegundos + $detalle->total;
            $pdf->Cell(10, 5, $detalle->iddetalleproceso, 0, 0, 'J');          
            $pdf->Cell(70, 5, utf8_decode($detalle->proceso), 0, 0, 'L');
            $pdf->Cell(20, 4, $detalle->duracion,0,0, 'R');
            $pdf->Cell(20, 4, round(60 / $detalle->duracion * 60) ,0,0, 'R');
            $pdf->Cell(30, 4, $detalle->ponderacion,0,0, 'R');
            $pdf->Cell(18, 4, number_format($detalle->total,1),0,0, 'R');
            $pdf->Cell(25, 4, $detalle->tipomaquina->descripcion, 0, 0, 'R'); 
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 269);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(71, 8, 'ITEMS: '.$items, 1, 'J');
        $pdf->SetXY(81, 269);
        $pdf->MultiCell(62, 8, 'TOTAL SEGUNDOS: '.$totalsegundos, 1, 'J');
        $pdf->SetXY(143, 269);        
        $pdf->MultiCell(58, 8, 'TOTAL MINUTOS: '.(number_format($totalsegundos / 60 ,1)), 1, 'J');
        
        
        
        
        
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $idordenproduccion;
global $iddetalle;
$idordenproduccion = $model->idordenproduccion;
$iddetalle = $iddetalleorden;
$procesos = Ordenproducciondetalleproceso::find()->Where(['=', 'iddetalleorden', $iddetalle])->orderBy('proceso asc')->all();
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $procesos);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("FichaOperaciones$iddetalleorden.pdf", 'D');

exit;
