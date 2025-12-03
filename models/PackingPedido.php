<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packing_pedido".
 *
 * @property int $id_packing
 * @property int $id_pedido
 * @property int $id_despacho
 * @property int $id_transportadora
 * @property string $fecha_proceso
 * @property string $fecha_hora_registro
 * @property int $cantidad_despachadas
 * @property string $user_name
 *
 * @property Pedidos $pedido
 * @property DespachoPedidos $despacho
 * @property Transportadora $transportadora
 * @property PackingPedidoDetalle[] $packingPedidoDetalles
 */
class PackingPedido extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packing_pedido';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'id_despacho', 'id_transportadora', 'cantidad_despachadas','idcliente','numero_packing','autorizado','cerrado_packing','total_cajas'], 'integer'],
            [['fecha_proceso', 'fecha_hora_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['numero_guia'], 'string', 'max' => 20],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
            [['id_despacho'], 'exist', 'skipOnError' => true, 'targetClass' => DespachoPedidos::className(), 'targetAttribute' => ['id_despacho' => 'id_despacho']],
            [['id_transportadora'], 'exist', 'skipOnError' => true, 'targetClass' => Transportadora::className(), 'targetAttribute' => ['id_transportadora' => 'id_transportadora']],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_packing' => 'Codigo:',
            'id_pedido' => 'No de pedido:',
            'id_despacho' => 'No despacho:',
            'id_transportadora' => 'Transportadora:',
            'fecha_proceso' => 'Fecha proceso:',
            'fecha_hora_registro' => 'Fecha Hora Registro:',
            'cantidad_despachadas' => 'Cantidad Despachadas:',
            'user_name' => 'User Name',
            'idcliente' => 'Nombre del cliente:',
            'numero_packing' => 'Numero de packing:',
            'cerrado_packing' => 'Cerrado:',
            'autorizado' => 'Autorizado:',
            'total_cajas' => 'Total cajas:',
            'numero_guia' => 'numero_guia',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(Pedidos::className(), ['id_pedido' => 'id_pedido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespacho()
    {
        return $this->hasOne(DespachoPedidos::className(), ['id_despacho' => 'id_despacho']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransportadora()
    {
        return $this->hasOne(Transportadora::className(), ['id_transportadora' => 'id_transportadora']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackingPedidoDetalles()
    {
        return $this->hasMany(PackingPedidoDetalle::className(), ['id_packing' => 'id_packing']);
    }
    
    public function getAutorizadoPacking() {
        if($this->autorizado == 0){
            $autorizadopackink = 'NO';
        }else {
            $autorizadopackink = 'SI';
        }
        return $autorizadopackink;
    }
    
    public function getProcesoCerrado() {
        if($this->cerrado_packing == 0){
            $procesocerrado = 'NO';
        }else {
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
}
