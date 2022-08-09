<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mantenimiento_maquina".
 *
 * @property int $id_mantenimiento
 * @property int $id_maquina
 * @property int $id_servicio
 * @property int $id_mecanico
 * @property string $fecha_mantenimiento
 * @property string $usuario
 * @property string $observacion
 * @property string $fecha_proceso
 *
 * @property Maquinas $maquina
 * @property ServicioMantenimiento $servicio
 * @property Mecanico $mecanico
 */
class MantenimientoMaquina extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mantenimiento_maquina';
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
            [['id_servicio', 'id_mecanico', 'fecha_mantenimiento', 'observacion'], 'required'],
            [['id_maquina', 'id_servicio', 'id_mecanico'], 'integer'],
            [['fecha_mantenimiento', 'fecha_proceso'], 'safe'],
            [['usuario'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 150],
            [['id_maquina'], 'exist', 'skipOnError' => true, 'targetClass' => Maquinas::className(), 'targetAttribute' => ['id_maquina' => 'id_maquina']],
            [['id_servicio'], 'exist', 'skipOnError' => true, 'targetClass' => ServicioMantenimiento::className(), 'targetAttribute' => ['id_servicio' => 'id_servicio']],
            [['id_mecanico'], 'exist', 'skipOnError' => true, 'targetClass' => Mecanico::className(), 'targetAttribute' => ['id_mecanico' => 'id_mecanico']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_mantenimiento' => 'Id',
            'id_maquina' => 'Maquina:',
            'id_servicio' => 'Servicio:',
            'id_mecanico' => 'Mecanico:',
            'fecha_mantenimiento' => 'Fecha Mantenimiento:',
            'usuario' => 'Usuario:',
            'observacion' => 'Observacion:',
            'fecha_proceso' => 'Fecha Proceso:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaquina()
    {
        return $this->hasOne(Maquinas::className(), ['id_maquina' => 'id_maquina']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicio()
    {
        return $this->hasOne(ServicioMantenimiento::className(), ['id_servicio' => 'id_servicio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMecanico()
    {
        return $this->hasOne(Mecanico::className(), ['id_mecanico' => 'id_mecanico']);
    }
}
