<?php

use inquid\pdf\FPDF;
use app\models\Contrato;
use app\models\Matriculaempresa;
use app\models\Municipio;
use app\models\Departamento;
use app\models\TipoContrato;
use app\models\GrupoPago;
use app\models\PeriodoPago;
use app\models\FormatoContenido;
use app\models\Empleado;

class PDF extends FPDF {

    function Header() {
        if ( $this->PageNo() == 1 ) {
            $id_contrato = $GLOBALS['id_contrato'];
            $contrato = Contrato::findOne($id_contrato);
            $config = Matriculaempresa::findOne(1);
            $municipio = Municipio::findOne($config->idmunicipio);
            $departamento = Departamento::findOne($config->iddepartamento);
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
            //$this->SetXY(10, 42);
            //$this->Cell(190, 7, utf8_decode("_________________________________________________________________________________________________"), 0, 0, 'C', 0);
        }                
    } 
    
    function Body($pdf, $modelocontrato) {
        $config = Matriculaempresa::findOne(1);
        $contrato = Contrato::findOne($modelocontrato->id_contrato);
        $tipo_contrato = TipoContrato::find()->where(['=','id_tipo_contrato', $contrato->id_tipo_contrato])->one();
        $formato = FormatoContenido::find()->where(['=','id_configuracion_prefijo', $tipo_contrato->id_configuracion_prefijo])->one();
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        if (!$formato){
           $cadena = "El contrato no tiene asociado un formato tipo contrato"; 
        } else {                          
            $cadena = utf8_decode($formato->contenido);            
        }

        //contenido de la cadena a sustituir
        $sustitucion1 = $contrato->empleado->identificacion;
        $sustitucion2 = utf8_decode($contrato->empleado->nombrecorto);
        $sustitucion3 = $contrato->empleado->ciudadExpedicion->municipio .' - '. $contrato->empleado->ciudadExpedicion->departamento->departamento;        
        $sustitucion4 = utf8_decode($contrato->empleado->direccion);
        $sustitucion5 = utf8_decode($contrato->empleado->barrio);
        $sustitucion6 = utf8_decode($contrato->cargo->cargo);
        $sustitucion7 = $this->numtoletras($contrato->salario);
        $sustitucion8 = '$ '.number_format($contrato->salario,2);
        $sustitucion9 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($contrato->empleado->fecha_nacimiento))) ." de %Y", strtotime($contrato->empleado->fecha_nacimiento));
        $sustituciona = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($contrato->fecha_inicio))) ." de %Y", strtotime($contrato->fecha_inicio));
        $sustitucionb = utf8_decode($contrato->grupoPago->periodoPago->nombre_periodo);
        $sustitucionc = $contrato->ciudadContratado->municipio .' - '. $contrato->ciudadContratado->departamento->departamento;    
        $sustitucione = $contrato->dias_contrato;
        $sustituciond = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($contrato->fecha_final))) ." de %Y", strtotime($contrato->fecha_final));
        $sustitucionw = utf8_decode($contrato->tipoContrato->formatoContenidos->nombre_formato);
        $sustitucionx = utf8_decode($contrato->id_contrato);
        //etiquetas de sustitución
        $patron1 = '/#1/'; //Documento                
        $patron2 = '/#2/'; //nombre empleado
        $patron3 = '/#3/'; //ciudad de expedicion
        $patron4 = '/#4/'; //direccion empleado
        $patron5 = '/#5/'; //Barrio empleado
        $patron6 = '/#6/'; //cargo empleado
        $patron7 = '/#7/'; //letras salario (ejempl DOS MILLONES DE PESOS)
        $patron8 = '/#8/'; //Salario (2.000.000.00)
        $patron9 = '/#9/'; //fecha nacimiento empleado
        $patrona = '/#a/'; //fecha inicio contrato
        $patronb = '/#b/'; //forma de pago nomina
        $patronc = '/#c/'; //Ciudad de contratacion
        $patrond = '/#d/'; //fecha de terminacion
        $patrone = '/#e/'; //dias contratados
        $patronw = '/#w/'; //nombre del formato
        $patronx = '/#x/'; //nro del contrato
       
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
        $cadenaCambiada = preg_replace($patronb, $sustitucionb, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patronc, $sustitucionc, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrond, $sustituciond, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrone, $sustitucione, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patronw, $sustitucionw, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patronx, $sustitucionx, $cadenaCambiada);
        $pdf->MultiCell(0,5, $cadenaCambiada);
    } 
      
   function Footer() {
        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode(''));
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
    
    public function numtoletras($xcifra) {
        
            $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
    //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO PESOS M/C";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN PESO M/C ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " PESOS M/C "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }
    
    public function subfijo($xx) { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;    
    }

}
global $id_contrato;
$id_contrato = $modelocontrato->id_contrato;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf,$modelocontrato);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("Contrato$modelocontrato->id_contrato.pdf", 'D');

exit;