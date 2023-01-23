<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cantidad_prenda_terminadas".
 *
 * @property int $id_entrada
 * @property int $id_balanceo
 * @property int $idordenproduccion
 * @property int $iddetalleorden
 * @property int $cantidad_terminda
 * @property string $fecha_entrada
 * @property string $fecha_procesada
 * @property string $usuariosistema
 * @property string $observacion
 *
 * @property Balanceo $balanceo
 * @property Ordenproduccion $ordenproduccion
 * @property Ordenproducciondetalle $detalleorden
 */
class CantidadPrendaTerminadas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cantidad_prenda_terminadas';
    }
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtolower($this->observacion); 
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_balanceo', 'idordenproduccion', 'iddetalleorden', 'cantidad_terminada','nro_operarios'], 'integer'],
            [['cantidad_terminada','hora_corte_entrada'], 'required'],
            [['fecha_entrada', 'fecha_procesada'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['observacion','hora_corte_entrada'], 'string', 'max' => 50],
            [['id_balanceo'], 'exist', 'skipOnError' => true, 'targetClass' => Balanceo::className(), 'targetAttribute' => ['id_balanceo' => 'id_balanceo']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['iddetalleorden'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciondetalle::className(), 'targetAttribute' => ['iddetalleorden' => 'iddetalleorden']],
            [['id_proceso_confeccion'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoConfeccionPrenda::className(), 'targetAttribute' => ['id_proceso_confeccion' => 'id_proceso_confeccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrada' => 'Id Entrada',
            'id_balanceo' => 'Id Balanceo',
            'idordenproduccion' => 'Idordenproduccion',
            'iddetalleorden' => 'Iddetalleorden',
            'cantidad_terminada' => 'Cantidad Terminada',
            'fecha_entrada' => 'Fecha Entrada',
            'fecha_procesada' => 'Fecha Procesada',
            'usuariosistema' => 'Usuariosistema',
            'observacion' => 'Observacion',
            'nro_operarios' => 'Nro Operarios:',
            'hora_corte_entrada' => 'Hora corte:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceo()
    {
        return $this->hasOne(Balanceo::className(), ['id_balanceo' => 'id_balanceo']);
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
    public function getDetalleorden()
    {
        return $this->hasOne(Ordenproducciondetalle::className(), ['iddetalleorden' => 'iddetalleorden']);
    }
    
    public function getProcesoconfeccionprenda()
    {
        return $this->hasOne(ProcesoConfeccionPrenda::className(), ['id_proceso_confeccion' => 'id_proceso_confeccion']);
    }
}
