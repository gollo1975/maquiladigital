<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salida_bodega_operaciones".
 *
 * @property int $id_operacion
 * @property int $idproceso
 * @property int $idtipo
 * @property int $id_tipo
 * @property int $id_salida_bodega
 * @property int $codigo_operacion
 * @property string $nombre_operacion
 * @property double $minutos
 * @property string $fecha_creacion
 * @property string $user_name
 *
 * @property ProcesoProduccion $proceso
 * @property Ordenproducciontipo $tipo
 * @property TiposMaquinas $tipo0
 * @property SalidaBodega $salidaBodega
 */
class SalidaBodegaOperaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salida_bodega_operaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproceso', 'idtipo', 'id_tipo', 'id_salida_bodega','segundos'], 'integer'],
            [['minutos'], 'number'],
            [['fecha_creacion'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['idproceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['idproceso' => 'idproceso']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
            [['id_salida_bodega'], 'exist', 'skipOnError' => true, 'targetClass' => SalidaBodega::className(), 'targetAttribute' => ['id_salida_bodega' => 'id_salida_bodega']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operacion' => 'Id Operacion',
            'idproceso' => 'Idproceso',
            'idtipo' => 'Idtipo',
            'id_tipo' => 'Id Tipo',
            'id_salida_bodega' => 'Id Salida Bodega',
            'minutos' => 'Minutos',
            'fecha_creacion' => 'Fecha Creacion',
            'user_name' => 'User Name',
            'segundos' => 'Segundos:',
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
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoMaquinas()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalidaBodega()
    {
        return $this->hasOne(SalidaBodega::className(), ['id_salida_bodega' => 'id_salida_bodega']);
    }
}
