<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "referencia_producto".
 *
 * @property int $codigo
 * @property string $descripcion_referencia
 * @property int $id_tipo_producto
 * @property int $costo_producto
 * @property string $fecha_registro
 * @property string $user_name
 *
 * @property TipoProducto $tipoProducto
 */
class ReferenciaProducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'referencia_producto';
    }

       public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	$this->descripcion_referencia = strtoupper($this->descripcion_referencia);
        $this->codigo_homologado = strtoupper($this->codigo_homologado);
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion_referencia', 'id_tipo_producto','codigo'], 'required'],
            ['codigo', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['id_tipo_producto', 'costo_producto'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['descripcion_referencia'], 'string', 'max' => 40],
             [['descripcion'], 'string'],
            [['user_name','codigo_homologado'], 'string', 'max' => 15],
           
            [['id_tipo_producto'], 'exist', 'skipOnError' => true, 'targetClass' => TipoProducto::className(), 'targetAttribute' => ['id_tipo_producto' => 'id_tipo_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codigo' => 'Referencia:',
            'descripcion_referencia' => 'Nombre referencia:',
            'id_tipo_producto' => 'Grupo/Producto:',
            'costo_producto' => 'Costo producto:',
            'fecha_registro' => 'Fecha Registro:',
            'user_name' => 'User name:',
            'descripcion' =>'Ficha tecnica:',
            'codigo_homologado' => 'Codigo homologado:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoProducto()
    {
        return $this->hasOne(TipoProducto::className(), ['id_tipo_producto' => 'id_tipo_producto']);
    }
}
