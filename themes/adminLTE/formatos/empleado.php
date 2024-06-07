<?php

use inquid\pdf\FPDF;
use app\models\Empleado;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_empleado = $GLOBALS['id_empleado'];
        $empleado = Empleado::findOne($id_empleado);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        //Logo
        $this->SetXY(53, 10);
         $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
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
        //Factura
        /*$this->SetXY(10, 47);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(162, 7, utf8_decode("FACTURA DE VENTA"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($factura->nrofactura, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
        $this->SetFillColor(200, 200, 200);
        $this->SetXY(10, 54);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 5, utf8_decode("NIT:"), 0, 0, 'l', 1);
        $this->Cell(118, 5, utf8_decode($factura->cliente->cedulanit.'-'.$factura->cliente->dv), 0, 0, 'L',1);
        $this->Cell(25, 5, utf8_decode("FECHA EMISION"), 0, 0, 'c', 1);
        $this->Cell(25, 5, utf8_decode("FECHA VENCE"), 0, 0, 'c', 1);
        $this->SetXY(10, 59);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 5, utf8_decode("CLIENTE:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(118, 5, utf8_decode($factura->cliente->nombrecorto), 0, 0, 'L',1);
        $this->Cell(25, 5, utf8_decode($factura->fechainicio), 0, 0, 'c', 1);
        $this->Cell(25, 5, utf8_decode($factura->fechavcto), 0, 0, 'c', 1);
        $this->SetXY(10, 64);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 5, utf8_decode("CIUDAD:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(118, 5, utf8_decode($factura->cliente->municipio->municipio." - ".$factura->cliente->departamento->departamento), 0, 0, 'L', 1);
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 5, utf8_decode("ORDEN PROD INT:"), 0, 0, 'J', 1);
        $this->SetFont('Arial', '', 8);
        if ($factura->libre == 0){
            $this->Cell(25, 5, utf8_decode($factura->ordenproduccion->ordenproduccion), 0, 0, 'L', 1);
        }else{
            $this->Cell(25, 5, utf8_decode('N/A'), 0, 0, 'L', 1);
        }        
        $this->SetXY(10, 69);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 5, utf8_decode("DIRECCIÓN:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(118, 5, utf8_decode($factura->cliente->direccioncliente), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 7.5);
        $this->Cell(25, 5, utf8_decode("TIPO SERVICIO:"), 0, 0, 'J', 1);
        $this->SetFont('Arial', '', 7.5);
        if ($factura->libre == 0){
            $this->Cell(25, 5, utf8_decode(substr($factura->ordenproduccion->tipo->tipo,0,13)), 0, 0, 'L', 1);
        }else{
            $this->Cell(25, 5, utf8_decode(substr($factura->facturaventatipo->concepto,0,13)), 0, 0, 'L', 1);
        }                
        $this->SetXY(10, 74);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(23, 5, utf8_decode("TELÉFONO:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(118, 5, utf8_decode($factura->cliente->telefonocliente), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, utf8_decode("FORMA PAGO:"), 0, 0, 'c', 1);
        $this->SetFont('Arial', '', 8);
        if ($factura->cliente->formapago == 1 ){$formapago = 'CONTADO';}else{$formapago = 'CRÉDITO';}
        $this->Cell(25, 5, utf8_decode($formapago) , 0, 0, 'c', 1);                       
        //Lineas del encabezado
        $this->Line(10,85,10,170);
        $this->Line(23,85,23,170);
        $this->Line(118,85,118,170);
        $this->Line(136,85,136,170);
        $this->Line(156,85,156,170);
        $this->Line(176,85,176,170);
        $this->Line(201,85,201,170);
        //Cuadro de la nota
        $this->Line(10,170,156,170);//linea horizontal superior
        $this->Line(10,170,10,178);//linea vertical
        $this->Line(10,178,156,178);//linea horizontal inferior
        //Linea de las observacines
        $this->Line(10,178,10,202);//linea vertical
        //lineas para los cuadros de nit/cc,fecha,firma        
        $this->Line(10,218,10,245);//linea vertical x1,y1,x2,y2   
        $this->Line(42,218,42,245);//linea vertical x1,y1,x2,y2
        $this->Line(74,218,74,245);//linea vertical x1,y1,x2,y2
        $this->Line(138,218,138,245);//linea vertical x1,y1,x2,y2
        $this->Line(201,218,201,245);//linea vertical x1,y1,x2,y2                
        //Detalle factura*/
        $this->EncabezadoDetalles();
        
        
    }

    function EncabezadoDetalles() {
        /*$this->Ln(7);
        $header = array('CODIGO', 'PRODUCTO REFERENCIA', 'CANTIDAD', 'VLR UNIT', 'DSCTO', 'TOTAL');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        //creamos la cabecera de la tabla.
        $w = array(13, 95, 18, 20, 20, 25);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1)
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            else
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);

        //Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(5);*/
    }

    function Body($pdf,$model) {
        /*$config = Matriculaempresa::findOne(1);
        $detalles = Facturaventadetalle::find()->where(['=','idfactura',$model->idfactura])->all();
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 8);
        $cant = 0;
        foreach ($detalles as $detalle) { 
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(13, 4, $detalle->codigoproducto, 0, 0, 'L');
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(95, 4, $detalle->productodetalle->prendatipo->prenda.' / '.$detalle->productodetalle->prendatipo->talla->talla, 0, 0, 'L');
            $pdf->Cell(18, 4, $detalle->cantidad, 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($detalle->preciounitario, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(20, 4, number_format(0, 0, '.', ','), 0, 0, 'R');
            $pdf->Cell(25, 4, number_format($detalle->total, 2, '.', ','), 0, 0, 'R');            
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
            $cant = $cant + $detalle->cantidad;
        }
        $this->SetFillColor(200, 200, 200);
        $pdf->SetXY(10, 170);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode(valorEnLetras($model->totalpagar)),0,'J');
        $pdf->SetXY(156, 170);
        $pdf->MultiCell(20, 8, 'SUBTOTAL:',1,'C');
        $pdf->SetXY(176, 170);
        $pdf->MultiCell(25, 8, number_format($model->subtotal, 0, '.', ','),1,'R');
        $pdf->SetXY(10, 178);
        $this->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(146, 4, utf8_decode('Observaciones: '.$model->observacion),0,'J');
        $pdf->SetXY(156, 178);
        $pdf->MultiCell(20, 8, 'RETE FTE:',1,'C');
        $pdf->SetXY(176, 178);
        $pdf->MultiCell(25, 8, number_format($model->retencionfuente, 0, '.', ','),1,'R');
        $pdf->SetXY(156, 186);
        $pdf->MultiCell(20, 8, 'IVA:',1,'C');
        $pdf->SetXY(176, 186);
        $pdf->MultiCell(25, 8, number_format($model->impuestoiva, 0, '.', ','),1,'R');
        $pdf->SetXY(156, 194);
        $pdf->MultiCell(20, 8, 'RETE IVA:',1,'C');
        $pdf->SetXY(176, 194);
        $pdf->MultiCell(25, 8, number_format($model->retencioniva, 0, '.', ','),1,'R');
        $pdf->SetXY(10, 202);
        $pdf->MultiCell(108, 8, '',1,'R',1);
        $pdf->SetXY(118, 202);
        $pdf->MultiCell(38, 8, 'TOTAL CANTIDAD: '.$cant,1,'C',1);
        $pdf->SetXY(156, 202);           
        $pdf->MultiCell(20, 8, 'TOTAL:',1,'C',1);
        $pdf->SetXY(176, 202);
        $pdf->MultiCell(25, 8, number_format($model->totalpagar, 0, '.', ','),1,'R',1);
        $pdf->SetXY(10, 210);//recibido,aceptado        
        $pdf->Cell(64, 8, 'RECIBIDO POR',1,'J',1);        
        $pdf->Cell(64, 8, 'ACEPTADO POR',1,'J',1);
        $pdf->Cell(63, 8, '',1,'J',1);
        $pdf->SetXY(10, 218);//nit,fecha,fecha,firma        
        $pdf->Cell(32, 5, 'NIT/C.C',0,'L');
        $pdf->Cell(32, 5, 'FECHA',0,'L');        
        $pdf->Cell(64, 5, 'FECHA',0,'L');
        $pdf->Cell(63, 5, 'FIRMA DEL EMISOR',0,'L');
        $pdf->SetXY(10, 245);//nit,fecha,fecha,firma  
        $pdf->MultiCell(191, 4, utf8_decode($config->declaracion),1,'J');
        $pdf->SetXY(10, 266);//tipo cuenta
        $pdf->Cell(191, 5, 'TIPO DE CUENTA: '.$config->bancoFactura->producto.'  - NUMERO DE CUENTA: '.$config->bancoFactura->numerocuenta.' - ENTIDAD BANCARIA: '.$config->bancoFactura->entidad,1,'C');*/
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_empleado;
$id_empleado = $model->id_empleado;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Empleado$model->id_empleado.pdf", 'D');

exit;