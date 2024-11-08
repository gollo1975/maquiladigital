<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cliente".
 *
 * @property int $idcliente
 * @property int $id_tipo_documento
 * @property int $cedulanit
 * @property int $dv
 * @property string $razonsocial
 * @property string $nombrecliente
 * @property string $apellidocliente
 * @property string $nombrecorto
 * @property string $direccioncliente
 * @property string $telefonocliente
 * @property string $celularcliente
 * @property string $emailcliente
 * @property string $contacto
 * @property string $telefonocontacto
 * @property string $celularcontacto
 * @property string $formapago
 * @property int $plazopago
 * @property int $iddepartamento
 * @property int $idmunicipio
 * @property string $nitmatricula
 * @property string $tiporegimen
 * @property string $autoretenedor
 * @property string $retencioniva
 * @property string $retencionfuente
 * @property string $observacion
 * @property string $fechaingreso
 * @property float $minuto_confeccion
 * @property float $minuto_terminacion
 *
 * @property Tipodocumento $tipo
 * @property Departamento $departamento
 * @property Municipio $municipio
 * @property Facturaventa[] $facturaventas
 * @property Ordenproduccion[] $ordenproduccions
 * @property Producto[] $productos
 * @property Recibocaja[] $recibocajas
 * @property Fichatiempodetalle[] $fichatiempodetalle
 */
class Cliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cliente';
    }

    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->nombrecliente = strtoupper($this->nombrecliente);
	$this->apellidocliente = strtoupper($this->apellidocliente);
	$this->razonsocial = strtoupper($this->razonsocial);
	$this->nombrecorto = strtoupper($this->nombrecorto);
	$this->direccioncliente = strtoupper($this->direccioncliente);
	$this->contacto = strtoupper($this->contacto);
	$this->observacion = strtoupper($this->observacion);	
        return true;
    }

    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
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
    public function getFacturaventas()
    {
        return $this->hasMany(Facturaventa::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccions()
    {
        return $this->hasMany(Ordenproduccion::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Producto::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
     public function getFormaPago()
    {
        return $this->hasOne(FormaPago::className(), ['id_forma_pago' => 'id_forma_pago']);
    }
    
    public function getRecibocajas()
    {
        return $this->hasMany(Recibocaja::className(), ['idcliente' => 'idcliente']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFichatiempodetalle()
    {
        return $this->hasMany(Fichatiempodetalle::className(), ['idcliente' => 'idcliente']);
    }

    public function getNombreClientes()
    {
        return "{$this->nombrecorto} - {$this->cedulanit}";
    }
    
    public function getAutoretener()
    {
        if($this->autoretenedor == 1){
            $autoretenedor = "SI";
        }else{
            $autoretenedor = "NO";
        }
        return $autoretenedor;
    }
    
    public function getRetenerfuente()
    {
        if($this->retencionfuente == 1){
            $retenerfuente = "SI";
        }else{
            $retenerfuente = "NO";
        }
        return $retenerfuente;
    }
    
    public function getReteneriva()
    {
        if($this->retencioniva == 1){
            $retenerfiva = "SI";
        }else{
            $retenerfiva = "NO";
        }
        return $retenerfiva;
    }
    
    public function getRegimen()
    {
        if($this->tiporegimen == 1){
            $tiporegimen = "CÓMUN";
        }
        if($this->tiporegimen == 2){
            $tiporegimen = "SIMPLIFICADO";
        }        
        return $tiporegimen;
    }
}
