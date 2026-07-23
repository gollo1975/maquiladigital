<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "devolucion_aportes".
 *
 * @property int $id_devolucion
 * @property int $id_empleado
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property string $fecha_hora_registro
 * @property string $total_devolucion
 * @property string $user_name
 * @property int $codigo_salario
 *
 * @property Empleado $empleado
 * @property ConceptoSalarios $codigoSalario
 */
class DevolucionAportes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'devolucion_aportes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado', 'codigo_salario'], 'integer'],
            [['fecha_inicio', 'fecha_corte', 'fecha_hora_registro'], 'safe'],
            [['total_devolucion'], 'number'],
            [['user_name'], 'string', 'max' => 15],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_devolucion' => 'Id Devolucion',
            'id_empleado' => 'Id Empleado',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_corte' => 'Fecha Corte',
            'fecha_hora_registro' => 'Fecha Hora Registro',
            'total_devolucion' => 'Total Devolucion',
            'user_name' => 'User Name',
            'codigo_salario' => 'Codigo Salario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
}
