<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "motivo_disciplinario".
 *
 * @property int $id_motivo
 * @property string $concepto
 */
class MotivoDisciplinario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'motivo_disciplinario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['concepto'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_motivo' => 'Codigo',
            'concepto' => 'Concepto',
        ];
    }
}
