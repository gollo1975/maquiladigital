<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clasificacion_segundas".
 *
 * @property int $id_clasificacion
 * @property int $id_remision
 * @property int $id_tipo
 * @property int $xs
 * @property int $s
 * @property int $m
 * @property int $l
 * @property int $xl
 * @property int $xxl
 * @property int $2
 * @property int $4
 * @property int $6
 * @property int $8
 * @property int $10
 * @property int $12
 * @property int $14
 * @property int $16
 * @property int $18
 * @property int $20
 * @property int $22
 * @property int $28
 * @property int $30
 * @property int $32
 * @property int $34
 * @property int $36
 * @property int $38
 * @property int $42
 * @property int $txs
 * @property int $ts
 * @property int $tm
 * @property int $tl
 * @property int $txl
 * @property int $txxl
 * @property int $t2
 * @property int $t4
 * @property int $t6
 * @property int $t8
 * @property int $t10
 * @property int $t12
 * @property int $t14
 * @property int $t16
 * @property int $t18
 * @property int $t20
 * @property int $t22
 * @property int $t28
 * @property int $t30
 * @property int $t32
 * @property int $t34
 * @property int $t36
 * @property int $t38
 * @property int $t42
 * @property int $estado
 * @property int $unidades
 *
 * @property Remision $remision
 * @property TipoClasificacionSegundas $tipo
 */
class ClasificacionSegundas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clasificacion_segundas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_remision', 'id_tipo','xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl','txxs','txs','ts','tm','tl','txl','txxl','a2','a4','a6','a8','a10','a12','a14','a16',
                'a18','a20','a22','a28','a30','a32','a34','a36','a38','a40','a42','t2', 't4', 't6', 't8', 't10', 't12', 't14', 't16', 't18', 't20', 't22',
                't28', 't30', 't32', 't34', 't36', 't38','t40', 't42', 'unidades'], 'integer'],
            [['id_remision'], 'exist', 'skipOnError' => true, 'targetClass' => Remision::className(), 'targetAttribute' => ['id_remision' => 'id_remision']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoClasificacionSegundas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_clasificacion' => 'Id Clasificacion',
            'id_remision' => 'Id Remision',
            'id_tipo' => 'Id Tipo',
            'xxs' => 'XXS',
            'xs' => 'XS',
            's' => 'S',
            'm' => 'M',
            'l' => 'L',
            'xl' => 'Xl',
            'xxl' => 'XXL',
            'txxs' => 'XXS',
            'txs' => 'XS',
            'ts' => 'S',
            'tm' => 'M',
            'tl' => 'L',
            'txl' => 'XL',
            'txxl' => 'XXL',
            't2' => '2',
            't4' => '4',
            't6' => '6',
            't8' => '8',
            't10' => '10',
            't12' => '12',
            't14' => '14',
            't16' => '16',
            't18' => '18',
            't20' => '20',
            't22' => '22',
            't28' => '28',
            't30' => '30',
            't32' => '32',
            't34' => '34',
            't36' => '36',
            't38' => '38',
            't40' => '40',
            't42' => '42',
            'a2' => 'A2',
            'a4' => 'A4',
            'a6' => 'A6',
            'a8' => 'A8',
            'a10' => 'A10',
            'a12' => 'A12',
            'a14' => 'A14',
            'a16' => 'A16',
            'a18' => 'A18',
            'a20' => 'A20',
            'a22' => 'A22',
            'a28' => 'A28',
            'a30' => 'A30',
            'a32' => 'A32',
            'a34' => 'A34',
            'a36' => 'A36',
            'a38' => 'A38',
            'a40' => 'A40',
            'a42' => 'A42',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoClasificacionSegundas::className(), ['id_tipo' => 'id_tipo']);
    }
}
