<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salida_bodega_detalle".
 *
 * @property int $id
 * @property int $id_salida_bodega
 * @property int $id_insumo
 * @property string $codigo_insumo
 * @property string $nombre_insumo
 * @property int $cantidad_despachar
 * @property string $nota
 *
 * @property SalidaBodega $salidaBodega
 * @property Insumos $insumo
 */
class SalidaBodegaDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salida_bodega_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_salida_bodega', 'id_insumo','subtotal','iva','total_linea','valor_unitario'], 'integer'],
            [['cantidad_despachar'],'number'],
            [['cantidad_despachar'], 'required'],
            [['codigo_insumo'], 'string', 'max' => 15],
            [['nombre_insumo'], 'string', 'max' => 40],
            [['nota'], 'string', 'max' => 60],
            [['id_salida_bodega'], 'exist', 'skipOnError' => true, 'targetClass' => SalidaBodega::className(), 'targetAttribute' => ['id_salida_bodega' => 'id_salida_bodega']],
            [['id_insumo'], 'exist', 'skipOnError' => true, 'targetClass' => Insumos::className(), 'targetAttribute' => ['id_insumo' => 'id_insumos']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_salida_bodega' => 'Id Salida Bodega',
            'id_insumo' => 'Id Insumo',
            'codigo_insumo' => 'Codigo Insumo',
            'nombre_insumo' => 'Nombre Insumo',
            'cantidad_despachar' => 'Cantidad Despachar',
            'nota' => 'Nota',
            'subtotal' => 'Subtotal:',
            'iva' => 'Iva:',
            'total_linea' => 'Total linea:',
            'valor_unitario' => 'valor_unitario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalidaBodega()
    {
        return $this->hasOne(SalidaBodega::className(), ['id_salida_bodega' => 'id_salida_bodega']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumo()
    {
        return $this->hasOne(Insumos::className(), ['id_insumos' => 'id_insumo']);
    }
}
