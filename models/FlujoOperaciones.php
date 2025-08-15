<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "flujo_operaciones".
 *
 * @property int $id
 * @property int $idproceso
 * @property int $idordenproduccion
 * @property string $fecha_creacion
 * @property string $usuariosistema
 *
 * @property ProcesoProduccion $proceso
 * @property Ordenproduccion $ordenproduccion
 */
class FlujoOperaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flujo_operaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproceso'], 'required'],
            [['idproceso', 'idordenproduccion','orden_aleatorio','operacion','pieza','cantidad_operaciones','cantidad_confeccionadas'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['segundos','minutos'],'number'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['idproceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['idproceso' => 'idproceso']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idproceso' => 'Idproceso',
            'idordenproduccion' => 'Idordenproduccion',
            'fecha_creacion' => 'Fecha Creacion',
            'segundos' =>'Segundos',
            'orden_aleatorio' => 'Orden aleatorio',
            'minutos' =>'Minutos',
            'usuariosistema' => 'Usuariosistema',
            'operacion' => 'Proceso',
            'pieza' => 'Pieza',
            'cantidad_operaciones' => 'cantidad_operaciones',
            'cantidad_confeccionadas' => 'cantidad_confeccionadas'
        ];
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
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }
    
    public function getTipomaquina()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }
    public function getOperacionPrenda() {
        if($this->operacion == 0){
            $operacion = 'BALANCEO';
        }else{
            $operacion = 'PREPARACION';
        }
        return $operacion;
    }
     public function getPiezaPrenda() {
        if($this->pieza == 0){
            $pieza = 'PIEZA 1';
        }else{
            $pieza = 'PIEZA 2';
        }
        return $pieza;
    }
    
    //permite mostra el nombre de la operacion
     public function getMostrarOperacion()
    {
        return "{$this->idproceso} - Nombre: {$this->proceso->proceso}";
    } 
}
