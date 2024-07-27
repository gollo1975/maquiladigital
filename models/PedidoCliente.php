<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pedido_cliente".
 *
 * @property int $id_pedido
 * @property int $numero_pedido
 * @property int $idcliente
 * @property string $fecha_pedido
 * @property string $fecha_proceso
 * @property int $total_unidades
 * @property int $valor_total
 * @property int $impuesto
 * @property int $total_pedido
 * @property string $user_name
 * @property int $autorizado
 * @property int $pedido_cerrado
 * @property int $generar_orden
 *
 * @property Cliente $cliente
 */
class PedidoCliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pedido_cliente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero_pedido', 'idcliente', 'total_unidades', 'valor_total', 'impuesto', 'total_pedido', 'autorizado', 'pedido_cerrado', 'generar_orden','pedido_anulado'], 'integer'],
            [['idcliente', 'fecha_pedido','fecha_entrega'], 'required'],
            [['fecha_pedido', 'fecha_proceso','fecha_entrega'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 120],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pedido' => 'Id:',
            'numero_pedido' => 'Numero pedido:',
            'idcliente' => 'Cliente:',
            'fecha_pedido' => 'Fecha pedido:',
            'fecha_entrega' => 'Fecha entrega:',
            'fecha_proceso' => 'Fecha proceso:',
            'total_unidades' => 'Total unidades:',
            'valor_total' => 'Valor total:',
            'impuesto' => 'Impuesto:',
            'total_pedido' => 'Total pedido:',
            'user_name' => 'User name:',
            'autorizado' => 'Autorizado:',
            'pedido_cerrado' => 'Pedido cerrado:',
            'generar_orden' => 'Generar Orden:',
            'observacion' => 'Observacion:',
            'pedido_anulado' => 'pedido_anulado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }
    
    public function getPedidoCerrado() {
        if($this->pedido_cerrado == 0){
            $pedidocerrado = 'NO';
        }else{
            $pedidocerrado = 'SI';
        }
        return $pedidocerrado;
    }
       public function getNombrecliente()
    {
        $pedidos = PedidoClienteReferencias::find()->where(['=','id_pedido',$this->id_pedido])->one();
        if ($pedidos){
            return $pedidos = $this->numero_pedido.' - '.$pedidos->pedido->cliente->nombrecorto;
        }else{
            return $pedidos = $this->id_pedido.' - '."Sin referencias vendidas";
            $this->addError($attribute, "El cliente no tiene pedidos.");
        }
    }
       
}
