<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asignacion_producto_detalle".
 *
 * @property int $id_detalle_asignacion
 * @property int $id_asignacion
 * @property int $id_detalle
 * @property int $codigo_producto
 * @property string $referencia
 * @property int $idtalla
 * @property int $valor_minuto
 * @property int $subtotal_producto
 * @property string $fecha_proceso
 * @property string $usuario
 *
 * @property AsignacionProducto $asignacion
 * @property OrdenFabricacionTallas $detalle
 * @property Talla $talla
 */
class AsignacionProductoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asignacion_producto_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_asignacion', 'id_detalle', 'codigo_producto', 'idtalla', 'valor_minuto', 'subtotal_producto','cantidad'], 'integer'],
            [['fecha_proceso'], 'safe'],
            [['tiempo_confeccion'], 'number'],
            [['referencia'], 'string', 'max' => 40],
            [['usuario'], 'string', 'max' => 15],
            [['id_asignacion'], 'exist', 'skipOnError' => true, 'targetClass' => AsignacionProducto::className(), 'targetAttribute' => ['id_asignacion' => 'id_asignacion']],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenFabricacionTallas::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle_asignacion' => 'Id Detalle Asignacion',
            'id_asignacion' => 'Id Asignacion',
            'id_detalle' => 'Id Detalle',
            'codigo_producto' => 'Codigo Producto',
            'referencia' => 'Referencia',
            'idtalla' => 'Idtalla',
            'valor_minuto' => 'Valor Minuto',
            'subtotal_producto' => 'Subtotal Producto',
            'fecha_proceso' => 'Fecha Proceso',
            'usuario' => 'Usuario',
            'cantidad' => 'cantidad',
            'tiempo_confeccion' => 'tiempo_confeccion', 
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsignacion()
    {
        return $this->hasOne(AsignacionProducto::className(), ['id_asignacion' => 'id_asignacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(OrdenFabricacionTallas::className(), ['id_detalle' => 'id_detalle']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }
}
