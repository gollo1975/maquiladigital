<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "novedad_operario".
 *
 * @property int $id_novedad
 * @property int $id_tipo_novedad
 * @property int $id_operario
 * @property int $documento
 * @property string $fecha_inicio_permiso
 * @property string $fecha_final_permiso
 * @property string $hora_inicio_permiso
 * @property string $hora_final_permiso
 * @property string $fecha_registro
 * @property string $observacion
 * @property string $usuario
 *
 * @property TipoNovedad $tipoNovedad
 * @property Operarios $operario
 */
class NovedadOperario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'novedad_operario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_novedad', 'id_operario', 'fecha_inicio_permiso', 'fecha_final_permiso', 'hora_inicio_permiso', 'hora_final_permiso', 'observacion'], 'required'],
            [['id_tipo_novedad', 'id_operario', 'documento','nro_novedad','autorizado','cerrado'], 'integer'],
            [['fecha_inicio_permiso', 'fecha_final_permiso', 'fecha_registro'], 'safe'],
            [['hora_inicio_permiso', 'hora_final_permiso', 'observacion'], 'string'],
            [['usuario'], 'string', 'max' => 15],
            [['id_tipo_novedad'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNovedad::className(), 'targetAttribute' => ['id_tipo_novedad' => 'id_tipo_novedad']],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_novedad' => 'Id:',
            'id_tipo_novedad' => 'Tipo novedad:',
            'id_operario' => 'Operario:',
            'documento' => 'Documento:',
            'fecha_inicio_permiso' => 'Fecha inicio permiso:',
            'fecha_final_permiso' => 'Fecha final permiso:',
            'hora_inicio_permiso' => 'Hora inicio permiso:',
            'hora_final_permiso' => 'Hora final permiso:',
            'fecha_registro' => 'Fecha registro:',
            'observacion' => 'Observacion:',
            'usuario' => 'Usuario:',
            'autorizado' => 'Autorizado:',
            'cerrado' => 'Cerrado:',
            'nro_novedad' => ' No novedad:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoNovedad()
    {
        return $this->hasOne(TipoNovedad::className(), ['id_tipo_novedad' => 'id_tipo_novedad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperario()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
    }
    
    public function getEstadoAutorizado() {
        if($this->autorizado == 0){
            $estadoautorizado = 'NO';
        }else{
            $estadoautorizado = 'SI';
        }
        return $estadoautorizado ;
    }
    public function getProcesoCerrado() {
        if($this->cerrado == 0){
            $procesocerrado = 'NO';
        }else{
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
}
