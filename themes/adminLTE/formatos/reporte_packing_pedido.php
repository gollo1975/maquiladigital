<?php

use inquid\pdf\FPDF;
use app\models\PackingPedido;
use app\models\PackingPedidoDetalle;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        // Acceder a la variable global definida al final del script
        $id_packing = $GLOBALS['id_packing'];
        $packing = PackingPedido::findOne($id_packing);
        $config = Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        
        // Logo
        $this->SetXY(43, 10);
        $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        
        // Encabezado de la empresa
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(53, 9);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Empresa:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(61, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 1);
        
        $this->SetXY(53, 13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Nit:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(61, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 1);
        
        $this->SetXY(53, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Dirección:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(61, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 1);
        
        $this->SetXY(53, 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Teléfono:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(61, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 1);
        
        $this->SetXY(53, 25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(19, 5, utf8_decode("Municipio:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(61, 5, utf8_decode($municipio->municipio." - ".$departamento->departamento), 0, 0, 'L', 1);
        
        $this->SetXY(10, 32);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        $this->SetXY(10, 32.5);
        $this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________________________________________________"), 0, 0, 'C', 0);
        
        // Título y Número de Pedido
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(10, 39);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(162, 7, utf8_decode("PACKING DESPACHO"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($packing->numero_packing, 5, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
        $this->SetFillColor(200, 200, 200);
        
        // Datos del Cliente
        $this->SetXY(10, 49);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Nit:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($packing->cliente->cedulanit.'-'.$packing->cliente->dv), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Cliente:"), 0, 0, 'c', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($packing->cliente->nombrecorto), 0, 0, 'c', 1);
        
        $this->SetXY(10, 53);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Departamento:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($packing->cliente->departamento->departamento), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Municipio:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($packing->cliente->municipio->municipio), 0, 0, 'L', 1);
        
        $this->SetXY(10, 57);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha pedido:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($packing->fecha_proceso), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Fecha hora:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($packing->fecha_hora_registro), 0, 0, 'L', 1);
        
        $this->SetXY(10, 61);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha registro:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($packing->fecha_proceso), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("No pedido:"), 0, 0, 'J', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($packing->despacho->pedido->numero_pedido), 0, 0, 'L', 1);
        
        // Lineas del encabezado (Divisores)
        $this->Line(10,68,10,240);
        $this->Line(30,68,30,240);
        $this->Line(90,68,90,240);
        $this->Line(110,68,110,240);
        $this->Line(130,68,130,240);
        $this->Line(150,68,150,240);
        $this->Line(170,68,170,240);
        $this->Line(202,68,202,240);
        $this->Line(202,240,10,240);
        
        // Encabezado de la tabla de detalles (se llama en cada nueva página si es necesario)
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'REFERENCIA','No CAJA', 'TALLA', 'COLOR','CANTIDAD', 'GUIA');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        // creamos la cabecera de la tabla.
        $w = array(20,60, 20, 20, 20, 20, 32);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1)
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            else
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);

        // Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(5);
    }

    function Body($pdf, $model) {
        $detalles = PackingPedidoDetalle::find()->where([
                'id_packing' => $model->id_packing,
            ])
            // Ordenar por el ID del inventario para agrupar las referencias iguales
            ->orderBy(['id_inventario' => SORT_ASC]) 
            ->all(); 
        
        $referencia_actual = null; // Variable para rastrear el cambio de grupo
        foreach ($detalles as $detalle) {
            if ($detalle->id_inventario !== $referencia_actual && $referencia_actual !== null) {
                // Si la referencia ha cambiado e ITERACIÓN NO ES LA PRIMERA:
                
                // Imprimir línea de separación
                $pdf->Ln(1); // Pequeño salto antes de la línea
                $pdf->SetDrawColor(0, 0, 0); // Color de la línea (negro)
                $pdf->SetLineWidth(0.1); // Grosor de la línea
                // Dibuja una línea horizontal de 10mm a 202mm (ancho total de la tabla)
                $pdf->Line(10, $pdf->GetY(), 202, $pdf->GetY()); 
                $pdf->Ln(1); // Pequeño salto después de la línea
            }
        
            // 3. IMPRIMIR LA LÍNEA DEL DETALLE
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(20, 4, utf8_decode($detalle->inventario->codigo_producto), 0, 0, 'L');
            $pdf->Cell(60, 4, utf8_decode($detalle->inventario->nombre_producto), 0, 0, 'L');
            $pdf->Cell(20, 4, utf8_decode($detalle->numero_caja), 0, 0, 'C');
            $pdf->Cell(20, 4, utf8_decode($detalle->talla->talla), 0, 0, 'C');
            $pdf->Cell(20, 4, utf8_decode($detalle->colores->color), 0, 0, 'C');
            $pdf->Cell(20, 4, number_format($detalle->cantidad_despachada, 0, ',', '.'), 0, 0, 'R');
            $pdf->Cell(32, 4, utf8_decode($detalle->numero_guia), 0, 0, 'L');
            $pdf->Ln();
            // 4. ACTUALIZAR LA REFERENCIA ACTUAL
            $referencia_actual = $detalle->id_inventario; 
            
            $pdf->SetAutoPageBreak(true, 20);
        }    

        // Fila de total de unidades al final (opcional, pero útil)
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(134, 5, utf8_decode('TOTAL DE UNIDADES A DESPACHAR:'), 0, 0, 'R', 0);
        $pdf->Cell(24, 5, number_format($model->cantidad_despachadas, 0, ',', '.'), 0, 0, 'R', 0);
        $pdf->Cell(24, 5, '', 0, 1, 'R', 0); // Espacio para la columna de Color
        
        // Firma y pie de página
        $pdf->SetXY(10, 265);//firma cliente
        $this->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 5, 'FIRMA CLIENTE: ____________________________________________________', 0, 0, 'L',0);
        $pdf->SetXY(10, 270);
        $pdf->Cell(35, 5, 'NIT/CC.:', 0, 0, 'L',0);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
// --- Inicio de la ejecución del PDF ---
global $id_packing;
$id_packing = $model->id_packing;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Body($pdf,$model);
$pdf->SetFont('Times', '', 10);
$pdf->Output("Packin_pedido_$model->numero_packing.pdf", 'D');

exit;

function zero_fill ($valor, $long = 0)
{
    return str_pad($valor, $long, '0', STR_PAD_LEFT);
}