<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ordenproducciontipo".
 *
 * @property int $idtipo
 * @property string $tipo
 * @property int $remision
 * @property int $activo
 *
 * @property Ordenproduccion[] $ordenproduccions
 */
class Ordenproducciontipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ordenproducciontipo';
    }

    public function beforeSave($insert) {
        if(!parent::beforeSave($insert)){
            return false;
        }
        $this->tipo = strtoupper($this->tipo);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'required'],
            [['activo'], 'integer'],
            [['remision','ver_registro','permite_insumos'], 'integer'],
            [['tipo'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idtipo' => 'Id',
            'tipo' => 'Tipo',
            'activo' => 'Activo',
            'ver_registro' => 'Ver registro',
            'remision' => 'Requiere Remisión',
            'color' => 'color',
            'permite_insumos' => 'Ver insumos'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccions()
    {
        return $this->hasMany(Ordenproduccion::className(), ['idtipo' => 'idtipo']);
    }
    
    public function getEstado()
    {
        if($this->activo == 1){
            $estado = "NO";
        }else{
            $estado = "SI";
        }
        return $estado;
    }
    
    public function getRremision()
    {
        if($this->remision == 1){
            $remision = "SI";
        }else{
            $remision = "NO";
        }
        return $remision;
    }
     public function getVerregistro()
    {
        if($this->ver_registro == 1){
            $verregistro = "SI";
        }else{
            $verregistro = "NO";
        }
        return $verregistro;
    }
    
     public function getVerInsumos()
    {
        if($this->permite_insumos == 1){
            $verinsumos = "SI";
        }else{
            $verinsumos = "NO";
        }
        return $verinsumos;
    }
}
