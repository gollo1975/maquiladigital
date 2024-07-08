<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "refinanciar_credito".
 *
 * @property int $id_financiacion
 * @property int $id_credito
 * @property int $id_operario
 * @property int $adicionar_valor
 * @property int $nuevo_saldo
 * @property int $numero_cuotas
 * @property int $numero_cuota_actual
 * @property string $fecha_registro
 * @property string $nota
 * @property string $user_name
 *
 * @property CreditoOperarios $credito
 * @property Operarios $operario
 */
class RefinanciarCredito extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'refinanciar_credito';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_credito', 'id_operario', 'adicionar_valor', 'nuevo_saldo', 'numero_cuotas', 'numero_cuota_actual','valor_cuota'], 'integer'],
            [['adicionar_valor', 'numero_cuotas', 'numero_cuota_actual','valor_cuota'], 'required'],
            [['fecha_registro'], 'safe'],
            [['nota'], 'string', 'max' => 100],
            [['user_name'], 'string', 'max' => 15],
            [['id_credito'], 'exist', 'skipOnError' => true, 'targetClass' => CreditoOperarios::className(), 'targetAttribute' => ['id_credito' => 'id_credito']],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_financiacion' => 'Id Financiacion',
            'id_credito' => 'Credito',
            'id_operario' => 'Operario',
            'adicionar_valor' => 'Nuevo valor:',
            'nuevo_saldo' => 'Nuevo Saldo',
            'numero_cuotas' => 'Total cuotas:',
            'numero_cuota_actual' => 'Cuota inicial:',
            'fecha_registro' => 'Fecha Registro',
            'valor_cuota' => 'Nuevo valor cuota:',
            'nota' => 'Nota:',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredito()
    {
        return $this->hasOne(CreditoOperarios::className(), ['id_credito' => 'id_credito']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperario()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
    }
}
