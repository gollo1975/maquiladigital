<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parametros".
 *
 * @property int $id_parametros
 * @property double $auxilio_transporte
 * @property double $pension
 * @property double $caja
 * @property double $prestaciones
 * @property double $vacaciones
 * @property double $ajuste
 * @property double $salario_minimo
 * @property int $id_arl
 * @property int $admon
 * @property float $porcentaje_empleado
 *
 * @property Arl $arl
 */
class Parametros extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parametros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auxilio_transporte', 'pension', 'caja', 'prestaciones', 'vacaciones', 'ajuste', 'salario_minimo', 'id_arl','admon','valor_dia_empleado'], 'required'],
            [['auxilio_transporte', 'pension', 'caja', 'prestaciones', 'vacaciones', 'ajuste', 'salario_minimo','admon'], 'number'],
            [['valor_dia_empleado'],'integer'],
            [['id_arl'], 'integer'],
            [['id_arl'], 'exist', 'skipOnError' => true, 'targetClass' => Arl::className(), 'targetAttribute' => ['id_arl' => 'id_arl']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_parametros' => 'Id Parametros',
            'auxilio_transporte' => 'Auxilio Transporte:',
            'pension' => 'Pensión:',
            'caja' => 'Caja:',
            'prestaciones' => 'Prestaciones:',
            'vacaciones' => 'Vacaciones:',
            'ajuste' => 'Ajuste:',
            'salario_minimo' => 'Salario Minimo:',
            'id_arl' => 'Arl:',
            'admon' => 'Administración:',
            'valor_dia_empleado' => 'Valor dia empleado:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArl()
    {
        return $this->hasOne(Arl::className(), ['id_arl' => 'id_arl']);
    }
}
