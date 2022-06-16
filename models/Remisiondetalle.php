<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "remisiondetalle".
 *
 * @property int $id_remision_detalle
 * @property int $id_remision
 * @property string $color
 * @property int $oc
 * @property int $tula
 * @property int $xs
 * @property int $s
 * @property int $m
 * @property int $l
 * @property int $xl
 * @property int $28
 * @property int $30
 * @property int $32
 * @property int $34
 * @property int $38
 * @property int $40
 * @property int $42
 * @property int $44
 * @property int $estado
 * @property int $unidades
 *
 * @property Remision $remision
 */
class Remisiondetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'remisiondetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_remision', 'oc', 'tula', 'xs', 's', 'm', 'l', 'xl', 'xxl' , '28', '30', '32', '34', '38', '40', '42', '44', 'estado', 'unidades'], 'integer'],
            [['color'], 'string', 'max' => 25],
            [['id_remision'], 'exist', 'skipOnError' => true, 'targetClass' => Remision::className(), 'targetAttribute' => ['id_remision' => 'id_remision']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_remision_detalle' => 'Id Remision Detalle',
            'id_remision' => 'Id Remision',
            'color' => 'Color',
            'oc' => 'Oc',
            'tula' => 'Tula',
            'xs' => 'Xs',
            's' => 'S',
            'm' => 'M',
            'l' => 'L',
            'xl' => 'XL',
            'xxl' => 'XXL',
            '28' => '28',
            '30' => '30',
            '32' => '32',
            '34' => '34',
            '38' => '38',
            '40' => '40',
            '42' => '42',
            '44' => '44',
            'estado' => 'Estado',
            'unidades' => 'Unidades',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRemision()
    {
        return $this->hasOne(Remision::className(), ['id_remision' => 'id_remision']);
    }
}
