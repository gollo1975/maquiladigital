<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_contrato".
 *
 * @property int $id_tipo_contrato
 * @property string $contrato
 * @property int $estado
 *
 * @property Contrato[] $contratos
 */
class TipoContrato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_contrato';
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->contrato = strtoupper($this->contrato);        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contrato','id_configuracion_prefijo'], 'required'],
            [['estado','prorroga', 'nro_prorrogas'], 'integer'],
            [['contrato'], 'string', 'max' => 100],
            [['prefijo'],'string', 'max' => 4],
            [['abreviatura'],'string', 'max' => 10],
            [['id_configuracion_prefijo'], 'exist', 'skipOnError' => true, 'targetClass' => ConfiguracionFormatoPrefijo::className(), 'targetAttribute' => ['id_configuracion_prefijo' => 'id_configuracion_prefijo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_contrato' => 'Id',
            'contrato' => 'Tipo contrato:',
            'estado' => 'Activo:',
            'prorroga' => 'Prorroga:',
            'nro_prorrogas' => 'Nro_prorrogas:',
            'id_configuracion_prefijo' => 'Tipo formato:',
            'abreviatura' => 'Abreviatura:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['id_tipo_contrato' => 'id_tipo_contrato']);
    }
    
    public function getConfiguracionprefijo()
    {
        return $this->hasOne(ConfiguracionFormatoPrefijo::className(), ['id_configuracion_prefijo' => 'id_configuracion_prefijo']);
    }
    
     public function getFormatoContenidos()
    {
        return $this->hasone(FormatoContenido::className(), ['id_configuracion_prefijo' => 'id_configuracion_prefijo']);
    }
    
    public function getActivo()
    {
        if($this->estado == 1){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
      public function getProrrogacontrato()
    {
        if($this->prorroga == 1){
            $prorroga = "SI";
        }else{
            $prorroga = "NO";
        }
        return $prorroga;
    }
}
