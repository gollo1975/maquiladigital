<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Empleado;
use app\models\Departamentos;
use app\models\Municipio;

/**
 * ContactForm is the model behind the contact form.
 */
class FormEmpleado extends Model
{
    public $id_empleado;
    public $identificacion;
    public $id_empleado_tipo;
    public $fechaingreso;
    public $fecha_expedicion;
    public $ciudad_expedicion;
    public $dv;
    public $fecharetiro;
    public $sexo;
    public $id_estado_civil;
    public $nombre1;
    public $nombre2;
    public $apellido1;
    public $apellido2;
    public $iddepartamento;
    public $idmunicipio;
    public $email;
    public $direccion;
    public $telefono;
    public $celular;        
    public $contrato;
    public $id_rh;
    public $estatura;
    public $barrio;
    public $peso;
    public $libreta_militar;
    public $distrito_militar;
    public $fecha_nacimiento;
    public $ciudad_nacimiento;
    public $cabeza_hogar;
    public $padre_familia;
    public $id_tipo_documento;
    public $discapacidad;
    public $id_horario;
    public $id_banco_empleado;
    public $cuenta_bancaria;
    public $tipo_cuenta;
    public $id_centro_costo;
    public $id_sucursal;
    public $observacion;
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificacion', 'id_empleado_tipo','fecha_expedicion','ciudad_expedicion','nombre1','apellido1','iddepartamento','idmunicipio','email','id_rh','fecha_nacimiento','ciudad_nacimiento','id_tipo_documento','padre_familia','cabeza_hogar','discapacidad','id_horario','id_banco_empleado','id_sucursal','id_centro_costo','tipo_cuenta','cuenta_bancaria','sexo','id_estado_civil'], 'required', 'message' => 'Campo requerido'],
            ['identificacion', 'identificacion_existe'],
            ['email', 'email_existe'],
            [['id_empleado_tipo', 'identificacion', 'dv','id_estado_civil','estatura','peso','libreta_militar'], 'integer'],
            ['cuenta_bancaria', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['cuenta_bancaria'], 'string'],
            [['observacion','sexo','id_rh','distrito_militar','barrio','tipo_cuenta'], 'string'],            
            ['email', 'email_existe'],
            [['fecha_expedicion','fecha_nacimiento'], 'safe'],
            [['nombre1', 'nombre2', 'apellido1', 'apellido2'], 'string', 'max' => 40],
            //[['nombrecorto'], 'string', 'max' => 100],
            [['direccion', 'email'], 'string', 'max' => 120],
            [['telefono', 'celular', 'iddepartamento', 'idmunicipio'], 'string', 'max' => 45],
            [['id_empleado_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => EmpleadoTipo::className(), 'targetAttribute' => ['id_empleado_tipo' => 'id_empleado_tipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_empleado' => 'Id',
            'id_empleado_tipo' => 'Tipo Empleado',
            'identificacion' => 'Identificación',
            'dv' => 'Dv',
            'id_tipo_documento' => 'Tipo Documento',
            'nombre1' => 'Nombre1',
            'nombre2' => 'Nombre2',
            'apellido1' => 'Apellido1',
            'apellido2' => 'Apellido2',            
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'sexo' => 'Sexo',
            'email' => 'Email',
            'iddepartamento' => 'Departamento Res',
            'idmunicipio' => 'Municipio Res',
            //'contrato' => 'Contrato',
            'observacion' => 'Observacion',
            //'fechaingreso' => 'Fecha Ingreso',
            //'fecharetiro' => 'Fecha Retiro',
            'id_estado_civil' => 'Estado Civil',
            'estatura' => 'Estatura',
            'peso' => 'Peso',
            'id_rh' => 'Rh',
            'barrio' => 'Barrio',
            'libreta_militar' => 'Libreta Militar',
            'distrito_militar' => 'Distrito Militar',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'ciudad_nacimiento' => 'Ciudad Nacimiento',
            'padre_familia' => 'Padre Familia',
            'cabeza_hogar' => 'Cabeza Hogar',
            'discapacidad' => 'Discapacidad',
            'id_horario' => 'Horario',
            'cuenta_bancaria' => 'Cuenta Bancaria',
            'tipo_cuenta' => 'Tipo Cuenta',
            'id_banco_empleado' => 'Banco Empleado',
            'id_centro_costo' => 'Centro Costo',
            'id_sucursal' => 'Sucursal',
        ];
    }

    
    
    public function identificacion_existe($attribute, $params)
    {
        //Buscar la identificacion en la tabla
        $table = Empleado::find()->where("identificacion=:identificacion", [":identificacion" => $this->identificacion])->andWhere("iddepartamento!=:iddepartamento", [':iddepartamento' => $this->iddepartamento]);
        //Si la identificacion no existe en inscritos mostrar el error
        if ($table->count() > 0)
        {
            $this->addError($attribute, "El numero de identificacion ya existe");
        }
    }  

    public function email_existe($attribute, $params)
    {
        //Buscar el email en la tabla
        $table = Empleado::find()->where("email=:email", [":email" => $this->email])->andWhere("identificacion!=:identificacion", [':identificacion' => $this->identificacion]);
        //Si el email existe mostrar el error
        if ($table->count() > 0)
        {
            $this->addError($attribute, "El email ya existe");
        }
    }
    
    
}
