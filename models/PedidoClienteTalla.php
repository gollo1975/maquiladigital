<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido_cliente_talla".
 *
 * @property int $codigo_talla
 * @property int $idtalla
 * @property int $id_referencia
 * @property int $id_pedido
 * @property int $cantidad
 * @property string $fecha_registro
 * @property string $user_name
 *
 * @property Talla $talla
 * @property PedidoClienteReferencias $referencia
 * @property PedidoCliente $pedido
 */
class PedidoClienteTalla extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_cliente_talla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idtalla', 'id_referencia', 'id_pedido', 'cantidad'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
             [['nota'], 'string', 'max' => 120],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id_referencia'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoClienteReferencias::className(), 'targetAttribute' => ['id_referencia' => 'id_referencia']],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoCliente::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo_talla' => 'Codigo Talla',
            'idtalla' => 'Idtalla',
            'id_referencia' => 'Id Referencia',
            'id_pedido' => 'Id Pedido',
            'cantidad' => 'Cantidad',
            'fecha_registro' => 'Fecha Registro',
            'user_name' => 'User Name',
            'nota' => 'Nota'
        ];
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
    public function getReferencia()
    {
        return $this->hasOne(PedidoClienteReferencias::className(), ['id_referencia' => 'id_referencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(PedidoCliente::className(), ['id_pedido' => 'id_pedido']);
    }
}
