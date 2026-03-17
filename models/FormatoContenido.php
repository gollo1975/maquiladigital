<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formato_contenido".
 *
 * @property int $id_formato_contenido
 * @property string $nombre_formato
 * @property string $fecha_creacion
 * @property string $contenido
 * @property int $id_configuracion_prefijo
 * @property string $usuariosistema
 *
 * @property ConfiguracionFormatoPrefijo $configuracionPrefijo
 */
class FormatoContenido extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formato_contenido';
    }

     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre_formato = strtoupper($this->nombre_formato);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_formato', 'contenido', 'id_configuracion_prefijo'], 'required'],
            [['fecha_creacion'], 'safe'],
            [['contenido'], 'string'],
            [['id_configuracion_prefijo','ver_listado','genera_prorroga'], 'integer'],
            [['nombre_formato'], 'string', 'max' => 70],
            [['usuariosistema'], 'string', 'max' => 30],
            [['id_configuracion_prefijo'], 'exist', 'skipOnError' => true, 'targetClass' => ConfiguracionFormatoPrefijo::className(), 'targetAttribute' => ['id_configuracion_prefijo' => 'id_configuracion_prefijo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_formato_contenido' => 'Id',
            'nombre_formato' => 'Nombre formato',
            'contenido' => 'Contenido',
            'id_configuracion_prefijo' => 'Tipo formato',
            'usuariosistema' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracionPrefijo()
    {
        return $this->hasOne(ConfiguracionFormatoPrefijo::className(), ['id_configuracion_prefijo' => 'id_configuracion_prefijo']);
    }
}
