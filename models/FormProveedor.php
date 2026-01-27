<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Proveedor;
use app\models\Departamentos;
use app\models\Municipio;

/**
 * ContactForm is the model behind the contact form.
 */
class FormProveedor extends Model
{
    public $idproveedor;
    public $id_tipo_documento;
    public $cedulanit;
    public $dv;
    public $razonsocial;
    public $nombreproveedor;
    public $apellidoproveedor;
    public $nombrecorto;
    public $direccionproveedor;
    public $telefonoproveedor;
    public $celularproveedor;
    public $emailproveedor;
    public $contacto;
    public $telefonocontacto;
    public $celularcontacto;
    public $formapago;
    public $plazopago;
    public $iddepartamento;
    public $idmunicipio;
    public $nitmatricula;
    public $tiporegimen;
    public $autoretenedor;
    public $naturaleza;
    public $sociedad;
    public $observacion;
    public $fechaingreso;
    public $banco;
    public $tipocuenta;
    public $cuentanumero;
    public $genera_moda;
    public $homologar_document;
    public $nuevo_documento;

    public function rules()
    {
        return [
			
            [['id_tipo_documento','telefonoproveedor','direccionproveedor'], 'required', 'message' => 'Campo requerido'],
            ['cedulanit', 'required', 'message' => 'Campo requerido'],
            ['genera_moda', 'required', 'message' => 'Campo requerido'],
            [['genera_moda','homologar_document','nuevo_documento'], 'integer'],
            [['cedulanit','nuevo_documento'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['cedulanit', 'cedulanit_existe'],            
            [['dv'], 'string', 'max' => 1],
            ['razonsocial', 'match', 'pattern' => '/^[a-záéíóúñ0123456789\s]+$/i', 'message' => 'Sólo se aceptan letrassssss'],
            ['nombreproveedor', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['apellidoproveedor', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['apellidoproveedor', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['direccionproveedor', 'string', 'max' => 100],
            ['telefonoproveedor', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['celularproveedor', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['emailproveedor', 'email'],
	    ['emailproveedor', 'required', 'message' => 'Campo requerido'],
            ['emailproveedor', 'email_existe'],
            ['contacto', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['telefonocontacto', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['celularcontacto', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['formapago', 'required', 'message' => 'Campo requerido'],
            ['formapago', 'default'],
            ['plazopago', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['iddepartamento', 'required', 'message' => 'Campo requerido'],
            ['idmunicipio', 'required', 'message' => 'Campo requerido'],
	    [['idmunicipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['idmunicipio' => 'idmunicipio'],'message' => 'Campo requerido'],                      
            ['tiporegimen', 'required', 'message' => 'Campo requerido'],
            ['autoretenedor', 'required'],
            ['naturaleza', 'required', 'message' => 'Campo requerido'],
            ['naturaleza', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['sociedad', 'required', 'message' => 'Campo requerido'],
            ['sociedad', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['observacion', 'default'],
            ['banco', 'match', 'pattern' => '/^[ 0-9a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras y numeros'],
            ['tipocuenta', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['cuentanumero', 'match', 'pattern' => '/^[-0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_tipo_documento' => 'Tipo Identificacion:',
            'cedulanit' => 'Cedula/Nit:',
            'razonsocial' => 'Razón Social:',
            'nombreproveedor' => 'Nombres:',
            'apellidoproveedor' => 'Apellidos:',
            'direccionproveedor' => 'Dirección:',
            'telefonoproveedor' => 'Teléfono:',
            'celularproveedor' => 'celular:',
            'emailproveedor' => 'Email:',
            'contacto' => 'Contacto:',
            'telefonocontacto' => 'Telefono contacto:',
            'celularcontacto' => 'Celular contacto:',
            'formapago' => 'Forma de Pago:',
            'plazopago' => 'Plazo:',
            'iddepartamento' => 'Departamento:',
            'idmunicipio' => 'Municipio:',            
            'tiporegimen' => 'Tipo Régimen:',
            'autoretenedor' => 'Autoretenedor:',
            'naturaleza' => 'naturaleza:',
            'sociedad' => 'sociedad:',
            'dv' => '',
            'observacion' => 'Observaciones:',
            'banco' => 'Entidad Bancaria:',
            'tipocuenta' => 'Tipo Cuenta:',
            'cuentanumero' => 'Numero cuenta:',
            'genera_moda' => 'Maquila:',
            'homologar_document' => 'Homologar documento:',
            'nuevo_documento' => 'Documento pago banco:',
        ];
    }

    public function cedulanit_existe($attribute, $params)
    {
        //Buscar la cedula/nit en la tabla
        $table = Proveedor::find()->where("cedulanit=:cedulanit", [":cedulanit" => $this->cedulanit])
                                  ->andWhere("emailproveedor!=:emailproveedor", [':emailproveedor' => $this->emailproveedor]);
        //Si la identificacion existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El número de identificación ya existe");
        }
    }   

    public function email_existe($attribute, $params)
    {
        //Buscar el email en la tabla
        $table = Proveedor::find()->where("emailproveedor=:emailproveedor", [":emailproveedor" => $this->emailproveedor])
                                 ->andWhere("cedulanit!=:cedulanit", [':cedulanit' => $this->cedulanit]);
        //Si el email existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El email ya existe");
        }
    }
}
