<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "simulador_salario".
 *
 * @property int $id_simulador_salario
 * @property int $id_arl
 * @property int $salario
 * @property int $auxilio_transporte
 * @property int $valor_pension
 * @property int $valor_caja
 * @property int $valor_arl
 * @property int $valor_prima
 * @property int $valor_cesantia
 * @property int $valor_interes
 * @property int $valor_vacacion
 * @property int $ajuste_vacacion
 * @property int $total_salarios
 * @property string $usuario
 * @property string $fecha_proceso
 */
class SimuladorSalario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simulador_salario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_arl'], 'required'],
            [['id_arl', 'salario', 'auxilio_transporte', 'valor_caja', 'valor_arl', 'valor_prima', 'valor_cesantia', 'valor_interes', 'valor_vacacion', 'ajuste_vacacion',
                'total_salarios','valor_prenda','dias_laborados','unidades_dia', 'valor_venta','unidades_mes','valor_minuto'], 'integer'],
            [['valor_pension','sam_prenda','eficiencia'], 'number'],
            [['fecha_proceso'], 'safe'],
            [['usuario'], 'string', 'max' => 15],
            [['id_arl'], 'exist', 'skipOnError' => true, 'targetClass' => Arl::className(), 'targetAttribute' => ['id_arl' => 'id_arl']],
            [['id_horario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['id_horario' => 'id_horario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_simulador_salario' => 'Id Simulador Salario',
            'id_arl' => 'Id Arl',
            'salario' => 'Salario',
            'auxilio_transporte' => 'Auxilio Transporte',
            'valor_pension' => 'Valor Pension',
            'valor_caja' => 'Valor Caja',
            'valor_arl' => 'Valor Arl',
            'valor_prima' => 'Valor Prima',
            'valor_cesantia' => 'Valor Cesantia',
            'valor_interes' => 'Valor Interes',
            'valor_vacacion' => 'Valor Vacacion',
            'ajuste_vacacion' => 'Ajuste Vacacion',
            'total_salarios' => 'Total Salarios',
            'id_horario' => 'Horario:',
            'sam_prenda' => 'sam_prenda',
            'eficiencia' => 'eficiencia',
            'valor_prenda' => 'valor_prenda',
            'dias_laborados' => 'dias_laborados',
            'unidades_dia' => 'unidades_dia',
            'valor_venta' => 'valor_venta',
            'unidades_mes' => 'unidades_mes',
            'valor_minuto' => 'valor_minuto',
            'usuario' => 'Usuario',
            'fecha_proceso' => 'Fecha Proceso',
        ];
    }
     public function getArl()
    {
        return $this->hasOne(Arl::className(), ['id_arl' => 'id_arl']);
    }
      public function getHorario()
    {
        return $this->hasOne(Horario::className(), ['id_horario' => 'id_horario']);
    }
}
