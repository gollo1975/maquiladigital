<?php

use inquid\pdf\FPDF;
use app\models\CambioSalario;
use app\models\Contrato;
use app\models\FormatoContenido;
use app\models\Municipio;
use app\models\Departamento;
use app\models\Matriculaempresa;

class PDF extends FPDF {

    function Header() {
        $id_cambio_salario = $GLOBALS['id_cambio_salario'];
        $cambio_salario = CambioSalario::findOne($id_cambio_salario);
        $config = Matriculaempresa::findOne(1);        
        //Logo
        $this->SetXY(53, 10);
        $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        //Encabezado
        $this->SetFont('Arial', '', 10);
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
        $this->SetFont('Arial', 'B', 10);
        //$this->SetXY(10, 38);
        //$this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________"), 0, 0, 'C', 0);
        
        $this->EncabezadoDetalles();
    }

    function EncabezadoDetalles() {
        
        
    }

    function Body($pdf, $modelocambio) {
        $config = Matriculaempresa::findOne(1);
        $model = CambioSalario::findOne($modelocambio->id_cambio_salario);
        $contrato = Contrato::findOne($model->id_contrato);
        $formato = FormatoContenido::findOne($model->id_formato_contenido);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(10);
        $pdf->Ln(10);
        if (!$formato){
           $cadena = "El contrato no tiene asociado un formato tipo contrato"; 
        } else {                          
            $cadena = utf8_decode($formato->contenido);            
        }
        
        //contenido de la cadena a sustituir
        $sustitucion1 = ''.number_format($contrato->empleado->identificacion);
        $sustitucion2 = utf8_decode($contrato->empleado->nombrecorto);
        $sustitucion3 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($contrato->fecha_inicio))) ." de %Y", strtotime($contrato->fecha_inicio));
        $sustitucion4 = '$ '.number_format($model->nuevo_salario);
        $sustitucion5 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($model->fecha_aplicacion))) ." de %Y", strtotime($model->fecha_aplicacion));
        $sustitucion6 = $contrato->empleado->ciudadExpedicion->municipio .' - '. $contrato->empleado->ciudadExpedicion->departamento->departamento;
        $sustitucion7 = $config->razonsocialmatricula;
        $sustitucion8 = $config->nitmatricula;
        $sustitucion9 = $model->id_cambio_salario; 
        $sustituciona = $config->representante_legal;
        //etiquetas de sustitución
        $patron1 = '/#1/'; //Documento                
        $patron2 = '/#2/'; //Nombre completo
        $patron3 = '/#3/'; //Fecha contrato
        $patron4 = '/#4/'; //Nuevo Salario
        $patron5 = '/#5/'; //fecha de aplicaicon
        $patron6 = '/#6/'; //ciudad de expedicion
        $patron7 = '/#7/'; //empresa        
        $patron8 = '/#8/'; //Nit empresa
        $patron9 = '/#9/'; //Nro cambio
        $patrona = '/#a/'; // nombre representante legal 
        //reemplazar en la cadena
        $cadenaCambiada = preg_replace($patron1, $sustitucion1, $cadena);
        $cadenaCambiada = preg_replace($patron2, $sustitucion2, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron3, $sustitucion3, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron4, $sustitucion4, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron5, $sustitucion5, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron6, $sustitucion6, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron7, $sustitucion7, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron8, $sustitucion8, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron9, $sustitucion9, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrona, $sustituciona, $cadenaCambiada);
     
        $pdf->MultiCell(0,5, $cadenaCambiada);
                
    }

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }
    
    public static function MesesEspañol($mes) {
        
        if ($mes == '01'){
            $mesEspañol = "Enero";
        }
        if ($mes == '02'){
            $mesEspañol = "Febrero";
        }
        if ($mes == '03'){
            $mesEspañol = "Marzo";
        }
        if ($mes == '04'){
            $mesEspañol = "Abril";
        }
        if ($mes == '05'){
            $mesEspañol = "Mayo";
        }
        if ($mes == '06'){
            $mesEspañol = "Junio";
        }
        if ($mes == '07'){
            $mesEspañol = "Julio";
        }
        if ($mes == '08'){
            $mesEspañol = "Agosto";
        }
        if ($mes == '09'){
            $mesEspañol = "Septiembre";
        }
        if ($mes == '10'){
            $mesEspañol = "Octubre";
        }
        if ($mes == '11'){
            $mesEspañol = "Noviembre";
        }
        if ($mes == '12'){
            $mesEspañol = "Diciembre";
        }

        return $mesEspañol;
    }

}

global $id_cambio_salario;
$id_cambio_salario = $modelocambio->id_cambio_salario;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $modelocambio);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Cambiosalario$modelocambio->id_cambio_salario.pdf", 'D');

exit;

