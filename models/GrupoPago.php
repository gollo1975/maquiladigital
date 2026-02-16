<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grupo_pago".
 *
 * @property int $id_grupo_pago
 * @property string $grupo_pago
 * @property int $estado
 * @property string $nombre_periodo
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property int $id_sucursal
 */
class GrupoPago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grupo_pago';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->grupo_pago = strtoupper($this->grupo_pago);        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['iddepartamento', 'id_sucursal', 'idmunicipio','ultimo_pago_prima', 'ultimo_pago_cesantia','grupo_pago','id_periodo_pago'], 'required', 'message' => 'Este campo no puede ser vacio'],
            [['estado', 'id_sucursal', 'limite_devengado','contrato_especial'], 'integer'],
            [['ultimo_pago_prima', 'ultimo_pago_cesantia', 'fecha_creacion', 'ultimo_pago_nomina'], 'safe'],
            [['iddepartamento', 'idmunicipio', 'observacion'], 'string'],
            ['limite_devengado', 'match', 'pattern' => '/^[0-9]+$/i', 'message' => 'Sólo se aceptan números'],
            [['grupo_pago'], 'string', 'max' => 60],
            [['dias_pago'], 'string', 'max' => 10],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_grupo_pago' => 'Nro Grupo',
            'grupo_pago' => 'Grupo Pago',
            'id_periodo_pago' => 'Periodo',
            'iddepartamento' => 'Departamento',
            'idmunicipio' => 'Municipio',
            'id_sucursal' => 'Sucursal pila',
            'ultimo_pago_nomina' =>'Ultimo_pago_nomina',
            'ultimo_pago_prima' => 'Ultima fecha prima',
            'ultimo_pago_cesantia' => 'Ultima fecha cesantia',
            'limite_devengado' => 'Limite devengado',
            'dias_pago' => 'Dias pago',
            'estado' => 'Estado',
            'observacion' => 'Observación',
            'contrato_especial' => 'contrato_especial'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    
     public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
    
    public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }
    
    public function getPeriodoPago() {
        return $this->hasOne(PeriodoPago::className(), ['id_periodo_pago' => 'id_periodo_pago']);
    }
    public function getGrupopago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }
    public function getSucursalpila()
    {
        return $this->hasOne(Sucursal::className(), ['id_sucursal' => 'id_sucursal']);
    }
    public function getActivo()
    {
        if($this->estado == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }
}
