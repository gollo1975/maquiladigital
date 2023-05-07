<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "novedad_orden_produccion".
 *
 * @property int $id_novedad
 * @property int $idordenproduccion
 * @property string $novedad
 * @property string $fecha_proceso
 * @property string $usuariosistema
 * @property int $autorizado
 *
 * @property Ordenproduccion $ordenproduccion
 */
class NovedadOrdenProduccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'novedad_orden_produccion';
    }
   
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'novedad'], 'required'],
            [['idordenproduccion', 'autorizado'], 'integer'],
            [['novedad'], 'string'],
            [['fecha_proceso'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_novedad' => 'Id Novedad',
            'idordenproduccion' => 'Idordenproduccion',
            'novedad' => 'Novedad:',
            'fecha_proceso' => 'Fecha Proceso',
            'usuariosistema' => 'Usuariosistema',
            'autorizado' => 'Autorizado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }
    
    public function getAutorizarNovedad() {
        if($this->autorizado == 1){
           $autorizarnovedad = 'SI';
        }else{
            $autorizarnovedad = 'NO';
        }   
        return $autorizarnovedad;
    }
}
