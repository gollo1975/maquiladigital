<?php

use inquid\pdf\FPDF;
use app\models\FormatoContratoObralabor;
use app\models\Contrato;
use app\models\FormatoContenido;
use app\models\Municipio;
use app\models\Departamento;
use app\models\Matriculaempresa;
use app\models\Empleado;

class PDF extends FPDF {

   function Header() {        
        $config = Matriculaempresa::findOne(1);        
        // Logo
        $this->Image('dist/images/logos/logomaquila.jpeg', 10, 10, 30, 19);
        // Encabezado
        $this->SetFont('Arial', '', 9);
        $this->SetXY(53, 9);
        $this->Cell(150, 7, utf8_decode($config->razonsocialmatricula), 0, 0, 'C', 0);
        $this->SetXY(53, 13.5);
        $this->Cell(150, 7, utf8_decode(" NIT:" .$config->nitmatricula." - ".$config->dv), 0, 0, 'C', 0);
        $this->SetXY(53, 18);
        $this->Cell(150, 7, utf8_decode($config->direccionmatricula. " Teléfono: " .$config->telefonomatricula), 0, 0, 'C', 0);
        $this->SetXY(53, 23);
        $this->Cell(150, 7, utf8_decode($config->municipio->municipio." - ".$config->departamento->departamento), 0, 0, 'C', 0);
        $this->Ln(15);
    }

   
    function Body($pdf, $model) {
        $config = Matriculaempresa::findOne(1);
        $model = FormatoContratoObralabor::findOne($model->id);
        $contrato = Contrato::findOne($model->id_contrato);
        $formato = FormatoContenido::findOne($model->id_formato_contenido);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetX(10);
        
        if (!$formato){
           $cadena = "El contrato no tiene asociado un formato tipo contrato"; 
        } else {                           
            $cadena = utf8_decode($formato->contenido);            
        }
        
        // Sustituciones
        $sustitucion1 = $contrato->empleado->identificacion;
        $sustitucion2 = utf8_decode($contrato->empleado->nombrecorto);
        $sustitucion3 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($model->fecha_inicio_periodo))) ." de %Y", strtotime($model->fecha_inicio_periodo));
        $sustitucion4 = '$('.number_format($model->id_ingreso,2).')'; // Ajusta según tu campo de valor
        $sustitucion5 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($model->fecha_corte_labor))) ." de %Y", strtotime($model->fecha_corte_labor));
        $sustitucion6 = '$('.number_format($model->id_ingreso,2).')';
        $sustitucion7 = $this->numtoletras($model->id_ingreso);
        $sustitucion8 = strftime("%d de ". $this->MesesEspañol(date('m',strtotime($model->fecha_hora_creacion))) ." de %Y", strtotime($model->fecha_hora_creacion));
        $sustituciona = $contrato->empleado->ciudadExpedicion->municipio .' - '. $contrato->empleado->ciudadExpedicion->departamento->departamento;        
        $sustitucionb = $config->razonsocialmatricula;
        $sustitucionc = $config->nitmatricula;
        $sustituciond = $model->id;
        $sustitucione = $config->cedula_representante_legal;

        // Patrones
        $patron1 = '/#1/'; $patron2 = '/#2/'; $patron3 = '/#3/'; $patron4 = '/#4/';
        $patron5 = '/#5/'; $patron6 = '/#6/'; $patron7 = '/#7/'; $patron8 = '/#8/'; $patrona = '/#a/';
        $patronb = '/#b/'; $patronc = '/#c/'; $patrond = '/#d/'; $patrone = '/#e/';

        // Reemplazos
        $cadenaCambiada = preg_replace($patron1, $sustitucion1, $cadena);
        $cadenaCambiada = preg_replace($patron2, $sustitucion2, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron3, $sustitucion3, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron4, $sustitucion4, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron5, $sustitucion5, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron6, $sustitucion6, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patron7, $sustitucion7, $cadenaCambiada);
         $cadenaCambiada = preg_replace($patron8, $sustitucion8, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrona, $sustituciona, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patronb, $sustitucionb, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patronc, $sustitucionc, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrond, $sustituciond, $cadenaCambiada);
        $cadenaCambiada = preg_replace($patrone, $sustitucione, $cadenaCambiada);

        // Imprimir Texto
        $pdf->MultiCell(0,5, $cadenaCambiada);
        $detalles = (new \yii\db\Query())
            ->from('ingreso_personal_contrato_detalle')
            ->where(['id_ingreso' => $model->id_ingreso])
            ->andWhere(['id_empleado' => $model->id_empleado])    
            ->all();
        // --- INSERCIÓN DE LA TABLA ---
        if ($detalles) {
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(250, 250, 250); // Un gris muy tenue para diferenciar
            // 190 es la suma de los anchos: 80 + 30 + 40 + 40
            $pdf->Cell(190, 7, utf8_decode('ACTIVIDADES CONTRATADAS'), 1, 1, 'C', 1);
            $this->EncabezadoDetalles($pdf); // Imprime CÓDIGO, OPERACIÓN, etc.
            
            // --- NUEVA FILA: ACTIVIDADES CONTRATADAS ---
            
            
            // --- DATOS DE LA TABLA ---
            $pdf->SetFont('Arial', '', 8);
            $w = array(80, 30, 40, 40); 
            $granTotal = 0;

            foreach ($detalles as $fila) {
                $pdf->Cell($w[0], 5, utf8_decode($fila['operacion']), 1, 0, 'L');
                $pdf->Cell($w[1], 5, $fila['cantidad'], 1, 0, 'C');
                $pdf->Cell($w[2], 5, number_format($fila['valor_unitario'], 2), 1, 0, 'R');
                $pdf->Cell($w[3], 5, number_format($fila['total_pagar'], 2), 1, 0, 'R');
                $pdf->Ln();
                
                $granTotal += $fila['total_pagar'];
            }

            // --- FILA DE GRAN TOTAL ---
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(150, 7, 'TOTAL VALOR CONTRATO: ', 1, 0, 'R', 1);
            $pdf->Cell($w[3], 7, '$ ' . number_format($granTotal, 2), 1, 0, 'R', 1);
            $pdf->Ln(15);
            
            $this->SeccionFirmas($pdf, $config, $contrato);
        }
        // -----------------------------
    }                    
    function SeccionFirmas($pdf, $config, $contrato) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln(16); // Espacio para que puedan firmar físicamente

        // --- FILA 1: Las líneas de firma ---
        $pdf->Cell(95, 5, '__________________________________', 0, 0, 'C'); // 0 = sigue en la misma línea
        $pdf->Cell(95, 5, '__________________________________', 0, 1, 'C'); // 1 = baja a la siguiente línea

        // --- FILA 2: Los nombres ---
        $pdf->Cell(95, 5, utf8_decode($config->razonsocialmatricula), 0, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode($contrato->empleado->nombrecorto), 0, 1, 'C');

        // --- FILA 3: Los títulos (EMPLEADOR y TRABAJADOR) ---
        $pdf->Cell(95, 5, utf8_decode('EMPLEADOR'), 0, 0, 'C');
        $pdf->Cell(95, 5, utf8_decode('TRABAJADOR'), 0, 1, 'C');

        // --- FILA 4: Documentos de identidad ---
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(95, 5, 'NIT: ' . $config->nitmatricula . " - " . $config->dv, 0, 0, 'C');
        $pdf->Cell(95, 5, 'C.C. ' . $contrato->empleado->identificacion, 0, 1, 'C');
       }
    
   function EncabezadoDetalles($pdf) {
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('Arial', 'B', 8);
        $header = array(utf8_decode('OPERACIÓN'), 'CANTIDAD', 'VLR UNITARIO', 'TOTAL PAGAR');
        $w = array(80, 30, 40, 40); 

        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();
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

global $codigo;
$codigo = $model->id;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $model);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("otro_si_labor$model->id.pdf", 'D');

exit;

