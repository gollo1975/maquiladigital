<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "valor_prenda_unidad_detalles".
 *
 * @property int $consecutivo
 * @property int $id_operario
 * @property int $idordenproduccion
 * @property string $dia_pago
 * @property int $cantidad
 * @property int $vlr_prenda
 * @property int $vlr_pago
 * @property int $id_valor
 * @property string $fecha_creacion
 * @property string $usuariosistema
 * @property string $observacion
 *
 * @property Operarios $operario
 * @property Ordenproduccion $ordenproduccion
 * @property ValorPrendaUnidad $valor
 */
class ValorPrendaUnidadDetalles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'valor_prenda_unidad_detalles';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtolower($this->observacion); 
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operario', 'idordenproduccion', 'cantidad', 'vlr_prenda', 'vlr_pago', 'id_valor','registro_pagado','exportado','meta_diaria','costo_dia_operaria',
                'control_fecha','aplica_regla','aplica_sabado','id_planta','id_tipo','aplicar_porcentaje','iddetalleorden','idproceso','alimentacion','hora_descontar'], 'integer'],
            [['dia_pago', 'fecha_creacion'], 'safe'],
            [['porcentaje_cumplimiento'], 'number'],
            [['usuariosistema', 'observacion','hora_inicio_modulo'], 'string', 'max' => 20],
            [['operacion'], 'string', 'max' => 1],
            [['hora_inicio', 'hora_corte'], 'string'],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['id_valor'], 'exist', 'skipOnError' => true, 'targetClass' => ValorPrendaUnidad::className(), 'targetAttribute' => ['id_valor' => 'id_valor']],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['id_tipo' => 'idtipo']],
            [['iddetalleorden'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciondetalle::className(), 'targetAttribute' => ['iddetalleorden' => 'iddetalleorden']],
            [['idproceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['idproceso' => 'idproceso']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'consecutivo' => 'Consecutivo',
            'id_operario' => 'Operario:',
            'idordenproduccion' => 'Op Interna:',
            'dia_pago' => 'Dia Pago',
            'cantidad' => 'Cantidad',
            'vlr_prenda' => 'Vlr Prenda',
            'vlr_pago' => 'Vlr Pago',
            'id_valor' => 'Id Valor',
            'fecha_creacion' => 'Fecha Creacion',
            'usuariosistema' => 'Usuariosistema',
            'observacion' => 'Observacion',
            'operacion' => 'Operacion',
            'registro_pagado' => 'Registro pago',
            'porcentaje_cumplimiento' => '% cumplimiento',
            'control_fecha' => 'Control fecha',
            'aplica_regla'=> 'aplica_regla',
            'aplica_sabado' =>'aplica_sabado',
            'aplicar_porcentaje' => 'aplicar_porcentaje',
            'idproceso' => 'idproceso',
            'iddetalleorden' => 'Tallas:',
            'hora_inicio' => 'Hora inicio:',
            'hora_corte' => 'Hora corte:',
            'alimentacion' => 'alimentacion',
            'hora_descontar' => 'hora_descontar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
      public function getOperarioProduccion()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
    }
    
     public function getTipoproceso()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'id_tipo']);
    }
    
     public function getDetalleOrdenProduccion()
    {
        return $this->hasOne(Ordenproducciondetalle::className(), ['iddetalleorden' => 'iddetalleorden']);
    }
    
     public function getOperaciones()
    {
        return $this->hasOne(ProcesoProduccion::className(), ['idproceso' => 'idproceso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValor()
    {
        return $this->hasOne(ValorPrendaUnidad::className(), ['id_valor' => 'id_valor']);
    }
    
     public function getPlanta()
    {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }
    public function getOperacionPrenda(){
        if($this->operacion == 1){
            $operacionprenda = 'CONFECCION';
        }else{
            if($this->operacion == 2){
                $operacionprenda = 'OPERACION';
            }else{
                $operacionprenda = 'AJUSTE';
            }
        }
        return $operacionprenda;
    }
    
     public function getRegistroPagado(){
        if($this->registro_pagado == 1){
            $registropagado = 'SI';
        }else{
                $registropagado = 'NO';
        }
        return $registropagado;
    }
    
    public function getRegistroExportado(){
        if($this->exportado == 1){
            $exportado = 'SI';
        }else{
                $exportado = 'NO';
        }
        return $exportado;
    }
     public function getAplicaSabado(){
        if($this->aplica_sabado == 1){
            $aplica_sabado = 'SI';
        }else{
                $aplica_sabado = 'NO';
        }
        return $aplica_sabado;
    }
    
    public function getAplicaPorcentaje(){
        if($this->aplicar_porcentaje == 0){
            $aplicarporcentaje = 'NO';
        }else{
                $aplicarporcentaje = 'SI';
        }
        return $aplicarporcentaje;
    }
    
    public function getOperario()
{
    // Assuming the foreign key in ValorPrendaUnidadDetalles is 'id_operario'
    // and the primary key in the Operario model is also 'id_operario'
    return $this->hasOne(Operarios::class, ['id_operario' => 'id_operario']);
}
}
