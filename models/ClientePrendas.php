<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cliente_prendas".
 *
 * @property int $id
 * @property int $id_cliente
 * @property int $id_tipo_producto
 * @property int $valor_confeccion
 * @property int $valor_terminacion
 *
 * @property Cliente $cliente
 * @property TipoProducto $tipoProducto
 */
class ClientePrendas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cliente_prendas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cliente', 'id_tipo_producto', 'valor_confeccion', 'valor_terminacion'], 'integer'],
            [['id_tipo_producto'], 'required'],
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['id_cliente' => 'idcliente']],
            [['id_tipo_producto'], 'exist', 'skipOnError' => true, 'targetClass' => TipoProducto::className(), 'targetAttribute' => ['id_tipo_producto' => 'id_tipo_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_cliente' => 'Id Cliente',
            'id_tipo_producto' => 'Id Tipo Producto',
            'valor_confeccion' => 'Valor Confeccion',
            'valor_terminacion' => 'Valor Terminacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'id_cliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoProducto()
    {
        return $this->hasOne(TipoProducto::className(), ['id_tipo_producto' => 'id_tipo_producto']);
    }
}
