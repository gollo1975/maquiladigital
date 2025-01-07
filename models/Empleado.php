<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleado".
 *
 * @property int $id_empleado
 * @property int $id_empleado_tipo
 * @property int $id_tipo_documento
 * @property int $identificacion
 * @property string $fecha_expedicion
 * @property string $ciudad_expedicion
 * @property int $dv
 * @property string $nombre1
 * @property string $nombre2
 * @property string $apellido1
 * @property string $apellido2
 * @property string $nombrecorto
 * @property string $direccion
 * @property string $telefono
 * @property string $celular
 * @property string $email
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property string $barrio
 * @property string $sexo
 * @property int $id_estado_civil
 * @property int $estatura
 * @property int $peso
 * @property int $id_rh
 * @property string $libreta_militar
 * @property string $distrito_militar
 * @property string $fecha_nacimiento
 * @property string $ciudad_nacimiento
 * @property int $contrato
 * @property string $observacion
 * @property string $fechaingreso
 * @property string $fecharetiro
 * @property int $padre_familia
 * @property int $cabeza_hogar
 * @property int $id_nivel_estudio
 * @property int $discapacidad
 * @property int $id_horario
 * @property int $cuenta_bancaria
 * @property int $tipo_cuenta
 * @property int $id_banco_empleado
 * @property int $id_centro_costo
 * @property int $id_sucursal
 * @property string $fechacreacion
 *
 * @property EmpleadoTipo $empleadoTipo
 * @property EstadoCivil $estadoCivil
 * @property Rh $rh
 * @property Municipio $ciudadNacimiento
 * @property Municipio $ciudadExpedicion
 * @property NivelEstudio $nivelEstudio
 * @property BancoEmpleado $bancoEmpleado
 * @property Departamento $departamento
 * @property Municipio $municipio
 * @property Horario $horario
 * @property Sucursal $sucursal
 * @property CentroCosto $centroCosto
 * @property EmpleadoTipo $empleadoTipo0
 * @property Tipodocumento $tipoDocumento
 * @property Fichatiempo[] $fichatiempos
 */
