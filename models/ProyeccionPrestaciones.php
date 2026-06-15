<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proyeccion_prestaciones".
 *
 * @property int $id_proyeccion
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property double $total_primas
 * @property double $total_cesantias
 * @property double $total_intereses
 * @property double $total_vacaciones
 * @property string $user_name
 * @property string $fecha_hora_registro
 *
 * @property ProyeccionPrestacionesDetalle[] $proyeccionPrestacionesDetalles
 */
class ProyeccionPrestaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proyeccion_prestaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio', 'fecha_corte'], 'required'],
            [['fecha_inicio', 'fecha_corte', 'fecha_hora_registro'], 'safe'],
            [['total_primas', 'total_cesantias', 'total_intereses', 'total_vacaciones','gran_total'], 'number'],
            [['user_name'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_proyeccion' => 'Id Proyeccion',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_corte' => 'Fecha Corte',
            'total_primas' => 'Total Primas',
            'total_cesantias' => 'Total Cesantias',
            'total_intereses' => 'Total Intereses',
            'total_vacaciones' => 'Total Vacaciones',
            'user_name' => 'User Name',
            'fecha_hora_registro' => 'Fecha Hora Registro',
            'gran_total' => 'gran_total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyeccionPrestacionesDetalles()
    {
        return $this->hasMany(ProyeccionPrestacionesDetalle::className(), ['id_proyeccion' => 'id_proyeccion']);
    }
    
   public function getResumenValores()
    {
        return [
            'Total Primas' => '$ ' . number_format($this->total_primas, 0, ',', '.'),
            'Total Cesantías' => '$ ' . number_format($this->total_cesantias, 0, ',', '.'),
            'Total Intereses' => '$ ' . number_format($this->total_intereses, 0, ',', '.'),
            'Total Vacaciones' => '$ ' . number_format($this->total_vacaciones, 0, ',', '.'),
            'GRAN TOTAL' => '$ ' . number_format($this->gran_total, 0, ',', '.'),
        ];
    }
    
    public function consolidarTotales()
    {
        // Usamos el query builder para sumar los campos de los detalles
        $totales = ProyeccionPrestacionesDetalle::find()
            ->where(['id_proyeccion' => $this->id_proyeccion])
            ->select([
                'sum(valor_prima) as total_primas',
                'sum(valor_cesantia) as total_cesantias',
                'sum(valor_intereses) as total_intereses',
                'sum(valor_vacacion) as total_vacaciones',
                'sum(total_linea) as gran_total'
            ])
            ->asArray()
            ->one();

        // Asignamos los resultados al modelo padre
        $this->total_primas = $totales['total_primas'] ?? 0;
        $this->total_cesantias = $totales['total_cesantias'] ?? 0;
        $this->total_intereses = $totales['total_intereses'] ?? 0;
        $this->total_vacaciones = $totales['total_vacaciones'] ?? 0;
        $this->gran_total = $totales['gran_total'] ?? 0;

        return $this->save(false);
    }
}
