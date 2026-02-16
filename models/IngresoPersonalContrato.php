<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingreso_personal_contrato".
 *
 * @property int $id_ingreso
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property int $estado_preceso
 * @property string $user_name
 * @property string $fecha_hora_proceso
 * @property int $total_pagar
 *
 * @property IngresoPersonalContratoDetalle[] $ingresoPersonalContratoDetalles
 */
class IngresoPersonalContrato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingreso_personal_contrato';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio', 'fecha_corte'], 'required'],
            [['fecha_inicio', 'fecha_corte', 'fecha_hora_proceso'], 'safe'],
            [['estado_proceso', 'total_pagar'], 'integer'],
            [['user_name'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_ingreso' => 'Id Ingreso',
            'fecha_inicio' => 'Fecha inicio',
            'fecha_corte' => 'Fecha corte:',
            'estado_proceso' => 'Estado:',
            'user_name' => 'User Name',
            'fecha_hora_proceso' => 'Fecha hora proceso:',
            'total_pagar' => 'Total pagar:',
            'observacion' => 'Observacion:'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngresoPersonalContratoDetalles()
    {
        return $this->hasMany(IngresoPersonalContratoDetalle::className(), ['id_ingreso' => 'id_ingreso']);
    }
    
    
    public function getEstadoProceso() {
        if ($this->estado_proceso == 0){
            $estadoproceso = 'SI';
        }else{
            $estadoproceso = 'NO';
        }
        return $estadoproceso;
        
    }
}
