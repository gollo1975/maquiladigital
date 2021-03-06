<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seguimiento_produccion_detalle2".
 *
 * @property int $id_seguimiento_produccion_detalle
 * @property int $id_seguimiento_produccion
 * @property string $fecha_inicio
 * @property string $fecha_consulta
 * @property string $hora_inicio
 * @property string $hora_consulta
 * @property double $minutos
 * @property double $horas_a_trabajar
 * @property double $cantidad_por_hora
 * @property double $cantidad_total_por_hora
 * @property double $operarias
 * @property double $total_unidades_por_dia
 * @property double $total_unidades_por_hora
 * @property double $prendas_sistema
 * @property double $prendas_reales
 * @property double $porcentaje_produccion
 * @property double $total_venta
 */
class SeguimientoProduccionDetalle2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seguimiento_produccion_detalle2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_seguimiento_produccion'], 'integer'],
            [['fecha_inicio', 'hora_inicio', 'fecha_consulta','hora_consulta'], 'safe'],
            [['minutos', 'horas_a_trabajar', 'cantidad_por_hora', 'cantidad_total_por_hora','operarias', 'total_unidades_por_dia', 'total_unidades_por_hora', 'prendas_sistema', 'prendas_reales', 'porcentaje_produccion','total_venta'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_seguimiento_produccion_detalle' => 'Id Seguimiento Produccion Detalle',
            'id_seguimiento_produccion' => 'Id Seguimiento Produccion',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_consulta' => 'Fecha Consulta',
            'hora_inicio' => 'Hora Inicio',
            'hora_consulta' => 'Hora Consulta',
            'minutos' => 'Minutos',            
            'horas_a_trabajar' => 'Horas A Trabajar',
            'cantidad_por_hora' => 'Cantidad Por Hora',
            'cantidad_total_por_hora' => 'Cantidad',
            'operarias' => 'Operarias',
            'total_unidades_por_dia' => 'Total',
            'total_unidades_por_hora' => 'Operacion Por Hora',
            'prendas_sistema' => 'Prendas Sistema',
            'prendas_reales' => 'Prendas Reales',
            'porcentaje_produccion' => 'Porcentaje Produccion',
            'total_venta' => 'Total Venta',
        ];
    }
}
