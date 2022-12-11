<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "simulador_tiempo".
 *
 * @property int $id_simulador
 * @property int $cantidad_operarios
 * @property int $id_horario
 * @property double $eficiencia
 * @property int $vlr_minuto_contrato
 * @property int $salario
 * @property int $vinculado
 * @property int $unidades_lote
 * @property double $sam_prenda
 * @property int $vlr_minuto_venta
 * @property int $valor_lote
 * @property int $valor_costo_lote
 * @property int $utilidad_lote
 * @property string $fecha_registro
 *
 * @property Horario $horario
 */
class SimuladorTiempo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simulador_tiempo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_simulador'], 'required'],
            [['id_simulador', 'cantidad_operarios', 'id_horario', 'vlr_minuto_contrato', 'salario', 'vinculado', 'unidades_lote', 'vlr_minuto_venta', 'valor_lote', 'valor_costo_lote', 'utilidad_lote','idcliente'], 'integer'],
            [['eficiencia', 'sam_prenda','dias_proceso','dias_reales','unidades_por_dia'], 'number'],
            [['fecha_registro','fecha_inicio','fecha_final'], 'safe'],
            [['id_simulador'], 'unique'],
            [['id_horario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['id_horario' => 'id_horario']],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_simulador' => 'Id Simulador',
            'cantidad_operarios' => 'Cantidad Operarios',
            'id_horario' => 'Id Horario',
            'eficiencia' => 'Eficiencia',
            'vlr_minuto_contrato' => 'Vlr Minuto Contrato',
            'salario' => 'Salario',
            'vinculado' => 'Vinculado',
            'unidades_lote' => 'Unidades Lote',
            'sam_prenda' => 'Sam Prenda',
            'vlr_minuto_venta' => 'Vlr Minuto Venta',
            'valor_lote' => 'Valor Lote',
            'valor_costo_lote' => 'Valor Costo Lote',
            'utilidad_lote' => 'Utilidad Lote',
            'fecha_registro' => 'Fecha Registro',
            'idcliente' => 'Cliente:',
            'fecha_inicio' => 'fecha_inicio',
            'unidades_por_dia' => 'Unidades x dia:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHorario()
    {
        return $this->hasOne(Horario::className(), ['id_horario' => 'id_horario']);
    }
     public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }
}
