<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prorroga_contrato".
 *
 * @property int $id_prorroga_contrato
 * @property int $id_contrato
 * @property string $fecha_desde
 * @property string $fecha_hasta
 * @property string $fecha_creacion
 * @property string $fecha_ultima_contrato
 * @property string $fecha_nueva_renovacion
 * @property int $fecha_preaviso
 * @property int $dias_preaviso
 * @property int $dias_contratados
 * @property string $usuariosistema
 *
 * @property Contrato $contrato
 */
class ProrrogaContrato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prorroga_contrato';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_contrato', 'fecha_preaviso', 'dias_preaviso', 'dias_contratados', 'id_formato_contenido'], 'integer'],
            [['fecha_desde', 'fecha_hasta', 'fecha_creacion', 'fecha_ultima_contrato', 'fecha_nueva_renovacion','fecha_notificacion'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 30],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_formato_contenido'], 'exist', 'skipOnError' => true, 'targetClass' => FormatoContenido::className(), 'targetAttribute' => ['id_formato_contenido' => 'id_formato_contenido']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_prorroga_contrato' => 'Id Prorroga Contrato',
            'id_contrato' => 'Id Contrato',
            'fecha_desde' => 'Fecha Desde',
            'fecha_hasta' => 'Fecha Hasta',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_ultima_contrato' => 'Fecha Ultima Contrato',
            'fecha_nueva_renovacion' => 'Fecha Nueva Renovacion',
            'fecha_preaviso' => 'Fecha Preaviso',
            'dias_preaviso' => 'Dias Preaviso',
            'dias_contratados' => 'Dias Contratados',
            'usuariosistema' => 'Usuariosistema',
            'id_formato_contenido' => 'Tipo formato:',
            'fecha_notificacion' => 'fecha_notificacion',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }
     public function getFormatoContenido()
    {
        return $this->hasOne(FormatoContenido::className(), ['id_formato_contenido' => 'id_formato_contenido']);
    }
}
