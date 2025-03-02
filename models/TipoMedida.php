<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_medida".
 *
 * @property int $id_tipo_medida
 * @property string $medida
 * @property int $estado
 *
 * @property Insumos[] $insumos
 */
class TipoMedida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_medida';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['medida', 'estado'], 'required'],
            [['estado'], 'integer'],
            [['medida','abreviatura'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_medida' => 'Id Tipo Medida',
            'medida' => 'Medida',
            'estado' => 'Estado',
            'abreviatura' => 'abreviatura',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasMany(Insumos::className(), ['id_tipo_medida' => 'id_tipo_medida']);
    }
    
     public function getEstadomedida()
    {
        if($this->estado == 1){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
}
