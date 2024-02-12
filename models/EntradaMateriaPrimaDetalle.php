<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entrada_materia_prima_detalle".
 *
 * @property int $id_detalle
 * @property int $id_entrada
 * @property int $id_insumos
 * @property string $fecha_vencimiento
 * @property int $actualizar_precio
 * @property double $porcentaje_iva
 * @property int $cantidad
 * @property int $valor_unitario
 * @property int $total_iva
 * @property int $subtotal
 * @property int $total_entrada
 *
 * @property EntradaMateriaPrima $entrada
 * @property Insumos $insumos
 */
class EntradaMateriaPrimaDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrada_materia_prima_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_entrada', 'id_insumos', 'actualizar_precio', 'cantidad', 'valor_unitario', 'total_iva', 'subtotal', 'total_entrada'], 'integer'],
            [['fecha_vencimiento'], 'safe'],
            [['porcentaje_iva'], 'number'],
            ['codigo_producto', 'string'],
            [['id_entrada'], 'exist', 'skipOnError' => true, 'targetClass' => EntradaMateriaPrima::className(), 'targetAttribute' => ['id_entrada' => 'id_entrada']],
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
            'id_entrada' => 'Id Entrada',
            'id_insumos' => 'Id Insumos',
            'fecha_vencimiento' => 'Fecha Vencimiento',
            'actualizar_precio' => 'Actualizar Precio',
            'porcentaje_iva' => 'Porcentaje Iva',
            'cantidad' => 'Cantidad',
            'valor_unitario' => 'Valor Unitario',
            'total_iva' => 'Total Iva',
            'subtotal' => 'Subtotal',
            'total_entrada' => 'Total Entrada',
            'codigo_producto' => 'Codigo producto:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntrada()
    {
        return $this->hasOne(EntradaMateriaPrima::className(), ['id_entrada' => 'id_entrada']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInsumos()
    {
        return $this->hasOne(Insumos::className(), ['id_insumos' => 'id_insumos']);
    }
    
    public function getActualizarPrecio(){
        if($this->actualizar_precio == 0){
            $actualizarprecio = 'NO';
        }else{
            $actualizarprecio = 'SI';
        }
        return $actualizarprecio;
    }
}
