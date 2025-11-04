<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "referencia_lista_precio".
 *
 * @property int $id_detalle
 * @property int $id_lista
 * @property int $codigo
 * @property int $valor_venta
 * @property string $user_name
 *
 * @property ListaPrecios $lista
 * @property ReferenciaProducto $codigo0
 */
class ReferenciaListaPrecio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'referencia_lista_precio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_lista', 'codigo', 'valor_venta'], 'integer'],
            [['valor_venta'], 'required'],
            [['user_name'], 'string', 'max' => 15],
            [['id_lista'], 'exist', 'skipOnError' => true, 'targetClass' => ListaPrecios::className(), 'targetAttribute' => ['id_lista' => 'id_lista']],
            [['codigo'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenciaProducto::className(), 'targetAttribute' => ['codigo' => 'codigo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_lista' => 'Id Lista',
            'codigo' => 'Codigo',
            'valor_venta' => 'Valor Venta',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLista()
    {
        return $this->hasOne(ListaPrecios::className(), ['id_lista' => 'id_lista']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigo()
    {
        return $this->hasOne(ReferenciaProducto::className(), ['codigo' => 'codigo']);
    }
    
     public function getListaPrecio()
    {
        return "{$this->lista->nombre_lista}";
    }
}
