<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_proceso_disciplinario".
 *
 * @property int $id_tipo_disciplinario
 * @property string $concepto
 * @property int $aplica_descargo
 */
class TipoProcesoDisciplinario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_proceso_disciplinario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['concepto'], 'required'],
            [['codigo_interface','id_formato_contenido'], 'integer'],
            [['concepto','color_proceso'], 'string', 'max' => 40],
            [['id_formato_contenido'], 'exist', 'skipOnError' => true, 'targetClass' => FormatoContenido::className(), 'targetAttribute' => ['id_formato_contenido' => 'id_formato_contenido']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tipo_disciplinario' => 'Id Tipo Disciplinario',
            'concepto' => 'Concepto',
            'codigo_interface' => 'Codigo interface',
            'color_proceso' => 'color_proceso',
            'id_formato_contenido' => 'Tipo de formato'
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormatoContenido()
    {
        return $this->hasOne(FormatoContenido::className(), ['id_formato_contenido' => 'id_formato_contenido']);
    }
}
