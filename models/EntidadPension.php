<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entidad_pension".
 *
 * @property int $id_entidad_pension
 * @property string $entidad
 * @property int $estado
 *
 * @property Contrato[] $contratos
 */
class EntidadPension extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entidad_pension';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->entidad = strtoupper($this->entidad);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entidad'], 'required'],
            [['estado'], 'integer'],
            [['entidad'], 'string', 'max' => 100],
            [['codigo_api_nomina'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entidad_pension' => 'Id Entidad Pension',
            'entidad' => 'Entidad',
            'estado' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['id_entidad_pension' => 'id_entidad_pension']);
    }
    
    public function getActivo()
    {
        if($this->estado == 1){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
}
