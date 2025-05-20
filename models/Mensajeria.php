<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mensajeria".
 *
 * @property int $id_codigo
 * @property int $idproveedor
 * @property string $fecha_proceso
 * @property string $fecha_registro
 * @property int $id_precio
 * @property string $nota
 * @property string $user_name
 *
 * @property Proveedor $proveedor
 * @property PrecioMensajeria $precio
 */
class Mensajeria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mensajeria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproveedor', 'fecha_proceso', 'id_precio'], 'required'],
            [['idproveedor', 'id_precio','valor_precio','cerrado'], 'integer'],
            [['fecha_proceso', 'fecha_registro'], 'safe'],
            [['nota'], 'string', 'max' => 150],
            [['user_name'], 'string', 'max' => 15],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['id_precio'], 'exist', 'skipOnError' => true, 'targetClass' => PrecioMensajeria::className(), 'targetAttribute' => ['id_precio' => 'id_precio']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_codigo' => 'Id',
            'idproveedor' => 'Proveedor:',
            'fecha_proceso' => 'Fecha proceso:',
            'fecha_registro' => 'Fecha registro:',
            'id_precio' => 'Nombre de ruta:',
            'nota' => 'Nota:',
            'user_name' => 'User Name:',
            'valor_precio' => 'Valor precio:',
            'cerrado' => 'Cerrado:'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrecio()
    {
        return $this->hasOne(PrecioMensajeria::className(), ['id_precio' => 'id_precio']);
    }
    
    public function getCerradoProceso() {
        if($this->cerrado == 0){
            $cerradoproceso = 'NO';
        }else{
            $cerradoproceso = 'SI';
        }
        return $cerradoproceso;
    }
}
