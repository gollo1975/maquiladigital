<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "costo_seguridadsocial".
 *
 * @property int $id_seguridad_social
 * @property int $id_costo_gasto
 * @property int $documento
 * @property string $empleado
 * @property int $pension
 * @property int $eps
 * @property int $arl
 * @property int $caja_compensacion
 * @property string $fecha_proceso
 * @property string $usuariosistema
 *
 * @property CostosGastosEmpresa $costoGasto
 */
class CostoSeguridadsocial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costo_seguridadsocial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_costo_gasto'], 'required'],
            [['id_costo_gasto', 'documento', 'pension', 'eps', 'arl', 'caja_compensacion','salario_prestacional'], 'integer'],
            [['porcentaje_pension', 'porcentaje_eps', 'porcentaje_arl','porcentaje_caja'], 'number'],
            [['fecha_proceso'], 'safe'],
            [['empleado'], 'string', 'max' => 40],
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
            'id_seguridad_social' => 'Id Seguridad Social',
            'id_costo_gasto' => 'Id Costo Gasto',
            'documento' => 'Documento',
            'empleado' => 'Empleado',
            'pension' => 'Pension',
            'eps' => 'Eps',
            'arl' => 'Arl',
            'caja_compensacion' => 'Caja Compensacion',
            'fecha_proceso' => 'Fecha Proceso',
            'usuariosistema' => 'Usuariosistema',
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
