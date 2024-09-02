<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_fletes".
 *
 * @property int $id_pago
 * @property int $idproveedor
 * @property string $fecha_pago
 * @property int $total_pagado
 * @property string $fecha_registro
 * @property int $numero_pago
 * @property int $autorizado
 * @property int $proceso_cerrado
 * @property string $user_name
 *
 * @property PagoFleteDetalle[] $pagoFleteDetalles
 * @property Proveedor $proveedor
 */
class PagoFletes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_fletes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproveedor', 'fecha_pago'], 'required'],
            [['idproveedor', 'total_pagado', 'numero_pago', 'autorizado', 'proceso_cerrado'], 'integer'],
            [['fecha_pago', 'fecha_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 100],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pago' => 'Id',
            'idproveedor' => 'Nombre del proveedor:',
            'fecha_pago' => 'Fecha pago:',
            'total_pagado' => 'Total pagado:',
            'fecha_registro' => 'Fecha registro:',
            'numero_pago' => 'Numero pago:',
            'autorizado' => 'Autorizado:',
            'proceso_cerrado' => 'Proceso cerrado:',
            'user_name' => 'User Name:',
            'observacion' => 'Observacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagoFleteDetalles()
    {
        return $this->hasMany(PagoFleteDetalle::className(), ['id_pago' => 'id_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'idproveedor']);
    }
    
    public function getProcesoCerrado() {
        if($this->proceso_cerrado == 0){
            $procesocerrado = 'NO';
        } else {
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
    
    public function getRegistroAutorizado() {
        if($this->autorizado == 0){
            $registroautorizado = 'NO';
        } else {
            $registroautorizado = 'SI';
        }
        return $registroautorizado;
    }
}
