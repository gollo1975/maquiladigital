<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_estampacion_app".
 *
 * @property int $id_configuracion
 * @property int $aplica_modulo_estampacion
 * @property int $cantidad_unidades_ingreso
 */
class ConfiguracionEstampacionApp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_estampacion_app';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_configuracion'], 'required'],
            [['id_configuracion', 'aplica_modulo_estampacion', 'cantidad_unidades_ingreso'], 'integer'],
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
            'aplica_modulo_estampacion' => 'Aplica Modulo Estampacion',
            'cantidad_unidades_ingreso' => 'Cantidad Unidades Ingreso',
        ];
    }
}
