<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "concepto_salarios".
 *
 * @property int $codigo_salario
 * @property string $nombre_concepto
 * @property int $compone_salario
 * @property int $aplica_porcentaje
 * @property double $porcentaje
 * @property double $porcentaje_tiempo_extra
 * @property int $prestacional
 * @property int $ingreso_base_prestacional
 * @property int $ingreso_base_cotizacion
 * @property int $debito_credito
 * @property int $adicion
 * @property int $auxilio_transporte
 * @property int $concepto_incapacidad
 * @property int $concepto_pension
 * @property int $concepto_salud
 * @property int $concepto_vacacion
 * @property int $provisiona_vacacion
 * @property int $tipo_adicion
 * @property int $recargo_nocturno
 * @property string $fecha_creacion
 *
 * @property ConfiguracionPension[] $configuracionPensions
 */
class ConceptoSalarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'concepto_salarios';
    }
 public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre_concepto = strtoupper($this->nombre_concepto);        
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_salario', 'nombre_concepto', 'debito_credito', 'tipo_adicion'], 'required'],
            [['codigo_salario', 'compone_salario', 'aplica_porcentaje', 'prestacional', 'ingreso_base_prestacional', 'ingreso_base_cotizacion', 'debito_credito', 'adicion', 'auxilio_transporte', 'concepto_incapacidad', 'concepto_pension', 'concepto_salud', 'concepto_vacacion',
                'provisiona_vacacion', 'provisiona_indemnizacion', 'tipo_adicion', 'recargo_nocturno', 'inicio_nomina','hora_extra','concepto_comision','concepto_licencia', 'fsp','concepto_prima', 'concepto_cesantias', 'intereses','id_agrupado','devengado_deduccion'], 'integer'],
            [['porcentaje', 'porcentaje_tiempo_extra'], 'number'],
            [['fecha_creacion'], 'safe'],
            [['nombre_concepto'], 'string', 'max' => 50],
            [['codigo_salario'], 'unique'],
            [['id_agrupado'], 'exist', 'skipOnError' => true, 'targetClass' => AgruparConceptoSalario::className(), 'targetAttribute' => ['id_agrupado' => 'id_agrupado']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo_salario' => 'Codigo Salario',
            'nombre_concepto' => 'Nombre Concepto',
            'compone_salario' => 'Compone Salario',
             'inicio_nomina' => 'Inicio nomina',
            'aplica_porcentaje' => 'Aplica Porcentaje',
            'porcentaje' => 'Porcentaje',
            'porcentaje_tiempo_extra' => 'Porcentaje Tiempo Extra',
            'prestacional' => 'Prestacional',
            'ingreso_base_prestacional' => 'IBP',
            'ingreso_base_cotizacion' => 'IBC',
            'debito_credito' => 'Debito Credito',
            'adicion' => 'Adicion',
            'auxilio_transporte' => 'Auxilio Transporte',
            'concepto_incapacidad' => 'Concepto Incapacidad',
            'concepto_pension' => 'Concepto Pension',
            'concepto_salud' => 'Concepto Salud',
            'concepto_vacacion' => 'Concepto Vacacion',
            'provisiona_vacacion' => 'Provisiona Vacacion',
             'provisiona_indemnizacion' => 'Provisiona indemnizacion',
            'tipo_adicion' => 'Tipo Adicion',
            'recargo_nocturno' => 'Recargo Nocturno',
            'hora_extra' => 'Hora extra',
            'concepto_comision' => 'Comisión',
            'concepto_licencia' => 'Licencia',
            'fecha_creacion' => 'Fecha Creacion',
            'fsp' => 'FSP',
            'concepto_prima' => 'Prima',
            'concepto_cesantias' => 'Cesantias',
            'intereses' => 'Intereses',
            'devengado_deduccion' => 'Devengado / Deduccion:',
            'id_agrupado' => 'Tipo de concepto:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracionPension()
    {
        return $this->hasMany(ConfiguracionPension::className(), ['codigo_salario' => 'codigo_salario']);
    }
    public function getCompone()
    {
        if($this->compone_salario == 1){
            $compone = "SI";
        }else{
            $compone = "NO";
        }
        return $compone;
    }
     public function getAplicaPorcentaje()
    {
        if($this->aplica_porcentaje == 1){
            $aplica = "SI";
        }else{
            $aplica = "NO";
        }
        return $aplica;
    }
     public function getPrestacion()
    {
        if($this->prestacional == 1){
            $prestacion = "SI";
        }else{
            $prestacion = "NO";
        }
        return $prestacion;
    }
    public function getIbpPrestacion()
    {
        if($this->ingreso_base_prestacional == 1){
            $ibpprestacion = "SI";
        }else{
            $ibpprestacion = "NO";
        }
        return $ibpprestacion;
    }
    public function getDebitocredito()
    {
        if($this->debito_credito == 1){
            $debitocredito = "SUMA";
        }elseif ($this->debito_credito == 2){
            $debitocredito = "RESTA";
        }else{
            $debitocredito = "NEUTRO";
        }
        
        return $debitocredito;
    }
    public function getAdicion()
    {
        if($this->adicion == 1){
            $adicion = "SI";
        }else{
            $adicion = "NO";
        }
        return $adicion;
    }
    public function getAuxilioTransporte()
    {
        if($this->auxilio_transporte == 1){
            $auxiliotransporte = "SI";
        }else{
            $auxiliotransporte = "NO";
        }
        return $auxiliotransporte;
    }
    public function getconceptoIncapacidad()
    {
        if($this->concepto_incapacidad == 1){
            $conceptoincapacidad= "SI";
        }else{
            $conceptoincapacidad = "NO";
        }
        return $conceptoincapacidad;
    }
     public function getconceptoPension()
    {
        if($this->concepto_pension == 1){
            $conceptopension= "SI";
        }else{
            $conceptopension = "NO";
        }
        return $conceptopension;
    }
      public function getconceptoSalud()
    {
        if($this->concepto_salud == 1){
            $conceptosalud= "SI";
        }else{
            $conceptosalud = "NO";
        }
        return $conceptosalud;
    }
      public function getconceptoVacacion()
    {
        if($this->concepto_vacacion == 1){
            $conceptovacacion= "SI";
        }else{
            $conceptovacacion = "NO";
        }
        return $conceptovacacion;
    }
     public function getprovisionaVacacion()
    {
        if($this->provisiona_vacacion == 1){
            $provisionavacacion= "SI";
        }else{
            $provisionavacacion = "NO";
        }
        return $provisionavacacion;
    }
     public function getprovisionaIndemnizacion()
    {
        if($this->provisiona_indemnizacion == 1){
            $provisionaindemnizacion= "SI";
        }else{
            $provisionaindemnizacion = "NO";
        }
        return $provisionaindemnizacion;
    }
    public function getTipoAdicion()
    {
        if($this->tipo_adicion == 1){
            $tipoadicion = "BONIFICACION";
        }elseif ($this->tipo_adicion == 2){
            $tipoadicion = "DESCUENTO";
        }else{
            $tipoadicion = "NO APLICA";
        }
        
        return $tipoadicion;
    }
     public function getrecargoNocturno()
    {
        if($this->recargo_nocturno == 1){
            $recargonocturno= "SI";
        }else{
            $recargonocturno = "NO";
        }
        return $recargonocturno;
    }
      public function getInicionomina()
    {
        if($this->inicio_nomina == 1){
            $inicionomina= "SI";
        }else{
            $inicionomina = "NO";
        }
        return $inicionomina;
    }
    public function getComision()
    {
        if($this->concepto_comision == 1){
            $comision= "SI";
        }else{
            $comision = "NO";
        }
        return $comision;
    }
    public function getConceptolicencia()
    {
        if($this->concepto_licencia == 1){
            $conceptolicencia= "SI";
        }else{
            $conceptolicencia = "NO";
        }
        return $conceptolicencia;
    }
      public function getFondoSP()
    {
        if($this->fsp == 1){
            $fondosp= "SI";
        }else{
            $fondosp = "NO";
        }
        return $fondosp;
    }
}
