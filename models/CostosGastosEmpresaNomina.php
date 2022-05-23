<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "costos_gastos_empresa_nomina".
 *
 * @property int $id_detalle_nomina
 * @property int $id_costo_gasto
 * @property int $salarios
 * @property int $cesantias
 * @property int $intereses
 * @property int $primas
 * @property int $vacacion
 * @property int $ajuste
 * @property string $usuariosistema
 * @property string $fecha_proceso
 *
 * @property CostosGastosEmpresa $costoGasto
 */
class CostosGastosEmpresaNomina extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costos_gastos_empresa_nomina';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_costo_gasto'], 'required'],
            [['id_costo_gasto', 'salarios', 'cesantias', 'intereses', 'primas', 'vacacion', 'ajuste'], 'integer'],
            [['fecha_proceso'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['id_costo_gasto'], 'exist', 'skipOnError' => true, 'targetClass' => CostosGastosEmpresa::className(), 'targetAttribute' => ['id_costo_gasto' => 'id_costo_gasto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle_nomina' => 'Id Detalle Nomina',
            'id_costo_gasto' => 'Id Costo Gasto',
            'salarios' => 'Salarios',
            'cesantias' => 'Cesantias',
            'intereses' => 'Intereses',
            'primas' => 'Primas',
            'vacacion' => 'Vacacion',
            'ajuste' => 'Ajuste',
            'usuariosistema' => 'Usuariosistema',
            'fecha_proceso' => 'Fecha Proceso',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCostoGasto()
    {
        return $this->hasOne(CostosGastosEmpresa::className(), ['id_costo_gasto' => 'id_costo_gasto']);
    }
}
