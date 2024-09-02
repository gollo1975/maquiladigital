<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_flete_detalle".
 *
 * @property int $id
 * @property int $id_pago
 * @property int $id_despacho
 * @property int $valor_flete
 *
 * @property PagoFletes $pago
 * @property Despachos $despacho
 */
class PagoFleteDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_flete_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pago', 'id_despacho', 'valor_flete'], 'integer'],
            [['id_pago'], 'exist', 'skipOnError' => true, 'targetClass' => PagoFletes::className(), 'targetAttribute' => ['id_pago' => 'id_pago']],
            [['id_despacho'], 'exist', 'skipOnError' => true, 'targetClass' => Despachos::className(), 'targetAttribute' => ['id_despacho' => 'id_despacho']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_pago' => 'Id Pago',
            'id_despacho' => 'Id Despacho',
            'valor_flete' => 'Valor Flete',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPago()
    {
        return $this->hasOne(PagoFletes::className(), ['id_pago' => 'id_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespacho()
    {
        return $this->hasOne(Despachos::className(), ['id_despacho' => 'id_despacho']);
    }
}
