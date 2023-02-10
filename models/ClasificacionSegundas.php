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
            [['id_remision', 'id_tipo', 'xs', 's', 'm', 'l', 'xl', 'xxl','txs','ts','tm','tl','txl','a2','a4','a6','a8','a10','a12','a14','a16','txxl','t2', 't4', 't6', 't8', 't10', 't12', 't14', 't16', 't18', 't20', 't22', 't28', 't30', 't32', 't34', 't36', 't38', 't42', 'unidades'], 'integer'],
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
            'xs' => 'Xs',
            's' => 'S',
            'm' => 'M',
            'l' => 'L',
            'xl' => 'Xl',
            'xxl' => 'Xxl',
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
            't42' => 'T42',
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
