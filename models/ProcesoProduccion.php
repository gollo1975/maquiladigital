<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proceso_produccion".
 *
 * @property int $idproceso
 * @property string $proceso
 * @property int $estado
 *
 * @property Ordenproducciondetalleproceso[] $ordenproducciondetalleprocesos
 */
class ProcesoProduccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proceso_produccion';
    }

    public function beforeSave($insert) {
        if(!parent::beforeSave($insert)){
            return false;
        }
        $this->proceso = strtoupper($this->proceso);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proceso','estandarizado'], 'required','message'=> 'Campo obligatorio'],
            [['estado','estandarizado','id_tipo_producto','id_tipo'], 'integer'],
            [['segundos','minutos'], 'number'],
            [['proceso'], 'string', 'max' => 50],
            [['id_tipo_producto'], 'exist', 'skipOnError' => true, 'targetClass' => TipoProducto::className(), 'targetAttribute' => ['id_tipo_producto' => 'id_tipo_producto']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idproceso' => 'Id',
            'proceso' => 'Nombre de la operación:',
            'estado' => 'Tipo de tiempo:',
            'segundos' => 'Sam segundos:',
            'minutos' => 'Sam minutos:',
            'estandarizado' => 'Estandarizado:',
            'id_tipo_producto' => 'Tipo de producto:',
            'id_tipo' => 'Tipo de maquina:',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproducciondetalleprocesos()
    {
        return $this->hasMany(Ordenproducciondetalleproceso::className(), ['idproceso' => 'idproceso']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoMaquina()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }
    
    public function getTipoProducto()
        {
            return $this->hasOne(TipoProducto::class, ['id_tipo_producto' => 'id_tipo_producto']);
        }
    
    public function getEstandar(){
        if ($this->estandarizado == 1){
            $estandarizado = 'SI';
        }else{
            $estandarizado = 'NO';
        }
        return $estandarizado;
    } 
     public function getActivoRegistro(){
        if ($this->estado == 0){
            $estadoregistro = 'SI';
        }else{
            $estadoregistro = 'NO';
        }
        return $estadoregistro;
    } 
}
