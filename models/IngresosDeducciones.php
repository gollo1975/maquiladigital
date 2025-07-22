<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingresos_deducciones".
 *
 * @property int $id_ingreso
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property string $user_name
 * @property string $fecha_hora_proceso
 *
 * @property IngresosDeduccionesDetalle[] $ingresosDeduccionesDetalles
 */
class IngresosDeducciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingresos_deducciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio', 'fecha_corte'], 'required'],
            [['fecha_inicio', 'fecha_corte', 'fecha_hora_proceso'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_ingreso' => 'Codigo:',
            'fecha_inicio' => 'Fecha de inicio:',
            'fecha_corte' => 'Fecha de corte:',
            'user_name' => 'User Name:',
            'fecha_hora_proceso' => 'Fecha Hora Proceso:',
            'observacion' => 'Observacion:'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngresosDeduccionesDetalles()
    {
        return $this->hasMany(IngresosDeduccionesDetalle::className(), ['id_ingreso' => 'id_ingreso']);
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
