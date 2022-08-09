<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicio_mantenimiento".
 *
 * @property int $id_servicio
 * @property string $servicio
 * @property int $valor_servicio
 * @property string $fecha_registro
 *
 * @property MantenimientoMaquina[] $mantenimientoMaquinas
 */
class ServicioMantenimiento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicio_mantenimiento';
    }
    public function beforeSave($insert) {
            if(!parent::beforeSave($insert)){
                return false;
            }
            # ToDo: Cambiar a cliente cargada de configuración.    
            $this->servicio = strtoupper($this->servicio);
            
            return true;
        }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['servicio'], 'required'],
            [['valor_servicio','valide_fecha'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['servicio'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_servicio' => 'Codigo',
            'servicio' => 'Servicio',
            'valor_servicio' => 'Valor Servicio',
            'fecha_registro' => 'Fecha Registro',
            'valide_fecha' => 'Aplica cambios',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMantenimientoMaquinas()
    {
        return $this->hasMany(MantenimientoMaquina::className(), ['id_servicio' => 'id_servicio']);
    }
}
