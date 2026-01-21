<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bitacora_eficiencia_operario".
 *
 * @property int $id
 * @property int $id_operario
 * @property int $idordenproduccion
 * @property string $fecha_confeccion
 * @property int $idproceso
 * @property int $iddetalleorden
 * @property string $hora_corte
 * @property string $concepto
 *
 * @property Operarios $operario
 * @property Ordenproduccion $ordenproduccion
 * @property ProcesoProduccion $proceso
 * @property Ordenproducciondetalle $detalleorden
 */
class BitacoraEficienciaOperario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bitacora_eficiencia_operario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operario', 'idordenproduccion', 'idproceso', 'iddetalleorden'], 'integer'],
            [['fecha_confeccion'], 'safe'],
            [['hora_corte'], 'string'],
            [['tiempo_real_confeccion','sam'], 'number'],
            [['concepto'], 'string', 'max' => 120],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['idproceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['idproceso' => 'idproceso']],
            [['iddetalleorden'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciondetalle::className(), 'targetAttribute' => ['iddetalleorden' => 'iddetalleorden']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_operario' => 'Id Operario',
            'idordenproduccion' => 'Idordenproduccion',
            'fecha_confeccion' => 'Fecha Confeccion',
            'idproceso' => 'Idproceso',
            'iddetalleorden' => 'Iddetalleorden',
            'hora_corte' => 'Hora Corte',
            'concepto' => 'Concepto',
            'tiempo_real_confeccion' => 'tiempo_real_confeccion',
            'sam' => 'sam',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperario()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
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
    public function getProceso()
    {
        return $this->hasOne(ProcesoProduccion::className(), ['idproceso' => 'idproceso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleorden()
    {
        return $this->hasOne(Ordenproducciondetalle::className(), ['iddetalleorden' => 'iddetalleorden']);
    }
}
