<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cartera_empresa".
 *
 * @property int $id_cartera
 * @property string $fecha_vencimiento
 * @property int $numero_factura
 * @property int $dias_adicionales
 * @property int $estado_registro
 */
class CarteraEmpresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cartera_empresa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cartera'], 'required'],
            [['id_cartera', 'numero_factura', 'dias_adicionales', 'estado_registro'], 'integer'],
            [['fecha_vencimiento'], 'safe'],
            [['id_cartera'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_cartera' => 'Id Cartera',
            'fecha_vencimiento' => 'Fecha Vencimiento',
            'numero_factura' => 'Numero Factura',
            'dias_adicionales' => 'Dias Adicionales',
            'estado_registro' => 'Estado Registro',
        ];
    }
}
