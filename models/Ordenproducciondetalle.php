<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ordenproducciondetalle".
 *
 * @property int $iddetalleorden
 * @property int $idproductodetalle
 * @property string $codigoproducto
 * @property int $cantidad
 * @property double $vlrprecio
 * @property double $subtotal
 * @property int $idordenproduccion
 * @property int $generado
 * @property int $facturado
 * @property int $porcentaje_proceso
 * @property int $porcentaje_cantidad
 * @property int $ponderacion
 * @property int $cantidad_efectiva
 * @property int $cantidad_operada
 * @property int $totalsegundos
 * @property int $segundosficha
 *
 * @property Productodetalle $productodetalle
 * @property Ordenproducciondetalleproceso[] $ordenproducciondetalleprocesos
 */
class Ordenproducciondetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ordenproducciondetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproductodetalle', 'codigoproducto', 'cantidad'], 'required'],            
            [['idproductodetalle', 'cantidad', 'idordenproduccion', 'generado', 'facturado','cantidad_operada','cantidad_efectiva','id_planta'], 'integer'],
            [['vlrprecio', 'subtotal','ponderacion', 'porcentaje_proceso','porcentaje_cantidad','totalsegundos','segundosficha'], 'number'],
            [['codigoproducto'], 'string', 'max' => 15],
            [['idproductodetalle'], 'exist', 'skipOnError' => true, 'targetClass' => Productodetalle::className(), 'targetAttribute' => ['idproductodetalle' => 'idproductodetalle']],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'iddetalleorden' => 'Iddetalleorden',
            'idproductodetalle' => 'idproductodetalle',
            'codigoproducto' => 'Codigoproducto',
            'cantidad' => 'Cantidad',
            'vlrprecio' => 'Vlrprecio',
            'subtotal' => 'Subtotal',
            'idordenproduccion' => 'Idordenproduccion',
            'generado' => 'Generado',
            'facturado' => 'Facturado',
            'porcentaje_proceso' => 'Porcentaje Proceso',
            'porcentaje_cantidad' => 'Porcentaje Cantidad',
            'ponderacion' => 'Ponderación',
            'cantidad_efectiva' => 'Cantidad Efectiva',
            'cantidad_operada' => 'Cantidad operada',
            'totalsegundos' => 'Total Segundos',
            'id_planta' => 'Planta:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductodetalle()
    {
        return $this->hasOne(Productodetalle::className(), ['idproductodetalle' => 'idproductodetalle']);
    }
     
    public function getPlantaProduccion()
     {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproducciondetalleprocesos()
    {
        return $this->hasMany(Ordenproducciondetalleproceso::className(), ['iddetalleorden' => 'iddetalleorden']);
    }
    
     public function getListadoTalla()
    {
        return " Id: {$this->iddetalleorden} - Talla: {$this->productodetalle->prendatipo->talla->talla}";
    }    
}
