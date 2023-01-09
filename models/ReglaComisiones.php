<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regla_comisiones".
 *
 * @property int $id_regla
 * @property string $concepto
 * @property double $porcentaje_cumplimiento
 * @property int $valor_ minuto_contrato
 * @property int $valor_ minuto_vinculado
 * @property int $estado_regla
 */
class ReglaComisiones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regla_comisiones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['porcentaje_cumplimiento'], 'number'],
            [['valor_minuto_contrato', 'valor_minuto_vinculado', 'estado_regla'], 'integer'],
            [['concepto'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_regla' => 'Id Regla',
            'concepto' => 'Concepto',
            'porcentaje_cumplimiento' => 'Porcentaje Cumplimiento',
            'valor_minuto_contrato' => 'Valor Minuto Contrato',
            'valor_minuto_vinculado' => 'Valor Minuto Vinculado',
            'estado_regla' => 'Estado Regla',
        ];
    }
}
