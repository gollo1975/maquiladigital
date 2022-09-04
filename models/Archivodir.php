<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "archivodir".
 *
 * @property int $idarchivodir
 * @property int $iddocumentodir
 * @property int $numero
 * @property int $iddirectorio
 * @property string $nombre
 * @property string $extension
 * @property string $tipo
 * @property double $tamaño
 * @property string $descripcion
 * @property string $comentarios
 *
 * @property Documentodir $documentodir
 * @property Directorio $directorio
 */
class Archivodir extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archivodir';
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->descripcion = strtoupper($this->descripcion);                
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['iddocumentodir', 'numero', 'iddirectorio'], 'integer'],
            [['tamaño'], 'number'],
            [['descripcion', 'comentarios','codigo'], 'string'],
            [['nombre', 'extension', 'tipo'], 'string', 'max' => 50],
            [['iddocumentodir'], 'exist', 'skipOnError' => true, 'targetClass' => Documentodir::className(), 'targetAttribute' => ['iddocumentodir' => 'iddocumentodir']],
            [['iddirectorio'], 'exist', 'skipOnError' => true, 'targetClass' => Directorio::className(), 'targetAttribute' => ['iddirectorio' => 'iddirectorio']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idarchivodir' => 'Idarchivodir',
            'iddocumentodir' => 'Iddocumentodir',
            'numero' => 'Numero',
            'iddirectorio' => 'Iddirectorio',
            'nombre' => 'Nombre',
            'codigo' => 'Codigo:',
            'extension' => 'Extension',
            'tipo' => 'Tipo',
            'tamaño' => 'Tamaño',
            'descripcion' => 'Descripcion',
            'comentarios' => 'Comentarios',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentodir()
    {
        return $this->hasOne(Documentodir::className(), ['iddocumentodir' => 'iddocumentodir']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirectorio()
    {
        return $this->hasOne(Directorio::className(), ['iddirectorio' => 'iddirectorio']);
    }
}
