<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_equivalente".
 *
 * @property int $consecutivo
 * @property int $identificacion
 * @property string $nombre_completo
 * @property string $fecha
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property string $descripcion
 * @property double $valor
 * @property double $subtotal
 * @property double $retencion_fuente
 * @property double $porcentaje
 *
 * @property Municipio $municipio
 * @property Departamento $departamento
 */
class DocumentoEquivalente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento_equivalente';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a documento cargada de configuración.    
	$this->nombre_completo = strtoupper($this->nombre_completo);
	$this->descripcion = strtoupper($this->descripcion);		
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificacion', 'nombre_completo', 'fecha', 'iddepartamento', 'idmunicipio', 'descripcion', 'valor', 'porcentaje'], 'required'],
            [['identificacion','generar_comprobante'], 'integer'],
            [['fecha'], 'safe'],
            [['valor', 'subtotal', 'retencion_fuente', 'porcentaje'], 'number'],
            [['nombre_completo'], 'string', 'max' => 100],
            [['iddepartamento', 'idmunicipio'], 'string', 'max' => 15],
            [['descripcion'], 'string', 'max' => 250],
            [['idmunicipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['idmunicipio' => 'idmunicipio']],
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'consecutivo' => 'Id',
            'identificacion' => 'Identificación',
            'nombre_completo' => 'Tercero',
            'fecha' => 'Fecha proceso',
            'iddepartamento' => 'Departamento',
            'idmunicipio' => 'Municipio',
            'descripcion' => 'Descripción',
            'valor' => 'Valor',
            'subtotal' => 'Subtotal:',
            'retencion_fuente' => 'Retención Fuente:',
            'porcentaje' => 'Porcentaje:',
            'generar_comprobante' => 'Generar comprobante:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
}
