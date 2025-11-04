<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "traslado_referencia_punto".
 *
 * @property int $id_traslado
 * @property int $id_inventario_saliente
 * @property int $id_inventario_entrante
 * @property int $id_punto_saliente
 * @property int $id_punto_entrante
 * @property int $idtalla
 * @property int $id
 * @property int $id_detalle
 * @property int $unidades
 * @property string $fecha_proceso
 * @property string $fecha_hora_registro
 * @property string $user_name
 * @property int $aplicado
 *
 * @property InventarioPuntoVenta $inventarioSaliente
 * @property InventarioPuntoVenta $inventarioEntrante
 * @property PuntoVenta $puntoSaliente
 * @property PuntoVenta $puntoEntrante
 * @property Talla $talla
 * @property Color $id0
 * @property DetalleColorTalla $detalle
 */
class TrasladoReferenciaPunto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traslado_referencia_punto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inventario_saliente', 'id_inventario_entrante', 'id_punto_saliente', 'id_punto_entrante', 'idtalla', 'id', 'id_detalle', 'unidades', 'aplicado'], 'integer'],
            [['fecha_proceso', 'fecha_hora_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_inventario_saliente'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario_saliente' => 'id_inventario']],
            [['id_inventario_entrante'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario_entrante' => 'id_inventario']],
            [['id_punto_saliente'], 'exist', 'skipOnError' => true, 'targetClass' => PuntoVenta::className(), 'targetAttribute' => ['id_punto_saliente' => 'id_punto']],
            [['id_punto_entrante'], 'exist', 'skipOnError' => true, 'targetClass' => PuntoVenta::className(), 'targetAttribute' => ['id_punto_entrante' => 'id_punto']],
            [['idtalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::className(), 'targetAttribute' => ['idtalla' => 'idtalla']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id' => 'id']],
            [['id_detalle'], 'exist', 'skipOnError' => true, 'targetClass' => DetalleColorTalla::className(), 'targetAttribute' => ['id_detalle' => 'id_detalle']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_traslado' => 'Id Traslado',
            'id_inventario_saliente' => 'Id Inventario Saliente',
            'id_inventario_entrante' => 'Id Inventario Entrante',
            'id_punto_saliente' => 'Id Punto Saliente',
            'id_punto_entrante' => 'Id Punto Entrante',
            'idtalla' => 'Idtalla',
            'id' => 'ID',
            'id_detalle' => 'Id Detalle',
            'unidades' => 'Unidades',
            'fecha_proceso' => 'Fecha Proceso',
            'fecha_hora_registro' => 'Fecha Hora Registro',
            'user_name' => 'User Name',
            'aplicado' => 'Aplicado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarioSaliente()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario_saliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventarioEntrante()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario_entrante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPuntoSaliente()
    {
        return $this->hasOne(PuntoVenta::className(), ['id_punto' => 'id_punto_saliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPuntoEntrante()
    {
        return $this->hasOne(PuntoVenta::className(), ['id_punto' => 'id_punto_entrante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTalla()
    {
        return $this->hasOne(Talla::className(), ['idtalla' => 'idtalla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Color::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalle()
    {
        return $this->hasOne(DetalleColorTalla::className(), ['id_detalle' => 'id_detalle']);
    }
}
