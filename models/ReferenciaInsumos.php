<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "referencia_insumos".
 *
 * @property int $id_detalle
 * @property int $codigo
 * @property int $idtipo
 * @property int $id_insumos
 * @property double $cantidad
 * @property int $costo_producto
 * @property string $fecha_registro
 * @property string $user_name
 *
 * @property ReferenciaProducto $codigo0
 * @property Ordenproducciontipo $tipo
 * @property Insumos $insumos
 */
class ReferenciaInsumos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'referencia_insumos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'idtipo', 'id_insumos', 'costo_producto'], 'integer'],
            [['cantidad'], 'number'],
            [['fecha_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['codigo'], 'exist', 'skipOnError' => true, 'targetClass' => ReferenciaProducto::className(), 'targetAttribute' => ['codigo' => 'codigo']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
            [['id_insumos'], 'exist', 'skipOnError' => true, 'targetClass' => Insumos::className(), 'targetAttribute' => ['id_insumos' => 'id_insumos']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'codigo' => 'Codigo',
            'idtipo' => 'Idtipo',
            'id_insumos' => 'Id Insumos',
            'cantidad' => 'Cantidad',
            'costo_producto' => 'Costo Producto',
            'fecha_registro' => 'Fecha Registro',
            'user_name' => 'User Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoReferencia()
    {
        return $this->hasOne(ReferenciaProducto::className(), ['codigo' => 'codigo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasOne(Insumos::className(), ['id_insumos' => 'id_insumos']);
    }
}
