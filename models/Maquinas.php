<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "maquinas".
 *
 * @property int $id_maquina
 * @property int $id_tipo
 * @property string $codigo
 * @property string $serial
 * @property int $id_marca
 * @property string $modelo
 * @property string $fecha_compra
 * @property string $usuario
 * @property string $fecha_registro
 *
 * @property TiposMaquinas $tipo
 * @property MarcaMaquinas $marca
 */
class Maquinas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'maquinas';
    }
     public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
        $this->serial = strtoupper($this->serial);
	$this->codigo = strtoupper($this->codigo);
	$this->modelo = strtoupper($this->modelo);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo', 'id_marca'], 'required'],
            [['id_tipo', 'id_marca'], 'integer'],
            [['fecha_compra', 'fecha_registro'], 'safe'],
            [['codigo', 'usuario'], 'string', 'max' => 15],
            [['serial'], 'string', 'max' => 20],
            [['modelo'], 'string', 'max' => 10],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
            [['id_marca'], 'exist', 'skipOnError' => true, 'targetClass' => MarcaMaquinas::className(), 'targetAttribute' => ['id_marca' => 'id_marca']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_maquina' => 'Id:',
            'id_tipo' => 'Tipo de maquina:',
            'codigo' => 'Codigo:',
            'serial' => 'Serial:',
            'id_marca' => 'Tipo de marca:',
            'modelo' => 'Modelo',
            'fecha_compra' => 'Fecha Compra',
            'usuario' => 'Usuario',
            'fecha_registro' => 'Fecha Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarca()
    {
        return $this->hasOne(MarcaMaquinas::className(), ['id_marca' => 'id_marca']);
    }
}
