<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resolucion".
 *
 * @property int $idresolucion
 * @property string $nroresolucion
 * @property string $desde
 * @property string $hasta
 * @property string $fechacreacion
 * @property string $fechavencimiento
 * @property string $nitmatricula
 * @property int $codigoactividad
 * @property string $descripcion
 * @property int $activo
 *
 * @property Facturaventa[] $facturaventas
 */
class Resolucion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resolucion';
    }
    
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->consecutivo = strtoupper($this->consecutivo);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nroresolucion', 'inicio_rango', 'final_rango', 'fechavencimiento', 'codigoactividad', 'descripcion','vigencia','id_documento'], 'required'],
            [['fechacreacion', 'fechavencimiento','fecha_notificacion'], 'safe'],
            [['codigoactividad', 'activo','inicio_rango','final_rango','id_documento'], 'integer'],
            [['nroresolucion'], 'string', 'max' => 40],
            [['descripcion','codigo_interfaz'], 'string', 'max' => 50],
            [['consecutivo'],'string' ,'max' => 3],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idresolucion' => 'Id',
            'nroresolucion' => 'N° Resolucion',
            'desde' => 'Inicio de rango',
            'hasta' => 'Final de rango',
            'fechacreacion' => 'Fecha Resolución',
            'fechavencimiento' => 'Fecha Vencimiento',
            'nitmatricula' => 'Nitmatricula',
            'codigoactividad' => 'Código Actividad',
            'descripcion' => 'Descripción',
            'activo' => 'Activo',
            'consecutivo' => 'Consecutivo',
            'id_documento' => 'Documento',
            'vigencia' => 'Vigencia',
            'codigo_interfaz' => 'Codigo interfaz',
            'fecha_notificacion' => 'Fecha de notificacion',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaventas()
    {
        return $this->hasMany(Facturaventa::className(), ['idresolucion' => 'idresolucion']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoelectronico()
    {
       
        return $this->hasOne(DocumentoElectronico::className(), ['id_documento' => 'id_documento']);
    }
   
    
    public function getEstado()
    {
        if ($this->activo == 0){
            $activoregistro = "SI";
        }else{
            $activoregistro = "NO";
        }
        return $activoregistro;
    }
    
}
