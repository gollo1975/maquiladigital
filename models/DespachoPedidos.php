<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "despacho_pedidos".
 *
 * @property int $id_despacho
 * @property int $id_pedido
 * @property int $idcliente
 * @property string $fecha_despacho
 * @property int $cantidad_despachada
 * @property string $fecha_hora_registro
 * @property int $user_name
 * @property int $subtotal
 * @property int $impuesto
 * @property int $total_despacho
 *
 * @property DespachoPedidoDetalles[] $despachoPedidoDetalles
 * @property Pedidos $pedido
 * @property Cliente $cliente
 */
class DespachoPedidos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'despacho_pedidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'fecha_despacho'], 'required'],
            [['id_pedido', 'idcliente', 'cantidad_despachada',  'subtotal', 'impuesto', 'total_despacho','numero_pedido','autorizado','despacho_cerrado','numero_despacho'], 'integer'],
            [['fecha_despacho', 'fecha_hora_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => Pedidos::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_despacho' => 'Id',
            'id_pedido' => 'Numero pedido:',
            'idcliente' => 'Nombre del cliente:',
            'fecha_despacho' => 'Fecha despacho:',
            'cantidad_despachada' => 'Cantidad despachada:',
            'fecha_hora_registro' => 'Fecha Hora Registro',
            'user_name' => 'User Name',
            'subtotal' => 'Subtotal:',
            'impuesto' => 'Impuesto:',
            'total_despacho' => 'Total despacho:',
            'numero_pedido' => 'Numero pedido:',
            'despacho_cerrado' => 'despacho_cerrado',
            'autorizado' => 'autorizado',
            'numero_despacho' => 'Numero despacho:'
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespachoPedidoDetalles()
    {
        return $this->hasMany(DespachoPedidoDetalles::className(), ['id_despacho' => 'id_despacho']);
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
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }
    
    //procesoso alternos
    
    public function getAutorizadoPedido(){
        if($this->autorizado == 0){
            $autorizadopedido = 'NO';
        }else{
            $autorizadopedido = 'SI';
        }
        return $autorizadopedido;
    }
    
     public function getDespachoCerrado(){
        if($this->despacho_cerrado == 0){
            $despachocerrado = 'NO';
        }else{
            $despachocerrado = 'SI';
        }
        return $despachocerrado;
    }
}
