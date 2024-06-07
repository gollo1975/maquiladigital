<?php

use inquid\pdf\FPDF;
use app\models\Empleado;
use app\models\PilotoDetalleProduccion;
use app\models\ConceptoSalarios;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        if ( $this->PageNo() == 1 ) {
            $idordenproduccion = $GLOBALS['idordenproduccion'];
            $piloto = \app\models\Ordenproduccion::findOne($idordenproduccion);
            $config = Matriculaempresa::findOne(1);
            $municipio = Municipio::findOne($config->idmunicipio);
            $departamento = Departamento::findOne($config->iddepartamento);
            //Logo
           $this->SetXY(43, 10);
             $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
            //Encabezado
            $this->SetFillColor(220, 220, 220);
            $this->SetXY(53, 9);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(30, 5, utf8_decode("EMPRESA:"), 0, 0, 'l', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
            $this->SetXY(30, 5);
            //FIN
            $this->SetXY(53, 13);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(30, 5, utf8_decode("NIT:"), 0, 0, 'l', 1);
             $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
            $this->SetXY(40, 5);
            //FIN
            $this->SetXY(53, 17);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(30, 5, utf8_decode("DIRECCION:"), 0, 0, 'l', 1);
             $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
            $this->SetXY(40, 5);
            //FIN
            $this->SetXY(53, 21);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(30, 5, utf8_decode("TELEFONO:"), 0, 0, 'l', 1);
             $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($config->telefonomatricula." - ". $config->celularmatricula), 0, 0, 'L', 1);
            $this->SetXY(40, 5);
            //FIN
            $this->SetXY(53, 25);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(30, 5, utf8_decode("MUNICIPIO:"), 0, 0, 'l', 1);
             $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 1);
            $this->SetXY(40, 5);

            //FIN
            $this->SetXY(10, 32);
            $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
             $this->SetXY(10, 32.5);
            $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
            //Prestaciones sociales
            $this->SetFillColor(220, 220, 220);
            $this->SetXY(10, 39);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(162, 7, utf8_decode("REPORTE DE MEDIDAS PILOTOS (DL)"), 0, 0, 'l', 0);
            $this->Cell(30, 7, utf8_decode('REF. : '.str_pad($piloto->codigoproducto, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
           // $this->SetFillColor(200, 200, 200);
            $this->SetXY(10, 48);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("NIT/CEDULA:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(20, 5, utf8_decode($piloto->cliente->cedulanit), 0, 0, 'L',1);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("CLIENTE:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(50, 5, utf8_decode($piloto->cliente->nombrecorto), 0, 0, 'L', 1);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("DIRECCION:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(40, 5, utf8_decode($piloto->cliente->direccioncliente), 0, 0, 'L', 1);
            //FIN
            $this->SetXY(10, 52);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("TELEFONO:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(20, 5, utf8_decode($piloto->cliente->telefonocliente), 0, 0, 'L',1);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("CONTACTO:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(50, 5, utf8_decode($piloto->cliente->contacto), 0, 0, 'L', 1);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(25, 5, utf8_decode("OP CLIENTE:"), 0, 0, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->Cell(40  , 5, utf8_decode($piloto->ordenproduccion. "  OP: ".$piloto->idordenproduccion), 0, 0, 'L', 1);
            //FIN
             $this->EncabezadoDetalles();
        }
    }
    function EncabezadoDetalles() {
        $this->Ln(8);
        $header = array('OPERACION', ('TALLA'), ('MEDIDA FICHA'), ('MEDIDA CONFECCION'), ('TOLERANCIA'), ('OBSERVACION'));
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);

        //creamos la cabecera de la tabla.
        $w = array(53, 12, 22, 30, 20, 48);
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
        $detalle_piloto = PilotoDetalleProduccion::find()->where(['=','idordenproduccion', $model->idordenproduccion])
                                                         ->groupBy('iddetalleorden')
                                                         ->all();		
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 6.5);
	$auxiliar = 0; $totalLinea = 0;
        $total = count($detalle_piloto);
        foreach ($detalle_piloto as $detalle):   
            $totalLinea += 1;
            $auxiliar = $detalle->iddetalleorden;
            $detalle_piloto = PilotoDetalleProduccion::find()->where(['=','iddetalleorden', $auxiliar])
                                                         ->orderBy('concepto ASC ')
                                                         ->all();		
            foreach ($detalle_piloto as $medidas):
                $pdf->Cell(53, 4, $medidas->concepto, 0, 0, 'L');
                $pdf->Cell(12, 4, $medidas->detalleorden->productodetalle->prendatipo->talla->talla, 0, 0, 'C');
                $pdf->Cell(22, 4, $medidas->medida_ficha_dl, 0, 0, 'R');
                $pdf->Cell(30, 4, $medidas->medida_confeccion_dl, 0, 0, 'R');
                $pdf->Cell(20, 4, $medidas->tolerancia_dl, 0, 0, 'R');
                $pdf->Cell(48, 4, $medidas->observacion_dl, 0, 0, 'C');
                $pdf->Ln();
                $pdf->SetAutoPageBreak(true, 20);    
            endforeach; 
            if ($totalLinea == $total){
              $this->Ln();  
            }else{
                $this->Ln();
                $header = array('OPERACION', ('TALLA'), ('MEDIDA FICHA'), ('MEDIDA CONFECCION'), ('TOLERANCIA'), ('OBSERVACION'));
                $this->SetFillColor(200, 200, 200);
                $this->SetTextColor(0);
                $this->SetDrawColor(0, 0, 0);
                $this->SetLineWidth(.2);
                $this->SetFont('', 'B', 7);

                //creamos la cabecera de la tabla.
                $w = array(53, 12, 22, 30, 20, 48);
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
        endforeach;
	$this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);
    }
        
    function Footer() {

        $this->SetFont('Arial', '', 7);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $idordenproduccion;
$idordenproduccion = $model->idordenproduccion;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("ReporteMedidasPilotos$model->idordenproduccion.pdf", 'D');

exit;