class Empleado extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleado';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a empleado cargada de configuración.    
	$this->nombre1 = strtoupper($this->nombre1);
	$this->nombre2 = strtoupper($this->nombre2);
        $this->apellido1 = strtoupper($this->apellido1);
        $this->apellido2 = strtoupper($this->apellido2);
	$this->nombrecorto = strtoupper($this->nombrecorto);
	$this->direccion = strtoupper($this->direccion);	
	$this->observacion = strtoupper($this->observacion);	
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado_tipo', 'id_tipo_documento', 'identificacion', 'dv', 'id_estado_civil', 'estatura', 'peso', 'id_rh', 'padre_familia', 'cabeza_hogar', 'id_nivel_estudio', 'discapacidad', 'id_horario','id_banco_empleado',
                'id_centro_costo','tipo_transacion','homologar_document','documento_pago_banco','id_forma_pago'], 'integer'],
            [['identificacion'], 'required'],
            [['fecha_expedicion', 'fecha_nacimiento', 'fechacreacion'], 'safe'],
            [['observacion','tipo_cuenta'], 'string'],
            ['cuenta_bancaria', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['cuenta_bancaria', 'usuario_crear','usuario_editar'], 'string'],
            [['ciudad_expedicion', 'iddepartamento', 'idmunicipio', 'sexo', 'libreta_militar', 'distrito_militar', 'ciudad_nacimiento'], 'string', 'max' => 15],
            [['nombre1', 'nombre2', 'apellido1', 'apellido2', 'telefono', 'celular'], 'string', 'max' => 22],
            [['nombrecorto', 'direccion', 'barrio'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 60],
            [['id_empleado_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => EmpleadoTipo::className(), 'targetAttribute' => ['id_empleado_tipo' => 'id_empleado_tipo']],
            [['id_estado_civil'], 'exist', 'skipOnError' => true, 'targetClass' => EstadoCivil::className(), 'targetAttribute' => ['id_estado_civil' => 'id_estado_civil']],
            [['id_rh'], 'exist', 'skipOnError' => true, 'targetClass' => Rh::className(), 'targetAttribute' => ['id_rh' => 'id_rh']],
            [['ciudad_nacimiento'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['ciudad_nacimiento' => 'idmunicipio']],
            [['ciudad_expedicion'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['ciudad_expedicion' => 'idmunicipio']],
            [['id_nivel_estudio'], 'exist', 'skipOnError' => true, 'targetClass' => NivelEstudio::className(), 'targetAttribute' => ['id_nivel_estudio' => 'id_nivel_estudio']],
            [['id_banco_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => BancoEmpleado::className(), 'targetAttribute' => ['id_banco_empleado' => 'id_banco_empleado']],
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
            [['id_horario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['id_horario' => 'id_horario']],
       
            [['id_centro_costo'], 'exist', 'skipOnError' => true, 'targetClass' => CentroCosto::className(), 'targetAttribute' => ['id_centro_costo' => 'id_centro_costo']],
            [['id_empleado_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => EmpleadoTipo::className(), 'targetAttribute' => ['id_empleado_tipo' => 'id_empleado_tipo']],
            [['id_tipo_documento'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['id_tipo_documento' => 'id_tipo_documento']],
            [['id_sucursal'], 'exist', 'skipOnError' => true, 'targetClass' => Sucursal::className(), 'targetAttribute' => ['id_sucursal' => 'id_sucursal']],
            [['id_forma_pago'], 'exist', 'skipOnError' => true, 'targetClass' => FormaPago::className(), 'targetAttribute' => ['id_forma_pago' => 'id_forma_pago']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_empleado' => 'Id',
            'id_empleado_tipo' => 'Empleado Tipo',
            'id_tipo_documento' => 'Tipo Documento',
            'identificacion' => 'Identificacion',
            'fecha_expedicion' => 'Fecha Expedicion',
            'ciudad_expedicion' => 'Ciudad Expedicion',
            'dv' => 'Dv',
            'nombre1' => 'Primer Nombre',
            'nombre2' => 'Segundo Nombre',
            'apellido1' => 'Primer Apellido',
            'apellido2' => 'Segundo Apellido',
            'nombrecorto' => 'Nombre completo',
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono',
            'celular' => 'Celular',
            'email' => 'Email',
            'iddepartamento' => 'Departamento Res',
            'idmunicipio' => 'Municipio Res',
            'barrio' => 'Barrio',
            'sexo' => 'Sexo',
            'id_estado_civil' => 'Estado Civil',
            'estatura' => 'Estatura (cm)',
            'peso' => 'Peso (kg)',
            'id_rh' => 'Rh',
            'libreta_militar' => 'Libreta Militar',
            'distrito_militar' => 'Distrito Militar',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'ciudad_nacimiento' => 'Ciudad Nacimiento',
            //'contrato' => 'Contrato',
            'observacion' => 'Observacion',
            'fechaingreso' => 'Fecha_ingreso',
            'fecharetiro' => 'Fecha_retiro',
            'padre_familia' => 'Padre Familia',
            'cabeza_hogar' => 'Cabeza Hogar',
            'id_nivel_estudio' => 'Nivel Estudio',
            'discapacidad' => 'Discapacidad',
            'id_horario' => 'Horario',
            'cuenta_bancaria' => 'Cuenta Bancaria',
            'tipo_cuenta' => 'Tipo Cuenta',
            'id_banco_empleado' => 'Banco Empleado',
            'id_centro_costo' => 'Centro Costo',
            'fechacreacion' => 'Fecha creacion',
            'usuario_crear' => 'Usuario_creador',
            'usuario_editar' => 'Usuario_editado',
            'tipo_transacion' => 'Tipo transación',
            'homologar_document' => 'Homologar documento:',
            'documento_pago_banco' => 'Documento pago banco:',
            'id_forma_pago' => 'Forma de pago:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleadoTipo()
    {
        return $this->hasOne(EmpleadoTipo::className(), ['id_empleado_tipo' => 'id_empleado_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoCivil()
    {
        return $this->hasOne(EstadoCivil::className(), ['id_estado_civil' => 'id_estado_civil']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormaPago()
    {
        return $this->hasOne(FormaPago::className(), ['id_forma_pago' => 'id_forma_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRh()
    {
        return $this->hasOne(Rh::className(), ['id_rh' => 'id_rh']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudadNacimiento()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'ciudad_nacimiento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudadExpedicion()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'ciudad_expedicion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNivelEstudioempleado()
    {
        return $this->hasOne(NivelEstudio::className(), ['id_nivel_estudio' => 'id_nivel_estudio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoEmpleado()
    {
        return $this->hasOne(BancoEmpleado::className(), ['id_banco_empleado' => 'id_banco_empleado']);
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
    public function getHorario()
    {
        return $this->hasOne(Horario::className(), ['id_horario' => 'id_horario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCentroCosto()
    {
        return $this->hasOne(CentroCosto::className(), ['id_centro_costo' => 'id_centro_costo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleadoTipo0()
    {
        return $this->hasOne(EmpleadoTipo::className(), ['id_empleado_tipo' => 'id_empleado_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(TipoDocumento::className(), ['id_tipo_documento' => 'id_tipo_documento']);
    }
    
    public function getSucursalempleado()
    {
        return $this->hasOne(Sucursal::className(), ['id_sucursal' => 'id_sucursal']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    
    
    public function getEmpleado()
    {
        return $this->hasMany(Empleado::className(), ['id_contrato' => 'id_contrato']);        
    }
    
    public function getNombreEmpleado()
    {
        return "{$this->nombre1} {$this->nombre2} {$this->apellido1} {$this->apellido2}";
    }
    
    public function getNombreEmpleados()
    {
        return "{$this->nombrecorto} - {$this->identificacion}";
    }
    
    public function getEmpleadocontrato()
    {
        return "{$this->nombrecorto} - {$this->identificacion} ";
    }
    
    public function getContratado()
    {
        if($this->contrato == 1){
            $contratado = "SI";
        }else{
            $contratado = "NO";
        }
        return $contratado;
    }
    
    public function getTipo()
    {
        if($this->id_empleado_tipo == 1){
            $tipo = "OPERATIVO";
        }else{
            $tipo = "ADMINISTRATIVO";
        }
        return $tipo;
    }
    
    public function getCabezaHogar()
    {
        if($this->cabeza_hogar == 1){
            $cabezaHogar = "SI";
        }else{
            $cabezaHogar = "NO";
        }
        return $cabezaHogar;
    }
    
    public function getPadreFamilia()
    {
        if($this->padre_familia == 1){
            $padreFamilia = "SI";
        }else{
            $padreFamilia = "NO";
        }
        return $padreFamilia;
    }
     public function getTipocuenta()
     {
        if($this->tipo_cuenta == 'S'){
            $tipocuenta = "AHORRO";
        }else{
            $tipocuenta = "CORRIENTE";
        }
        return $tipocuenta;
    }
    
    public function getDiscapacitado()
    {
        if($this->discapacidad == 1){
            $discapacitado = "SI";
        }else{
            $discapacitado = "NO";
        }
        return $discapacitado;
    }
    
    public function getTipoTransacion()
     {
        if($this->tipo_transacion == 27){
            $tipotransacion = "ABONO A CTA CORRIENTE";
        }else{
            $tipotransacion = "ABONO A CTA AHORRO";
        }
        return $tipotransacion;
    }
    
    public function identificacion_no_existe($attribute, $params)
    {
        //Buscar la identificacion en la tabla
        $table = Empleado::find()->where("identificacion=:identificacion", [":identificacion" => $this->identificacion]);
        //Si la identificacion no existe en inscritos mostrar el error
        if ($table->count() > 1)
        {
            $this->addError($attribute, "El numero de identificacion ya existe");
        }
    }
}
