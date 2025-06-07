    <?php

use inquid\pdf\FPDF;
use app\models\ProcesoDisciplinario;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;

class PDF extends FPDF {

    function Header() {
        $id_proceso = $GLOBALS['id_proceso'];
        $proceso = ProcesoDisciplinario::findOne($id_proceso);
        $config = \app\models\Matriculaempresa::findOne(1);
        $municipio = Municipio::findOne($config->idmunicipio);
        $departamento = Departamento::findOne($config->iddepartamento);
        //Logo
       $this->SetXY(43, 10);
        $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFillColor(220, 220, 220);
        $this->SetXY(53, 9);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("EMPRESA:"), 0, 0, 'l', 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'L', 0);
        $this->SetXY(30, 5);
        //FIN
        $this->SetXY(53, 13);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("NIT:"), 0, 0, '0', 0);
         $this->SetFont('Arial', '', 7);
        $this->Cell(40, 5, utf8_decode($config->nitmatricula." - ".$config->dv), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("DIRECCION:"), 0, 0, '0', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->direccionmatricula), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("TELEFONO:"), 0, 0, '0', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->telefonomatricula), 0, 0, 'L', 0);
        $this->SetXY(40, 5);
        //FIN
        $this->SetXY(53, 25);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(30, 5, utf8_decode("MUNICIPIO:"), 0, 0, '0', 0);
         $this->SetFont('Arial', '', 8);
        $this->Cell(40, 5, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'L', 0);
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
        //$this->Cell(162, 7, utf8_decode("LLAMADO DE ATENCION"), 0, 0, 'l', 0);
       
        
       // $this->SetFillColor(200, 200, 200);
        $fecha_objeto = new DateTime($proceso->fecha_registro);
        $nombres_meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        $dia = $fecha_objeto->format('d');
        $mes_numero = (int)$fecha_objeto->format('m'); // Obtiene el número del mes y lo convierte a entero
        $mes_nombre = $nombres_meses[$mes_numero]; // Obtiene el nombre del mes del array
        $año = $fecha_objeto->format('Y');
        $fecha_formateada = $dia . ' de ' . $mes_nombre . ' del ' . $año;
        $ciudad = ucfirst(strtolower($proceso->codigoMunicipio->municipio));
        ///***///
        $this->SetXY(10, 48);
        $this->SetFont('Arial', '', 10);
        $this->Cell(170, 5, utf8_decode($ciudad. ", ". $fecha_formateada), 0, 0, 'L', 0);
        $this->Cell(20, 7, utf8_decode('N°. '.str_pad($proceso->numero_radicado, 4, "0", STR_PAD_LEFT)), 0, 0, 'l', 0);
        //fin
        $this->SetXY(10, 62);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 5, utf8_decode("Señores"), 0, 0, 'L', 0);
        //fin
        $this->SetXY(10, 68);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 5, utf8_decode($proceso->empleado->nombrecorto), 0, 0, 'L', 0);
        //fin
        $this->SetXY(10, 72);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 5, utf8_decode($proceso->empleado->tipoDocumento->tipo." ".$proceso->empleado->identificacion), 0, 0, 'L', 0);
        //fin
        $this->SetXY(10, 80);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 5, utf8_decode("Asunto: " .$proceso->tipoDisciplinario->concepto), 0, 0, 'L', 0);
        //fin
       
        //fin
        $this->Line(10,8,202,8);//linea superior horizontal
        $this->Line(10,30,10,8);//primera linea en y
        $this->Line(45,30,45,8);//segunda linea en y
        $this->Line(130,30,130,8);//tercera linea en y
        $this->Line(202,30,202,8);//cuarta linea en y
        $this->Line(10,30,202,30);//linea inferior horizontal
    }    
       function Body($pdf, $model) {
           $empresa = MatriculaEmpresa::findOne(1);
           $cadena = utf8_decode($model->descripcion_proceso); 
           $pdf->SetXY(10, 93);
           $this->SetFont('Arial', '', 10);
           $pdf->MultiCell(0,5, $cadena);
       
        $pdf->SetXY(10, 257);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(35, 5, '________________________________', 0, 0, 'L',0);
         $pdf->SetXY(10, 262);
        $pdf->Cell(35, 5, 'TRABAJADOR', 0, 0, 'L',0);
        $pdf->SetXY(10, 267);
        $pdf->Cell(35, 5, utf8_decode($model->empleado->tipoDocumento->tipo.' '.$model->empleado->identificacion), 0, 0, 'L',0);
        // SEGUNDA FIRMA
        $pdf->SetXY(120, 257);
        $this->SetFont('', 'B', 9);
        $pdf->Cell(120, 5, '________________________________', 0, 0, 'L',0);
        $pdf->SetXY(120, 262);
        $pdf->Cell(120, 5, 'EMPRESA', 0, 0, 'L',0);
        $pdf->SetXY(120, 267);
        $pdf->Cell(120, 5, utf8_decode($empresa->nitmatricula. "-".$empresa->dv), 0, 0, 'L',0);
        //liena
        //encabezado
        $pdf->SetXY(106, 12);
        $pdf->Cell(120, 5, 'GESTION HUMANA', 0, 0, 'C',0);
        $this->SetXY(129, 16);
        $pdf->Cell(125, 5, '_________________________________________', 0, 0, 'L',0);
        //SEGUNDO NOMBRE
        $pdf->SetXY(106, 22);
        $pdf->Cell(120, 5, 'PROCESOS DISCIPLINARIOS', 0, 0, 'C',0);
       }  
    function Footer() {

        $this->SetFont('Arial', '', 7);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

}
global $id_proceso;
$id_proceso = $model->id_proceso;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("ProcesoDisciplinario_$model->numero_radicado.pdf", 'D');

exit;
