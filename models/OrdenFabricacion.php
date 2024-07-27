<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orden_fabricacion".
 *
 * @property int $id_orden_fabricacion
 * @property int $id_pedido
 * @property int $idcliente
 * @property int $codigo
 * @property int $id_referencia
 * @property string $fecha_fabricacion
 * @property string $fecha_hora_registro
 * @property int $cantidades
 * @property string $user_name
 *
 * @property PedidoCliente $pedido
 * @property ReferenciaProducto $codigo0
 * @property Cliente $cliente
 * @property PedidoClienteReferencias $referencia
 */
class OrdenFabricacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_fabricacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'idcliente', 'codigo_producto', 'id_referencia', 'cantidades','numero_orden','autorizada','orden_cerrada','salida_insumo','asignado_taller'], 'integer'],
            [['fecha_fabricacion', 'fecha_hora_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_pedido'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoCliente::className(), 'targetAttribute' => ['id_pedido' => 'id_pedido']],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['id_referencia'], 'exist', 'skipOnError' => true, 'targetClass' => PedidoClienteReferencias::className(), 'targetAttribute' => ['id_referencia' => 'id_referencia']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_orden_fabricacion' => 'Id:',
            'id_pedido' => 'Numero pedido',
            'idcliente' => 'Cliente:',
            'codigo_producto' => 'Codigo_producto:',
            'id_referencia' => 'Referencia:',
            'fecha_fabricacion' => 'Fecha fabricacion:',
            'fecha_hora_registro' => 'Fecha Hora Registro',
            'cantidades' => 'Cantidades:',
            'user_name' => 'User Name:',
            'numero_orden' => 'Numero orden:',
            'autorizada' => 'Autorizada:',
            'orden_cerrada' => 'Orden cerrada:',
            'salida_insumo' => 'Salida_insumo:',
            'asignado_taller' =>'asignado_taller',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedido()
    {
        return $this->hasOne(PedidoCliente::className(), ['id_pedido' => 'id_pedido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

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
    public function getReferencia()
    {
        return $this->hasOne(PedidoClienteReferencias::className(), ['id_referencia' => 'id_referencia']);
    }
    
    public function getAutorizadoOrden() {
        if($this->autorizada == 0){
            $autorizadaorden = 'NO';
        }else{
            $autorizadaorden = 'SI';
        }
        return $autorizadaorden;
    }
    
     public function getOrdenCerrada() {
        if($this->orden_cerrada == 0){
            $ordencerrada = 'NO';
        }else{
            $ordencerrada = 'SI';
        }
        return $ordencerrada;
    }
    public function getOrdenFabricacion()
    {
        return "Orden: {$this->numero_orden} - {$this->referencia->referencia}";
    }
    
}
