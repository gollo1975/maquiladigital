<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "periodo_nomina_electronica".
 *
 * @property int $id_periodo_eletronico
 * @property string $fecha_inicio_periodo
 * @property string $fecha_corte_periodo
 * @property int $cantidad_empleados
 * @property string $fecha_registro
 * @property string $user_name
 */
class PeriodoNominaElectronica extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'periodo_nomina_electronica';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio_periodo', 'fecha_corte_periodo'], 'required'],
            [['fecha_inicio_periodo', 'fecha_corte_periodo', 'fecha_registro'], 'safe'],
            [['cantidad_empleados','cerrar_proceso','total_nomina','devengado_nomina','deduccion_nomina'], 'integer'],
            [['user_name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_periodo_electronico' => 'Codigo:',
            'fecha_inicio_periodo' => 'Fecha inicio periodo:',
            'fecha_corte_periodo' => 'Fecha corte periodo:',
            'cantidad_empleados' => 'Cantidad empleados:',
            'fecha_registro' => 'Fecha hora registro:',
            'user_name' => 'User name:',
            'cerrar_proceso' => 'cerrado:',
        ];
    }
    
    public function getCerradoProceso() {
        if($this->cerrar_proceso == 0){
            $procesocerrado = 'NO';
        }else{
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
}
