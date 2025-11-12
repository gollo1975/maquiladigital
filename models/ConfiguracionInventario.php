<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_inventario".
 *
 * @property int $id_configuracion
 * @property int $aplica_inventario_talla_color 0 = NO, 1 = SI
 * @property int $aplica_inventario_tallas 0 = NO, 1 = SI
 * @property int $aplica_solo_inventario 0 = NO, 1 = SI
 * @property int $aplica_iva_incluido 0 = NO, 1 = SI
 */
class ConfiguracionInventario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_inventario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_configuracion'], 'required'],
            [['id_configuracion', 'aplica_inventario_talla_color', 'aplica_inventario_tallas', 'aplica_solo_inventario', 'aplica_iva_incluido'], 'integer'],
            [['id_configuracion'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_configuracion' => 'Id Configuracion',
            'aplica_inventario_talla_color' => 'Aplica Inventario Talla Color',
            'aplica_inventario_tallas' => 'Aplica Inventario Tallas',
            'aplica_solo_inventario' => 'Aplica Solo Inventario',
            'aplica_iva_incluido' => 'Aplica Iva Incluido',
        ];
    }
}
