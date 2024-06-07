<?php

use inquid\pdf\FPDF;
use app\models\SalidaEntradaProduccion;
use app\models\SalidaEntradaProduccionDetalle;
use app\models\Producto;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_novedad = $GLOBALS['id_novedad'];
        $novedad = \app\models\NovedadOrdenProduccion::findOne($id_novedad);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);        
        //Logo
        //Logo
        $this->SetXY(43, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(70, 9);
        $this->SetFont('Arial', '', 10);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("EMPRESA:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(70, 13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("NIT:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("DIRECCION:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("TELEFONO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(70, 25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("MUNICIPIO:"), 0, 0, 'l', 1);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 1);
        $this->SetXY(40, 5);
        //FIN
       $this->SetXY(10, 29);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
         $this->SetXY(10, 30);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        //Programación Nomina
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(10, 36);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(162, 7, utf8_decode("NOVEDADES (PRODUCCION)"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($novedad->id_novedad, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
       // $this->SetFillColor(300, 300, 300);
        //INICIO
        $this->SetXY(10, 44); //FILA 1
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("NIT:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($novedad->ordenproduccion->cliente->cedulanit . '-' . $novedad->ordenproduccion->cliente->dv), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("FECHA PROCESO:"), 0, 0, 'J');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($novedad->fecha_proceso), 0, 0, 'J');
        //FIN
        $this->SetXY(10, 48); //FILA 2
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("CLIENTE:"), 0, 0, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($novedad->ordenproduccion->cliente->nombrecorto), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("OP CLIENTE:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($novedad->ordenproduccion->ordenproduccion), 0, 0, 'J');
        //FIN
        $this->SetXY(10, 52); //FILA 3
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(24, 5, utf8_decode("USUARIO:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, utf8_decode($novedad->usuariosistema), 0, 0, 'J');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(33, 5, utf8_decode("OP INTERNA:"), 0, 0, 'c');
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($novedad->idordenproduccion), 0, 0, 'J');
       
        //Lineas del encabezado
        $this->Line(10, 64, 10, 200);//x1,y1,x2,y2        
        $this->Line(200, 64, 200, 200);
        
        $this->Line(10, 200, 200, 200); //linea horizontal inferior x1,y1,x2,y2
        
      /*  //Linea de las observacines
        $this->Line(10, 148, 10, 164); //linea vertical
        $this->Line(10, 164, 200, 164); //linea horizontal inferior x1,y1,x2,y2*/
        //Detalle factura
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('OBSERVACION');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(190);
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
         
        
       
        $pdf->SetXY(10, 66);
        $this->SetFont('Arial', '', 9);
        $pdf->MultiCell(189, 4, utf8_decode($model->novedad), 0, 'J');
        
        	//firma trabajador
        $pdf->SetXY(10, 230);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA PROVEEDOR: ___________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 235);
        $pdf->Cell(35, 5, 'C.C.:', 0, 0, 'L',0);
        //firma empresa
        $pdf->SetXY(10, 255);//firma trabajador
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA EMPRESA: ____________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 260);
        $pdf->Cell(35, 5, 'NIT/CC.:', 0, 0, 'L',0);
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}

global $id_novedad;
$id_novedad = $model->id_novedad;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("NovedadProduccion$model->id_novedad.pdf", 'D');

exit;


