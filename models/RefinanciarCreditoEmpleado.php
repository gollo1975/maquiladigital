<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "refinanciar_credito_empleado".
 *
 * @property int $id_financiacion
 * @property int $id_credito
 * @property int $id_empleado
 * @property int $adicionar_valor
 * @property int $nuevo_saldo
 * @property int $numero_cuotas
 * @property int $numero_cuota_actual
 * @property int $valor_cuota
 * @property string $fecha_registro
 * @property string $nota
 * @property string $user_name
 *
 * @property Credito $credito
 * @property Empleado $empleado
 */
class RefinanciarCreditoEmpleado extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'refinanciar_credito_empleado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_credito', 'id_empleado', 'adicionar_valor', 'nuevo_saldo', 'numero_cuotas', 'numero_cuota_actual', 'valor_cuota'], 'integer'],
            [['adicionar_valor', 'numero_cuotas', 'numero_cuota_actual', 'valor_cuota'], 'required'],
            [['fecha_registro'], 'safe'],
            [['nota'], 'string', 'max' => 100],
            [['user_name'], 'string', 'max' => 15],
            [['id_credito'], 'exist', 'skipOnError' => true, 'targetClass' => Credito::className(), 'targetAttribute' => ['id_credito' => 'id_credito']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_financiacion' => 'Id Financiacion',
            'id_credito' => 'Id Credito',
            'id_empleado' => 'Id Empleado',
            'adicionar_valor' => 'Adicionar Valor',
            'nuevo_saldo' => 'Nuevo Saldo',
            'numero_cuotas' => 'Numero Cuotas',
            'numero_cuota_actual' => 'Numero Cuota Actual',
            'valor_cuota' => 'Valor Cuota',
            'fecha_registro' => 'Fecha Registro',
            'nota' => 'Nota',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredito()
    {
        return $this->hasOne(Credito::className(), ['id_credito' => 'id_credito']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }
}
