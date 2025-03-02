<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orden_produccion_insumo_detalle".
 *
 * @property int $id
 * @property int $id_entrega
 * @property int $id_detalle
 * @property int $id_insumos
 * @property int $cantidad
 *
 * @property OrdenProduccionInsumos $entrega
 * @property ReferenciaInsumos $detalle
 * @property Insumos $insumos
 */
class OrdenProduccionInsumoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_produccion_insumo_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_entrega', 'id_detalle', 'id_insumos', 'cantidad','iddetalleorden'], 'integer'],
            [['cantidad','metros','unidades'], 'number'],
            [['id_entrega'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenProduccionInsumos::className(), 'targetAttribute' => ['id_entrega' => 'id_entrega']],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenciaInsumos::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
            [['id_insumos'], 'exist', 'skipOnError' => true, 'targetClass' => Insumos::className(), 'targetAttribute' => ['id_insumos' => 'id_insumos']],
            [['iddetalleorden'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciondetalle::className(), 'targetAttribute' => ['iddetalleorden' => 'iddetalleorden']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_entrega' => 'Id Entrega',
            'id_detalle' => 'Id Detalle',
            'id_insumos' => 'Id Insumos',
            'cantidad' => 'Cantidad',
            'iddetalleorden' => 'iddetalleorden',
            'metros' => 'metros',
            'unidades' => 'unidades',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntrega()
    {
        return $this->hasOne(OrdenProduccionInsumos::className(), ['id_entrega' => 'id_entrega']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(ReferenciaInsumos::className(), ['id_detalle' => 'id_detalle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasOne(Insumos::className(), ['id_insumos' => 'id_insumos']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenDetalle()
    {
        return $this->hasOne(Ordenproducciondetalle::className(), ['iddetalleorden' => 'iddetalleorden']);
    }
}
