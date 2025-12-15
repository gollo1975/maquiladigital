<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedidos".
 *
 * @property int $id_pedido
 * @property int $numero_pedido
 * @property int $idcliente
 * @property string $fecha_pedido
 * @property string $fecha_entrega
 * @property string $fecha_proceso
 * @property int $total_unidades
 * @property int $valor_total
 * @property int $impuesto
 * @property int $total_pedido
 * @property string $user_name
 * @property int $autorizado
 * @property int $pedido_cerrado
 * @property int $generar_orden
 * @property string $observacion
 * @property int $pedido_anulado
 *
 * @property Cliente $cliente
 * @property PedidosDetalle[] $pedidosDetalles
 */
class Pedidos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero_pedido', 'idcliente', 'total_unidades', 'valor_total', 'impuesto', 'total_pedido', 'autorizado', 'pedido_cerrado', 'generar_orden', 'pedido_anulado',
                'id_agente','pedido_facturado','pedido_despachado'], 'integer'],
            [['idcliente', 'fecha_entrega'], 'required'],
            [['fecha_pedido', 'fecha_entrega', 'fecha_proceso','fecha_envio_email'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 120],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['id_agente'], 'exist', 'skipOnError' => true, 'targetClass' => AgentesComerciales::className(), 'targetAttribute' => ['id_agente' => 'id_agente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pedido' => 'Id',
            'numero_pedido' => 'Numero pedido:',
            'idcliente' => 'Cliente:',
            'fecha_pedido' => 'F. pedido',
            'fecha_entrega' => 'F. entrega',
            'fecha_proceso' => 'Fecha Proceso',
            'total_unidades' => 'Total Unidades',
            'valor_total' => 'Subtotal:',
            'impuesto' => 'Impuesto',
            'total_pedido' => 'Total pedido',
            'user_name' => 'User Name',
            'autorizado' => 'Autorizado',
            'pedido_cerrado' => 'Pedido cerrado',
            'generar_orden' => 'Generar Orden',
            'observacion' => 'Nota',
            'pedido_anulado' => 'Pedido Anulado',
            'valor_total' => 'valor_total',
            'id_agente' => 'Agente comercial',
            'pedido_facturado' => 'pedido_facturado',
            'fecha_envio_email' => 'fecha_envio_email',
           
        ];
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
    public function getAgente()
    {
        return $this->hasOne(AgentesComerciales::className(), ['id_agente' => 'id_agente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPedidosDetalles()
    {
        return $this->hasMany(PedidosDetalle::className(), ['id_pedido' => 'id_pedido']);
    }
    
    //subprocesos
    
    public function getAutorizadoPedido() {
        if($this->autorizado == 0){
            $autorizadopedido = 'NO';
        }else{
            $autorizadopedido = 'SI';
        }
        return $autorizadopedido;
    }
    
    public function getPedidoCerrado() {
        if($this->pedido_cerrado == 0){
            $pedidocerrado = 'NO';
        }else{
            $pedidocerrado = 'SI';
        }
        return $pedidocerrado;
    }
    
     public function getPedidoFacturado() {
        if($this->pedido_facturado == 0){
            $pedidofacturado = 'NO';
        }else{
            $pedidofacturado = 'SI';
        }
        return $pedidofacturado;
    }
    
     public function getPedidoEntregado() {
        if($this->pedido_despachado == 0){
            $pedidoentregado = 'NO';
        }else{
            $pedidoentregado = 'SI';
        }
        return $pedidoentregado;
    }
}
