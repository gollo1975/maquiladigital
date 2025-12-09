<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_insumos".
 *
 * @property int $id_configuracion
 * @property int $aplica_insumos_referencia
 * @property int $aplica_insumos_ordenes
 */
class ConfiguracionInsumos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_insumos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aplica_insumos_referencia', 'aplica_insumos_ordenes'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_configuracion' => 'Id Configuracion',
            'aplica_insumos_referencia' => 'Aplica Insumos Referencia',
            'aplica_insumos_ordenes' => 'Aplica Insumos Ordenes',
        ];
    }
}
