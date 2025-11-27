<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agentes_comerciales".
 *
 * @property int $id_agente
 * @property int $id_tipo_documento
 * @property string $nit_cedula
 * @property int $dv
 * @property string $primer_nombre
 * @property string $segundo_nombre
 * @property string $primer_apellido
 * @property string $segundo_apellido
 * @property string $nombre_completo
 * @property string $celular_agente
 * @property string $direccion
 * @property string $email_agente
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property int $estado
 * @property string $fecha_registro
 * @property string $user_name
 * @property int $gestion_diaria
 * @property int $hacer_pedido
 * @property int $hacer_recibo_caja
 *
 * @property Tipodocumento $tipoDocumento
 */
class AgentesComerciales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentes_comerciales';
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
     
        $this->primer_apellido = strtoupper($this->primer_apellido); 
        $this->segundo_apellido = strtoupper($this->segundo_apellido); 
        $this->primer_nombre = strtoupper($this->primer_nombre); 
        $this->segundo_nombre = strtoupper($this->segundo_nombre); 
        $this->direccion = strtoupper($this->direccion); 
        $this->email_agente = strtolower($this->email_agente); 
 
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_documento', 'nit_cedula', 'dv', 'primer_nombre', 'primer_apellido', 'iddepartamento', 'idmunicipio'], 'required'],
            [['id_tipo_documento', 'dv', 'estado', 'hacer_pedido', 'hacer_recibo_caja'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['nit_cedula', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'iddepartamento', 'idmunicipio', 'user_name'], 'string', 'max' => 15],
            [['nombre_completo', 'email_agente'], 'string', 'max' => 50],
            [['celular_agente'], 'string', 'max' => 12],
            [['direccion'], 'string', 'max' => 40],
            [['id_tipo_documento'], 'exist', 'skipOnError' => true, 'targetClass' => Tipodocumento::className(), 'targetAttribute' => ['id_tipo_documento' => 'id_tipo_documento']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_agente' => 'Id',
            'id_tipo_documento' => 'Tipo Documento:',
            'nit_cedula' => 'Documento:',
            'dv' => 'Dv',
            'primer_nombre' => 'Primer nombre:',
            'segundo_nombre' => 'Segundo nombre:',
            'primer_apellido' => 'Primer apellido:',
            'segundo_apellido' => 'Segundo apellido:',
            'nombre_completo' => 'Nombre completo:',
            'celular_agente' => 'Celular:',
            'direccion' => 'Direccion:',
            'email_agente' => 'Email:',
            'iddepartamento' => 'Departamento',
            'idmunicipio' => 'Mmunicipio',
            'estado' => 'Estado',
            'fecha_registro' => 'Fecha Registro',
            'user_name' => 'User Name',
            'hacer_pedido' => 'Hacer Pedido',
            'hacer_recibo_caja' => 'Hacer Recibo Caja',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento() {
        return $this->hasOne(TipoDocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }
   
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }
    
    
    
      //subprocesos
    public function getEstadoRegistro() {
        if($this->estado == 0){
            $estadoregistro = 'SI';
        }else{
            $estadoregistro = 'NO';
        }
        return $estadoregistro;
    }
    
  
     public function getGestionPedido() {
        if($this->hacer_pedido== 0){
            $gestionapedido = 'SI';
        }else{
            $gestionapedido = 'NO';
        }
        return $gestionapedido;
    }
    public function getHacerRecibo() {
        if($this->hacer_recibo_caja== 0){
            $hacerrecibo = 'SI';
        }else{
            $hacerrecibo = 'NO';
        }
        return $hacerrecibo;
    }
}
