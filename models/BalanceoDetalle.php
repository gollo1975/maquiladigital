<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "balanceo_detalle".
 *
 * @property int $id_detalle
 * @property int $id_proceso
 * @property int $id_balanceo
 * @property int $id_tipo
 * @property double $segundos
 * @property double $minutos
 * @property double $total_segundos
 * @property double $total_minutos
 * @property string $fecha_creacion
 * @property string $usuariosistema
 *
 * @property ProcesoProduccion $proceso
 * @property Balanceo $balanceo
 * @property TiposMaquinas $tipo
 */
class BalanceoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balanceo_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_proceso', 'id_balanceo', 'id_tipo','id_operario'], 'integer'],
            [['segundos', 'minutos', 'total_segundos', 'total_minutos','sobrante_faltante'], 'number'],
            [['fecha_creacion'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['id_proceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['id_proceso' => 'idproceso']],
            [['id_balanceo'], 'exist', 'skipOnError' => true, 'targetClass' => Balanceo::className(), 'targetAttribute' => ['id_balanceo' => 'id_balanceo']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_proceso' => 'Id Proceso',
            'id_balanceo' => 'Id Balanceo',
            'id_operario' => 'Operario:',
            'id_tipo' => 'Maquina:',
            'segundos' => 'Segundos:',
            'minutos' => 'Minutos:',
            'total_segundos' => 'Total Segundos',
            'total_minutos' => 'Total Minutos',
            'fecha_creacion' => 'Fecha Creacion',
            'usuariosistema' => 'Usuariosistema',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProceso()
    {
        return $this->hasOne(ProcesoProduccion::className(), ['idproceso' => 'id_proceso']);
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
    public function getTipo()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }
    
      public function getOperario()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
    }
    
   
}
