<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "punto_venta".
 *
 * @property int $id_punto
 * @property string $nombre_punto
 * @property string $direccion_punto
 * @property string $telefono
 * @property string $celular
 * @property string $email
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property string $fecha_inicio
 * @property string $user_name
 * @property string $administrador
 * @property string $fecha_registro
 * @property int $predeterminado
 * @property int $estado_punto
 *
 * @property Departamento $departamento
 * @property Municipio $municipio
 */
class PuntoVenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'punto_venta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_punto', 'iddepartamento', 'idmunicipio', 'fecha_inicio'], 'required'],
            [['fecha_inicio', 'fecha_registro'], 'safe'],
            [['predeterminado', 'estado_punto'], 'integer'],
            [['nombre_punto', 'direccion_punto', 'email', 'administrador'], 'string', 'max' => 50],
            [['telefono', 'celular', 'iddepartamento', 'idmunicipio', 'user_name'], 'string', 'max' => 15],
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
            [['idmunicipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['idmunicipio' => 'idmunicipio']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_punto' => 'Id Punto',
            'nombre_punto' => 'Nombre Punto',
            'direccion_punto' => 'Direccion Punto',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'email' => 'Email',
            'iddepartamento' => 'Iddepartamento',
            'idmunicipio' => 'Idmunicipio',
            'fecha_inicio' => 'Fecha Inicio',
            'user_name' => 'User Name',
            'administrador' => 'Administrador',
            'fecha_registro' => 'Fecha Registro',
            'predeterminado' => 'Predeterminado',
            'estado_punto' => 'Estado Punto',
        ];
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
    
    //proceso que incrita varios valores
     public function getNombrePunto()
    {
        return "{$this->nombre_punto}";
    }
    
    public function getPredeterminadoPunto() {
        if($this->predeterminado == 0){
            $predeterminadopunto = 'NO';
        }else{
            $predeterminadopunto = 'SI';
        }
        return $predeterminadopunto;
    }            
}
