<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orden_fabricacion_tallas".
 *
 * @property int $id_detalle
 * @property int $codigo_talla
 * @property int $id_orden_fabricacion
 * @property int $cantidad_vendida
 * @property int $cantida_real
 * @property int $idtalla
 *
 * @property PedidoClienteTalla $codigoTalla
 * @property OrdenFabricacion $ordenFabricacion
 * @property Talla $talla
 */
class OrdenFabricacionTallas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_fabricacion_tallas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_talla', 'id_orden_fabricacion', 'cantidad_vendida', 'cantidad_real', 'idtalla'], 'integer'],
            [['codigo_talla'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoClienteTalla::className(), 'targetAttribute' => ['codigo_talla' => 'codigo_talla']],
            [['id_orden_fabricacion'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenFabricacion::className(), 'targetAttribute' => ['id_orden_fabricacion' => 'id_orden_fabricacion']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'codigo_talla' => 'Codigo Talla',
            'id_orden_fabricacion' => 'Id Orden Fabricacion',
            'cantidad_vendida' => 'Cantidad Vendida',
            'cantidad_real' => 'Cantida Real',
            'idtalla' => 'Idtalla',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoTalla()
    {
        return $this->hasOne(PedidoClienteTalla::className(), ['codigo_talla' => 'codigo_talla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenFabricacion()
    {
        return $this->hasOne(OrdenFabricacion::className(), ['id_orden_fabricacion' => 'id_orden_fabricacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }
}
