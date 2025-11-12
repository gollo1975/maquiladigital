<?php

use inquid\pdf\FPDF;
use app\models\Pedidos;
use app\models\PedidoTallas;
use app\models\PedidoColores;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        // Acceder a la variable global definida al final del script
        $id_pedido = $GLOBALS['id_pedido'];
        $pedido = Pedidos::findOne($id_pedido);
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
        $this->Cell(162, 7, utf8_decode("TALLAS Y COLORES DEL PEDIDO"), 0, 0, 'l', 0);
        $this->Cell(30, 7, utf8_decode('N°. '.str_pad($pedido->numero_pedido, 5, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
        $this->SetFillColor(200, 200, 200);
        
        // Datos del Cliente
        $this->SetXY(10, 49);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Nit:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($pedido->cliente->cedulanit.'-'.$pedido->cliente->dv), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Cliente:"), 0, 0, 'c', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($pedido->cliente->nombrecorto), 0, 0, 'c', 1);
        
        $this->SetXY(10, 53);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Departamento:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($pedido->cliente->departamento->departamento), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Municipio:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($pedido->cliente->municipio->municipio), 0, 0, 'L', 1);
        
        $this->SetXY(10, 57);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha pedido:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($pedido->fecha_pedido), 0, 0, 'L',1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("Fecha entrega:"), 0, 0, 'l', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($pedido->fecha_entrega), 0, 0, 'L', 1);
        
        $this->SetXY(10, 61);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(26, 5, utf8_decode("Fecha registro:"), 0, 0, 'L', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(75, 5, utf8_decode($pedido->fecha_proceso), 0, 0, 'l', 1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, utf8_decode("User name:"), 0, 0, 'J', 1);
        $this->SetFont('Arial', '', 7);
        $this->Cell(71, 5, utf8_decode($pedido->user_name), 0, 0, 'L', 1);
        
        // Lineas del encabezado (Divisores)
        $this->Line(10,68,10,240);
        $this->Line(30,68,30,240);
        $this->Line(115,68,115,240);
        $this->Line(144,68,144,240);
        $this->Line(173,68,173,240);
        $this->Line(202,68,202,240);
        $this->Line(202,240,10,240);
        
        // Encabezado de la tabla de detalles (se llama en cada nueva página si es necesario)
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        $this->Ln(7);
        $header = array('CODIGO', 'REFERENCIA', 'TALLA', 'UNIDADES','COLOR');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 8);

        // creamos la cabecera de la tabla.
        $w = array(20, 85, 29, 29, 29);
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
        $detalles = \app\models\PedidosDetalle::find()->where(['=','id_pedido',$model->id_pedido])->all();
        
        // Array para almacenar y agrupar los ítems por Referencia-Talla-Color
        $groupedItems = [];

        foreach ($detalles as $detalle) {
            $tallas = PedidoTallas::find()->where([
                'id_pedido' => $model->id_pedido,
                'id_detalle' => $detalle->id_detalle,
            ])->all();
            
            $referenceKey = $detalle->inventario->codigo_producto;
            $referenceName = utf8_decode($detalle->inventario->nombre_producto);
            
            foreach ($tallas as $talla){
                // Se asume que PedidoColores se relaciona por 'codigo' como en el código original
                $colores = PedidoColores::find()->where([
                    'id_pedido' => $model->id_pedido,
                    'codigo' => $talla->codigo,
                ])->all();
                
                // Si no se encuentran colores para esta talla, se asume un color "N/A"
                if (empty($colores)) {
                    $tallaKey = $talla->talla->talla;
                    $colorName = utf8_decode('N/A');
                    
                    // Clave de agrupamiento: Código-Talla-Color
                    $key = "{$referenceKey}-{$tallaKey}-{$colorName}";
                    
                    if (!isset($groupedItems[$key])) {
                        $groupedItems[$key] = [
                            'codigo' => $referenceKey,
                            'referencia' => $referenceName,
                            'talla' => $tallaKey,
                            'color' => $colorName,
                            'unidades' => 0,
                        ];
                    }
                    // Agrega la cantidad de PedidoTallas
                    $groupedItems[$key]['unidades'] += $talla->cantidad;
                    
                } else {
                    foreach ($colores as $color) {
                        $tallaKey = $talla->talla->talla;
                        $colorName = utf8_decode($color->colores->color);
                        
                        // Clave de agrupamiento: Código-Talla-Color
                        $key = "{$referenceKey}-{$tallaKey}-{$colorName}";
                        
                        if (!isset($groupedItems[$key])) {
                            $groupedItems[$key] = [
                                'codigo' => $referenceKey,
                                'referencia' => $referenceName,
                                'talla' => $tallaKey,
                                'color' => $colorName,
                                'unidades' => 0,
                            ];
                        }
                        // Agrega la cantidad (se asume que PedidoTallas->cantidad es la cantidad para la combinación)
                        $groupedItems[$key]['unidades'] += $talla->cantidad;
                    }
                }
            }
        }
        
        // 1. ORDENAR el array agrupado por Código (Referencia) y luego por Talla
        $codigo_ordenar = [];
        $tallas_ordenar = [];
        foreach ($groupedItems as $key => $item) {
            $codigo_ordenar[$key] = $item['codigo'];
            $tallas_ordenar[$key] = $item['talla'];
        }

        // Ordenar $groupedItems: 1° por Código (ASC), 2° por Talla (ASC)
        if (!empty($codigo_ordenar)) {
            array_multisort(
                $codigo_ordenar, SORT_ASC,
                $tallas_ordenar, SORT_ASC,
                $groupedItems
            );
        }

        // Imprimir los datos agrupados
        $pdf->SetX(10);
        $pdf->SetFont('Arial', '', 7);
        $totalUnidades = 0;
        
        $lastTallaPrinted = null; // Variable para rastrear la última talla impresa
        $lastReferencePrinted = null; // Variable para rastrear la última referencia impresa
        
        foreach ($groupedItems as $item) {
            
            $currentReference = $item['codigo'];
            $currentTalla = $item['talla'];
            
            // --- LÓGICA DE SEPARADORES ---
            
            // Separador principal: Cambio de REFERENCIA (más grueso)
            if ($lastReferencePrinted !== null && $lastReferencePrinted !== $currentReference) {
                // Separador de Referencia (más grueso y con más espacio)
                $pdf->Ln(2); 
                $this->SetFillColor(200, 200, 200); // Gris oscuro
                $pdf->Rect(10, $pdf->GetY(), 192, 1.5, 'F'); 
                $pdf->Ln(2); 
                $pdf->SetX(10); 
                $lastTallaPrinted = null; // Resetear la talla para la nueva referencia
            }
            
            // Separador secundario: Cambio de TALLA dentro de la misma Referencia (más sutil)
            // Se ejecuta si: no es la primera línea, la talla cambió Y la referencia es la misma
            if ($lastTallaPrinted !== null && $lastTallaPrinted !== $currentTalla && $lastReferencePrinted === $currentReference) {
                $pdf->Ln(1); // Espacio antes del separador
                $this->SetFillColor(240, 240, 240); // Gris claro
                $pdf->Rect(10, $pdf->GetY(), 192, 0.5, 'F'); 
                $pdf->Ln(1); // Espacio después del separador
                $pdf->SetX(10); // Volver a la posición de inicio
            }
            
            // Imprimir la línea
            $pdf->Cell(20, 4, $item['codigo'], 0, 0, 'L');
            $pdf->Cell(85, 4, $item['referencia'], 0, 0, 'L');
            $pdf->Cell(29, 4, $item['talla'], 0, 0, 'R');
            // Formatear las unidades
            $pdf->Cell(29, 4, number_format($item['unidades'], 0, ',', '.'), 0, 0, 'R');
            $pdf->Cell(29, 4, $item['color'], 0, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 20);
            $totalUnidades += $item['unidades'];
            
            // 3. Actualizar los rastreadores
            $lastTallaPrinted = $currentTalla;
            $lastReferencePrinted = $currentReference;
        }

        // Fila de total de unidades al final (opcional, pero útil)
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(134, 5, utf8_decode('TOTAL DE UNIDADES A DESPACHAR:'), 0, 0, 'R', 0);
        $pdf->Cell(29, 5, number_format($totalUnidades, 0, ',', '.'), 0, 0, 'R', 0);
        $pdf->Cell(29, 5, '', 0, 1, 'R', 0); // Espacio para la columna de Color
        
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
global $id_pedido;
$id_pedido = $model->id_pedido;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if($model->pedido_anulado == 1){
    $pdf->Image('dist/images/logos/documentoanulado.jpeg' , 20 ,212.2, 130 , 28,'JPEG');
} 

$pdf->Body($pdf,$model);
$pdf->SetFont('Times', '', 10);
$pdf->Output("Tallas_Pedido_$model->numero_pedido.pdf", 'D');

exit;

function zero_fill ($valor, $long = 0)
{
    return str_pad($valor, $long, '0', STR_PAD_LEFT);
}