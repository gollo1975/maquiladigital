<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operarios".
 *
 * @property int $id_operario
 * @property int $id_tipo_documento
 * @property int $documento
 * @property string $nombres
 * @property string $apellidos
 * @property int $iddepartamento
 * @property int $idmunicipio
 * @property string $celular
 * @property string $email
 * @property string $usuariosistema
 * @property int $fecha_creacion
 *
 * @property Tipodocumento $tipoDocumento
 */
class Operarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operarios';
    }
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a empleado cargada de configuración.    
	$this->nombres = strtoupper($this->nombres);
        $this->apellidos = strtoupper($this->apellidos);
	$this->nombrecompleto = strtoupper($this->nombrecompleto);
	$this->email = strtolower($this->email);
        $this->direccion_operario = strtoupper($this->direccion_operario);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_documento', 'documento', 'nombres', 'apellidos', 'iddepartamento','idmunicipio','id_horario','id_planta','id_banco_empleado','tipo_transacion'], 'required'],
            [['id_tipo_documento','estado','polivalente','vinculado','salario_base','id_horario','id_planta','id_banco_empleado','tipo_transacion','idtipo'], 'integer'],
            [['nombres', 'apellidos', 'email','direccion_operario'], 'string', 'max' => 50],
            [['celular'], 'string', 'max' => 15],
            [['iddepartamento', 'idmunicipio','tipo_cuenta','numero_cuenta','documento'], 'string'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['fecha_creacion','fecha_nacimiento','fecha_ingreso'], 'safe'],
            [['id_tipo_documento'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['id_tipo_documento' => 'id_tipo_documento']],
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
            [['idmunicipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['idmunicipio' => 'idmunicipio']],
            [['id_horario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['id_horario' => 'id_horario']],
            [['id_arl'], 'exist', 'skipOnError' => true, 'targetClass' => Arl::className(), 'targetAttribute' => ['id_arl' => 'id_arl']],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
            [['id_banco_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => BancoEmpleado::className(), 'targetAttribute' => ['id_banco_empleado' => 'id_banco_empleado']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operario' => 'Codigo',
            'id_tipo_documento' => 'Id Tipo Documento',
            'documento' => 'Documento',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'iddepartamento' => 'Iddepartamento',
            'idmunicipio' => 'Idmunicipio',
            'celular' => 'Celular',
            'email' => 'Email',
            'usuariosistema' => 'Usuariosistema',
            'fecha_creacion' => 'Fecha Creacion',
            'estado' => 'Activo',
            'vinculado' => 'Vinculado',
            'fecha_nacimiento' => 'Fecha nacimiento',
            'fecha_ingreso' => 'Fecha Ingreso',
            'salario_base' => 'Salario base:',
            'id_horario' => 'Horario:',
            'id_banco_empleado' => 'Banco:',
            'tipo_cuenta' => 'Tipo cuenta:',
            'numero_cuenta' => 'Numero de cuenta:',
            'tipo_transacion' => 'Tipo transacion:',
            'direccion_operario' => 'Direccion:',
            'idtipo' => 'Area asignada',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(TipoDocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }
    
    public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }
    public function getArl()
    {
        return $this->hasOne(Arl::className(), ['id_arl' => 'id_arl']);
    }
    public function getHorarios()
    {
        return $this->hasOne(Horario::className(), ['id_horario' => 'id_horario']);
    }
    public function getPlanta()
    {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }
    
     public function getBancoEmpleado()
    {
        return $this->hasOne(BancoEmpleado::className(), ['id_banco_empleado' => 'id_banco_empleado']);
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
    public function getTipoOperaria()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }
    
    public function identificacion_no_existe($attribute, $params)
    {
        //Buscar la identificacion en la tabla
        $table = Operarios::find()->where("documento=:documento", [":documento" => $this->documento]);
        //Si la identificacion no existe en inscritos mostrar el error
        if ($table->count() > 1)
        {
            $this->addError($attribute, "El numero de identificacion ya existe");
        }
    }
    
     public function getEstadopago()
     {
        if($this->estado == 1){
            $estado = "SI";
        }else{
            $estado = "NO";
        }
        return $estado;
    }
     public function getTipocuenta()
     {
        if($this->tipo_cuenta == 'S'){
            $cuenta = "AHORRO";
        }else{
            $cuenta = "CORRIENTE";
        }
        return $cuenta;
    }
    
    // proceso que maneja la cta de ahorro y corriente
    
    public function getTipoTransacion()
     {
        if($this->tipo_transacion == 27){
            $tipotransacion = "ABONO A CTA CORRIENTE";
        }else{
            $tipotransacion = "ABONO A CTA AHORRO";
        }
        return $tipotransacion;
    }
    
    public function getPolivalenteOperacion()
     {
        if($this->polivalente == 1){
            $polivalente = "SI";
        }else{
            $polivalente = "NO";
        }
        return $polivalente;
    }
     public function getVinculadoOperacion()
     {
        if($this->vinculado == 1){
            $vinculado = "SI";
        }else{
            $vinculado = "NO";
        }
        return $vinculado;
    }
     
    public function getNominaAlterna()
     {
        if($this->aplica_nomina_modulo == 1){
            $nominalterna = "SI";
        }else{
            $nominalterna = "NO";
        }
        return $nominalterna;
    }
            
}
