<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orden_produccion_insumos".
 *
 * @property int $id_entrega
 * @property int $idordenproduccion
 * @property int $idtipo
 * @property string $fecha_hora_generada
 * @property string $codigo_producto
 * @property string $orden_produccion_cliente
 * @property int $total_insumos
 * @property int $total_costo
 * @property string $user_name
 * @property string $fecha_creada
 *
 * @property OrdenProduccionInsumoDetalle[] $ordenProduccionInsumoDetalles
 * @property Ordenproduccion $ordenproduccion
 * @property Ordenproducciontipo $tipo
 */
class OrdenProduccionInsumos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_produccion_insumos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'idtipo', 'total_insumos', 'total_costo','autorizado', 'numero_orden'], 'integer'],
            [['fecha_hora_generada', 'fecha_creada'], 'safe'],
            [['codigo_producto', 'orden_produccion_cliente', 'user_name'], 'string', 'max' => 15],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrega' => 'Id',
            'idordenproduccion' => 'Orden produccion',
            'idtipo' => 'Tipo de orden',
            'fecha_hora_generada' => 'Fecha hora generada',
            'codigo_producto' => 'Referencia',
            'orden_produccion_cliente' => 'Orden produccion cliente',
            'total_insumos' => 'Total insumos',
            'total_costo' => 'Total costo',
            'user_name' => 'User Name',
            'fecha_creada' => 'Fecha creada',
            'autorizado' => 'Autorizado',
            'numero_orden' => 'Numero de orden',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenProduccionInsumoDetalles()
    {
        return $this->hasMany(OrdenProduccionInsumoDetalle::className(), ['id_entrega' => 'id_entrega']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }
    
    public function getEstadoAutorizado() {
        if($this->autorizado == 0){
            $estadoautorizado = 'NO';
        }else{
            $estadoautorizado = 'SI';
        }
        return $estadoautorizado;
    }
}
