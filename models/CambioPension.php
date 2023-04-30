<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cambio_pension".
 *
 * @property int $id_cambio
 * @property int $id_contrato
 * @property int $id_entidad_pension_anterior
 * @property int $id_entidad_pension_nueva
 * @property string $fecha_cambio
 * @property string $usuariosistema
 * @property string $motivo
 *
 * @property Contrato $contrato
 * @property EntidadPension $entidadPensionAnterior
 * @property EntidadPension $entidadPensionNueva
 */
class CambioPension extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cambio_pension';
    }
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->motivo = strtoupper($this->motivo);        
        return true;
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_contrato', 'id_entidad_pension_anterior', 'id_entidad_pension_nueva'], 'integer'],
            [['id_entidad_pension_nueva'], 'required'],
            [['fecha_cambio'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['motivo'], 'string', 'max' => 100],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_entidad_pension_anterior'], 'exist', 'skipOnError' => true, 'targetClass' => EntidadPension::className(), 'targetAttribute' => ['id_entidad_pension_anterior' => 'id_entidad_pension']],
            [['id_entidad_pension_nueva'], 'exist', 'skipOnError' => true, 'targetClass' => EntidadPension::className(), 'targetAttribute' => ['id_entidad_pension_nueva' => 'id_entidad_pension']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_cambio' => 'Id',
            'id_contrato' => 'Nro contrato:',
            'id_entidad_pension_anterior' => 'Entidad pensión anterior:',
            'id_entidad_pension_nueva' => 'Nueva entidad pensión:',
            'fecha_cambio' => 'Fecha Cambio',
            'usuariosistema' => 'Usuario',
            'motivo' => 'Motivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidadPensionAnterior()
    {
        return $this->hasOne(EntidadPension::className(), ['id_entidad_pension' => 'id_entidad_pension_anterior']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidadPensionNueva()
    {
        return $this->hasOne(EntidadPension::className(), ['id_entidad_pension' => 'id_entidad_pension_nueva']);
    }
}
