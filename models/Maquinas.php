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
        $this->codigo_maquina = strtoupper($this->codigo_maquina);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo', 'id_marca','id_planta'], 'required'],
            [['id_tipo', 'id_marca','id_planta','estado_maquina'], 'integer'],
            [['fecha_compra', 'fecha_registro','fecha_ultimo_mantenimiento','fecha_nuevo_mantenimiento'], 'safe'],
            [['codigo', 'usuario'], 'string', 'max' => 20],
            [['serial'], 'string', 'max' => 20],
            [['modelo'], 'string', 'max' => 20],
            [['codigo_maquina'], 'string', 'max' => 20],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
            [['id_marca'], 'exist', 'skipOnError' => true, 'targetClass' => MarcaMaquinas::className(), 'targetAttribute' => ['id_marca' => 'id_marca']],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
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
            'codigo_maquina' => 'Nro maquina:',
            'fecha_registro' => 'Fecha Registro',
            'fecha_ultimo_mantenimiento' => 'Fecha ultimo mantenimiento',
            'fecha_nuevo_mantenimiento' => 'Fecha nuevo mantenimiento',
            'id_planta' => 'Bodega/Planta:',
            'estado_maquina' => 'Activa:',
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
    public function getPlanta()
    {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }
    public function getEstadoMaquina() {
        if($this->estado_maquina == 0){
            $estadomaquina = 'SI';
        }else{
            $estadomaquina = 'NO';
        }
        return $estadomaquina;
    }
}
