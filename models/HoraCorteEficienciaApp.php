<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hora_corte_eficiencia_app".
 *
 * @property int $id_corte
 * @property int $id_valor
 * @property int $idordenproduccion
 * @property string $codigo_producto
 * @property string $hora_inicio
 * @property string $hora_corte
 * @property string $fecha_dia
 * @property string $fecha_registro
 * @property int $aplica_sabado
 * @property string $user_name
 *
 * @property ValorPrendaUnidad $valor
 * @property Ordenproduccion $ordenproduccion
 */
class HoraCorteEficienciaApp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hora_corte_eficiencia_app';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_valor', 'idordenproduccion', 'aplica_sabado'], 'integer'],
            [['hora_inicio', 'hora_corte', 'fecha_dia'], 'required'],
            [['hora_inicio', 'hora_corte', 'fecha_dia', 'fecha_registro'], 'safe'],
            [['codigo_producto'], 'string', 'max' => 20],
            [['user_name'], 'string', 'max' => 15],
            [['id_valor'], 'exist', 'skipOnError' => true, 'targetClass' => ValorPrendaUnidad::className(), 'targetAttribute' => ['id_valor' => 'id_valor']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_corte' => 'Id Corte',
            'id_valor' => 'Id Valor',
            'idordenproduccion' => 'Idordenproduccion',
            'codigo_producto' => 'Codigo Producto',
            'hora_inicio' => 'Hora Inicio',
            'hora_corte' => 'Hora Corte',
            'fecha_dia' => 'Fecha Dia',
            'fecha_registro' => 'Fecha Registro',
            'aplica_sabado' => 'Aplica Sabado',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValor()
    {
        return $this->hasOne(ValorPrendaUnidad::className(), ['id_valor' => 'id_valor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }
}
