<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_adicional_permanente".
 *
 * @property int $id_pago_permanente
 * @property int $id_empleado
 * @property int $codigo_salario
 * @property int $id_contrato
 * @property int $tipo_adicion
 * @property int $vlr_adicion
 * @property int $permanente
 * @property int $aplicar_dia_laborado
 * @property int $aplicar_prima
 * @property int $aplicar_cesantias
 * @property int $estado_registro
 * @property int $estado_periodo
 * @property string $detalle
 * @property string $fecha_creacion
 * @property string $usuariosistema
 */
class PagoAdicionalPermanente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_adicional_permanente';
    }
 public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->detalle = strtolower($this->detalle); 
        $this->detalle = ucfirst($this->detalle);  
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado', 'codigo_salario', 'vlr_adicion'], 'required'],
            [['id_empleado', 'codigo_salario', 'id_contrato', 'tipo_adicion', 'vlr_adicion', 'permanente', 'aplicar_dia_laborado', 'aplicar_prima', 'aplicar_cesantias',
                'estado_registro', 'estado_periodo','total_dia_prima','id_grupo_pago','homologar','codigo_salario_homologado','dias_contrato'], 'integer'],
            [['fecha_creacion','fecha_corte'], 'safe'],
            [['detalle'], 'string', 'max' => 50],
            [['usuariosistema'], 'string', 'max' => 30],
             [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_grupo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoPago::className(), 'targetAttribute' => ['id_grupo_pago' => 'id_grupo_pago']],
            [['id_pago_fecha'], 'exist', 'skipOnError' => true, 'targetClass' => PagoAdicionalFecha::className(), 'targetAttribute' => ['id_pago_fecha' => 'id_pago_fecha']],
            [['codigo_salario_homologado'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario_homologado' => 'codigo_salario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pago_permanente' => 'Id',
            'id_empleado' => 'Empleado',
            'codigo_salario' => 'Concepto salario',
            'id_contrato' => 'Nro Contrato',
            'id_grupo_pago' => 'Grupo pago',
            'tipo_adicion' => 'Tipo Adicion',
            'vlr_adicion' => 'Vlr Adicion',
            'permanente' => 'Permanente',
            'aplicar_dia_laborado' => 'Aplicar Dia Laborado',
            'aplicar_prima' => 'Aplicar Prima',
            'aplicar_cesantias' => 'Aplicar Cesantias',
            'estado_registro' => 'Estado Registro',
            'estado_periodo' => 'Estado Periodo',
            'detalle' => 'Detalle',
            'fecha_creacion' => 'Fecha Creacion',
            'usuariosistema' => 'Usuariosistema',
            'total_dia_prima' => 'total_dia_prima',
            'codigo_salario_homologado' => 'codigo_salario_homologado',
            'homologar' => 'homologar',
            'dias_contrato' => 'dias_contrato',
        ];
    }
    
     public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }
    
      public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }
    
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }
    
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
    
    public function getCodigoSalarioHomologar()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario_homologado']);
    }
    
    
    public function getAplicardialaborado(){
        if($this->aplicar_dia_laborado == 1){
          $aplicardialaborado = "SI";
        }else{
          $aplicardialaborado = "NO";
        }
        return $aplicardialaborado;
    }
    
    public function getAplicarPrima(){
        if($this->aplicar_prima == 1){
          $aplicarprima = "SI";
        }else{
          $aplicarprima = "NO";
        }
        return $aplicarprima;
    }
    
     public function getAplicarCesantia(){
        if($this->aplicar_cesantias == 1){
          $aplicarcesantia = "SI";
        }else{
          $aplicarcesantia = "NO";
        }
        return $aplicarcesantia;
    }
    
    public function getPermanentes(){
        if($this->permanente == 1){
          $permanente = "SI";
        }else{
          $permanente = "NO";
        }
        return $permanente;
    }
    
       public function getEstadoPeriodo(){
        if($this->estado_periodo == 1){
          $estadoperiodo = "SI";
        }else{
          $estadoperiodo = "NO";
        }
        return $estadoperiodo;
    }
    
     public function getEstadoRegistro(){
        if($this->estado_registro == 1){
          $estadoregistro = "SI";
        }else{
          $estadoregistro = "NO";
        }
        return $estadoregistro;
    }
    
     public function getDebitoCredito(){
        if($this->tipo_adicion == 1){
          $debitocredito = "+";
        }else{
          $debitocredito = "-";
        }
        return $debitocredito;
    }


}
