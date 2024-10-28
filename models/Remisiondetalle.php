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
            [['id_remision', 'oc', 'tula', 'xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl' ,'c2','c4','c6','c8','c10','c12','c14','c16','c18', 'c20','c22','c28', 'c30', 'c32',
                'c34','c36','c38', 'c40', 't2','t4','t6','t8','t10','t12','t14','t16','t18','t20','t22','t28','t30','t32','t34','t36','t38','t40','t42',
                'txxs','txs','ts','tm','tl','txl','txxl','t2','t4','t6','t8','t10','t12','t14','t16','t18','t20','t22','t28','t30','t32','t34','t36','t38',
                't40','t42','estado', 'unidades'], 'integer'],
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
            'xxs' => 'XXs',
            'xs' => 'Xs',
            's' => 'S',
            'm' => 'M',
            'l' => 'L',
            'xl' => 'XL',
            'xxl' => 'XXL',
            'c2' => '2',
            'c4' => '4',
            'c6' => '6',
            'c8' => '8',
            'c10' => '10',
            'c12' => '12',
            'c14' => '14',
            'c16' => '16',
            'c18' => '18',
            'c20' => '20',
            'c22' => '22',
            'c28' => '28',
            'c30' => '30',
            'c32' => '32',
            'c34' => '34',
            'c38' => '38',
            'c40' => '40',
            'c42' => '42',
            'c44' => '44',
            't2' => 'T2',
            't4' => 'T4',
            't6' => 'T6',
            't8' => 'T8',
            't10' => 'T10',
            't12' => 'T12',
            't14' => 'T14',
            't16' => 'T16',
            't18' => 'T18',
            't20' => 'T20',
            't22' => 'T22',
            't28' => 'T28',
            't30' => 'T30',
            't32' => 'T32',
            't34' => 'T34',
            't36' => 'T36',
            't38' => 'T38',
            't40' => 'T40',
            't42' => 'T42',
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
