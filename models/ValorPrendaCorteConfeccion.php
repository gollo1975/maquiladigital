<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "valor_prenda_corte_confeccion".
 *
 * @property int $id_corte
 * @property int $id_valor
 * @property int $idordenproduccion
 * @property string $codigo_producto
 * @property string $hora_inicio
 * @property string $hora_corte
 * @property string $fecha_registro
 * @property string $user_name
 *
 * @property ValorPrendaUnidad $valor
 * @property Ordenproduccion $ordenproduccion
 */
class ValorPrendaCorteConfeccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'valor_prenda_corte_confeccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_valor', 'idordenproduccion'], 'integer'],
            [['hora_inicio', 'hora_corte'], 'string'],
            [['fecha_registro','fecha_proceso'], 'safe'],
            [['codigo_producto', 'user_name'], 'string', 'max' => 15],
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
            'fecha_registro' => 'Fecha Registro',
            'user_name' => 'User Name',
            'fecha_proceso' => 'fecha_proceso',
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
