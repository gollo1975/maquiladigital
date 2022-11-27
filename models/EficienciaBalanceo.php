<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eficiencia_balanceo".
 *
 * @property int $id_eficiencia
 * @property int $id_balanceo
 * @property string $fecha_confeccion
 * @property int $unidades_confeccionadas
 * @property int $nro_operarios
 * @property int $unidades_por_operarios
 * @property int $cantidad_por_dia
 * @property double $porcentaje_cumplimiento
 * @property string $usuario
 *
 * @property Balanceo $balanceo
 */
class EficienciaBalanceo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eficiencia_balanceo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_balanceo', 'unidades_confeccionadas', 'nro_operarios', 'unidades_por_operarios', 'cantidad_por_dia'], 'integer'],
            [['fecha_confeccion'], 'safe'],
            [['porcentaje_cumplimiento','minutos_balanceo','horas_finales','horas_inicio'], 'number'],
            [['usuario'], 'string', 'max' => 15],
            [['id_balanceo'], 'exist', 'skipOnError' => true, 'targetClass' => Balanceo::className(), 'targetAttribute' => ['id_balanceo' => 'id_balanceo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_eficiencia' => 'Id Eficiencia',
            'id_balanceo' => 'Id Balanceo',
            'fecha_confeccion' => 'Fecha Confeccion',
            'unidades_confeccionadas' => 'Unidades Confeccionadas',
            'nro_operarios' => 'Nro Operarios',
            'unidades_por_operarios' => 'Unidades Por Operarios',
            'cantidad_por_dia' => 'Cantidad Por Dia',
            'porcentaje_cumplimiento' => 'Porcentaje Cumplimiento',
            'usuario' => 'Usuario',
            'minutos_balanceo' => 'Minutos balanceo:',
            'horas_inicio' => 'horas_inicio',
            'horas_finales' => 'horas_finales',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceo()
    {
        return $this->hasOne(Balanceo::className(), ['id_balanceo' => 'id_balanceo']);
    }
}
