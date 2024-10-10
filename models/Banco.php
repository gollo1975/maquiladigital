<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banco".
 *
 * @property int $idbanco
 * @property string $nitbanco
 * @property string $entidad
 * @property string $direccionbanco
 * @property int $telefonobanco
 * @property string $producto
 * @property string $numerocuenta
 * @property string $nitmatricula
 * @property int $activo
 *
 * @property Matriculaempresa[] $matriculaempresas
 */
class Banco extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banco';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->entidad = strtoupper($this->entidad);
	$this->direccionbanco = strtoupper($this->direccionbanco);
        $this->producto = strtoupper($this->producto);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idbanco','nitbanco', 'entidad', 'direccionbanco', 'telefonobanco', 'producto', 'numerocuenta', 'activo'], 'required'],
            [['telefonobanco', 'activo'], 'integer'],
            [['nitbanco', 'producto', 'nitmatricula'], 'string', 'max' => 50],
            [['entidad', 'direccionbanco'], 'string', 'max' => 40],
            [['numerocuenta'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idbanco' => 'Codigo',
            'nitbanco' => 'Nit',
            'entidad' => 'Entidad',
            'direccionbanco' => 'Direccion',
            'telefonobanco' => 'Telefono',
            'producto' => 'Producto',
            'numerocuenta' => 'N° Cuenta',            
            'activo' => 'Activo',
            'nitmatricula' => 'nitmatricula',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatriculaempresas()
    {
        return $this->hasMany(Matriculaempresa::className(), ['id_banco_factura' => 'idbanco']);
    }
    
    public function getNombreCuenta()
    {
        return "{$this->entidad} - {$this->producto} - {$this->numerocuenta}";
    }
    
    public function getEstado()
    {
        if ($this->activo == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }
    
     public function getTipoCuenta()
    {
        if ($this->producto == 'S'){
            $tipoproducto = "AHORRO";
        }else{
            if ($this->producto == 'D'){
                $tipoproducto = "CORRIENTE";
            }else{
                $tipoproducto = "EFECTIVO";
            }    
        }
        return $tipoproducto;
    }
}
