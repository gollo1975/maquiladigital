<?php

use inquid\pdf\FPDF;
use app\models\ProrrogaContrato;
use app\models\Contrato;
use app\models\FormatoContenido;
use app\models\Municipio;
use app\models\Departamento;
use app\models\Matriculaempresa;

class PDF extends FPDF {

    function Header() {        
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

    function Body($pdf, $id_prorroga_contrato) {
    $config = Matriculaempresa::findOne(1);
    $model = ProrrogaContrato::findOne($id_prorroga_contrato);
    $contrato = Contrato::findOne($model->id_contrato);
    $formato = FormatoContenido::findOne($model->id_formato_contenido);

    if (!$formato) return;

    // 1. DATOS LIMPIOS
    $nombre = trim(utf8_decode($contrato->empleado->nombrecorto));
    $identificacion = trim($contrato->empleado->identificacion);
    $ciudadExp = trim(utf8_decode($contrato->empleado->ciudadExpedicion->municipio));
    $cargo = trim(utf8_decode($contrato->cargo->cargo));
    $empresa = trim(utf8_decode($config->razonsocialmatricula));
    $nit = trim($config->nitmatricula);
    $fecha_final = date('d', strtotime($model->fecha_hasta)) . " de " . self::MesesEspañol(date('m', strtotime($model->fecha_hasta))) . " de " . date('Y', strtotime($model->fecha_hasta));
    $fechaHoy = $config->municipio->municipio.', '. date('d', strtotime($model->fecha_creacion)) . " de " . self::MesesEspañol(date('m', strtotime($model->fecha_creacion))) . " de " . date('Y', strtotime($model->fecha_creacion));
    
  // BUSCAMO QUE NO SEA VACIA
    $timestamp = strtotime($model->fecha_notificacion);
    // Si strtotime falla, usamos la fecha actual o una por defecto
    if (!$timestamp) {
        $timestamp = time(); 
    }
    
    // 2. POSICIÓN INICIAL
    $pdf->Ln(20); 
    $pdf->SetFont('Arial', '', 10);

    // --- 3. ENCABEZADO MANUAL (Esto es lo que SI queremos) ---
    $pdf->SetX(10);
    $pdf->Cell(0, 6, $fechaHoy, 0, 1, 'L');
    $pdf->Cell(0, 6, utf8_decode("Señor (a)"), 0, 1, 'L');
    $pdf->Cell(0, 6, $nombre, 0, 1, 'L');
    $pdf->Cell(0, 6, "Documento: " . $identificacion . " de " . $ciudadExp, 0, 1, 'L');
    $pdf->Cell(0, 6, $cargo, 0, 1, 'L');

    $pdf->Ln(5);

    // --- 4. TRUCO FINAL: IGNORAR TODO LO ANTERIOR AL ASUNTO ---
    // Buscamos la posición de la palabra "Asunto:" en el texto original
    $contenidoOriginal = $formato->contenido;
    $posAsunto = stripos($contenidoOriginal, 'Asunto:');

    if ($posAsunto !== false) {
        // Cortamos el texto: solo nos quedamos desde "Asunto:" hacia adelante
        $soloCuerpo = substr($contenidoOriginal, $posAsunto);
    } else {
        $soloCuerpo = $contenidoOriginal;
    }

    // 5. REEMPLAZOS SOLO EN EL CUERPO RESTANTE
    $reemplazos = [
      
        '/#5/' => $empresa,
        '/#6/' => $nit,
        '/#7/' => $contrato->id_contrato,
        '/#8/' => date('d', strtotime($model->fecha_ultima_contrato)) . " de " . self::MesesEspañol(date('m', strtotime($model->fecha_ultima_contrato))) . " de " . date('Y', strtotime($model->fecha_ultima_contrato)),
        '/#9/' => $model->dias_contratados,
        '/#a/' => date('d', $timestamp) . " de " . self::MesesEspañol(date('m', $timestamp)) . " de " . date('Y', $timestamp),
        '/#b/' => date('d', strtotime($model->fecha_nueva_renovacion)) . " de " . self::MesesEspañol(date('m', strtotime($model->fecha_nueva_renovacion))) . " de " . date('Y', strtotime($model->fecha_nueva_renovacion)),
        '/#d/' => $fecha_final,
    ];
    
    $textoFinal = utf8_decode(preg_replace(array_keys($reemplazos), array_values($reemplazos), $soloCuerpo));

    // 6. IMPRESIÓN DEL CUERPO (Asunto con línea y resto justificado)
    $partes = preg_split('/(Asunto:.*?\.)/i', $textoFinal, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($partes as $bloque) {
        $bloque = trim($bloque);
        if (empty($bloque)) continue;

        $pdf->SetX(10);
        if (stripos($bloque, 'Asunto:') === 0) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(190, 5, $bloque, 0, 'L');
            $pdf->Line(10, $pdf->GetY() + 1, 200, $pdf->GetY() + 1);
            $pdf->Ln(6);
            $pdf->SetFont('Arial', '', 10);
        } else {
            $pdf->MultiCell(190, 5, $bloque, 0, 'J');
            $pdf->Ln(4);
        }
    }

    // --- 7. FIRMAS ---
    $pdf->Ln(22);
    $y = $pdf->GetY();
    if($y > 240) { $pdf->AddPage(); $y = $pdf->GetY() + 10; }
    $pdf->Line(15, $y, 85, $y); $pdf->Line(115, $y, 185, $y);
    $pdf->SetXY(15, $y + 2);
    $pdf->MultiCell(70, 4, $empresa . "\nNit: " . $nit . "-2\nEMPLEADOR", 0, 'L');
    $pdf->SetXY(115, $y + 2);
    $pdf->MultiCell(70, 4, $nombre . "\nDocumento " . $identificacion . "\nEL TRABAJADOR", 0, 'L');
}                

    function Footer() {

        $this->SetFont('Arial', '', 8);
        $this->Text(10, 290, utf8_decode('Nuestra compañía, en favor del medio ambiente.'));
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }
    
   public static function MesesEspañol($mes) {
        $meses = [
            '01' => "Enero", '02' => "Febrero", '03' => "Marzo", '04' => "Abril",
            '05' => "Mayo", '06' => "Junio", '07' => "Julio", '08' => "Agosto",
            '09' => "Septiembre", '10' => "Octubre", '11' => "Noviembre", '12' => "Diciembre"
        ];
        return $meses[$mes] ?? "Mes inválido";
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

global $id_prorroga_contrato;
$id_prorroga_contrato = $modeloprorroga->id_prorroga_contrato;
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Body($pdf, $id_prorroga_contrato);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 10);
$pdf->Output("prorroga$modeloprorroga->id_prorroga_contrato.pdf", 'D');

exit;

