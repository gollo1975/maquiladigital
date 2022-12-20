<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comprobante_egreso_tipo".
 *
 * @property int $id_comprobante_egreso_tipo
 * @property string $concepto
 * @property int $activo
 *
 * @property ComprobanteEgreso[] $comprobanteEgresos
 */
class ComprobanteEgresoTipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comprobante_egreso_tipo';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->concepto = strtoupper($this->concepto);		
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['activo','permite_importar'], 'integer'],
            [['concepto'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_comprobante_egreso_tipo' => 'Id',
            'concepto' => 'Concepto',
            'activo' => 'Activo',
            'permite_importar' => 'Permite importar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComprobanteEgresos()
    {
        return $this->hasMany(ComprobanteEgreso::className(), ['id_comprobante_egreso_tipo' => 'id_comprobante_egreso_tipo']);
    }
    
    public function getEstado()
    {
        if($this->activo == 0){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
      public function getImportar()
    {
        if($this->permite_importar == 0){
            $importar = "NO";
        }else{
            $importar = "SI";
        }
        return $importar;
    }
}
