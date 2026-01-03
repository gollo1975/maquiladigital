<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido_tallas".
 *
 * @property int $codigo
 * @property int $id_detalle
 * @property int $idtalla
 * @property int $id_pedido
 * @property int $cantidad
 *
 * @property PedidosDetalle $detalle
 * @property Talla $talla
 * @property Pedidos $pedido
 */
class PedidoTallas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_tallas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_detalle', 'idtalla', 'id_pedido', 'cantidad','unidades_despachadas','segundo_despacho'], 'integer'],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => PedidosDetalle::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo' => 'Codigo',
            'id_detalle' => 'Id Detalle',
            'idtalla' => 'Idtalla',
            'id_pedido' => 'Id Pedido',
            'cantidad' => 'Cantidad',
            'unidades_despachadas' => 'unidades_despachadas'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(PedidosDetalle::className(), ['id_detalle' => 'id_detalle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(Pedidos::className(), ['id_pedido' => 'id_pedido']);
    }
    
    // muestra e nombre de la talla
    public function getNombreTalla()
    {
        return " Talla: {$this->talla->talla}";
    }
}
