<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Cliente;
use app\models\Departamentos;
use app\models\Municipio;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCliente extends Model
{
    public $idcliente;
    public $id_tipo_documento;
    public $cedulanit;
    public $dv;
    public $razonsocial;
    public $nombrecliente;
    public $apellidocliente;
    public $nombrecorto;
    public $direccioncliente;
    public $telefonocliente;
    public $celularcliente;
    public $emailcliente;
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
    public $retencioniva;
    public $retencionfuente;
    public $observacion;
    public $fechaingreso;
    public $minuto_confeccion;
    public $minuto_terminacion;
    public $proceso;
    public $email_envio_factura_dian;

    public function rules()
    {
        return [
			
            ['id_tipo_documento', 'required', 'message' => 'Campo requerido'],
             [['proceso'], 'integer'],
            ['cedulanit', 'required', 'message' => 'Campo requerido'],
            ['cedulanit', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['cedulanit', 'cedulanit_existe'],            
            [['dv'], 'string', 'max' => 1],
            ['razonsocial', 'match', 'pattern' => '/^[.-0-9a-záéíóúñ\s ]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['nombrecliente', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['apellidocliente', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['apellidocliente', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['direccioncliente', 'default'],
            ['telefonocliente', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['celularcliente', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['emailcliente','email_envio_factura_dian'], 'email'],
            [['emailcliente','email_envio_factura_dian'], 'required', 'message' => 'Campo requerido'],
            ['emailcliente', 'email_existe'],
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
            ['retencioniva', 'required', 'message' => 'Campo requerido'],
            ['retencioniva', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['retencionfuente', 'required', 'message' => 'Campo requerido'],
            ['retencionfuente', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['minuto_confeccion', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['minuto_terminacion', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['observacion', 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_tipo_documento' => 'Tipo Identificacion:',
            'cedulanit' => 'Cedula/Nit:',
            'razonsocial' => 'Razón Social:',
            'nombrecliente' => 'Nombres:',
            'apellidocliente' => 'Apellidos:',
            'direccioncliente' => 'Dirección:',
            'telefonocliente' => 'Teléfono:',
            'celularcliente' => 'celular:',
            'emailcliente' => 'Email:',
            'email_envio_factura_dian' => 'Email dian:',
            'contacto' => 'Contacto:',
            'telefonocontacto' => 'Telefono contacto:',
            'celularcontacto' => 'Celular contacto:',
            'formapago' => 'Forma de Pago:',
            'plazopago' => 'Plazo:',
            'iddepartamento' => 'Departamento:',
            'idmunicipio' => 'Municipio:',            
            'tiporegimen' => 'Tipo Régimen:',
            'autoretenedor' => 'Autoretenedor:',
            'retencioniva' => 'Retención Iva:',
            'retencionfuente' => 'Retención Fte:',
            'dv' => '',
            'observacion' => 'Observaciones:',
            'minuto_confeccion' => 'Minuto Confección:',
            'minuto_terminacion' => 'Minuto Terminación:',
            'proceso' => 'Proceso:',

        ];
    }

    public function cedulanit_existe($attribute, $params)
    {
        //Buscar la cedula/nit en la tabla
        $table = Cliente::find()->where("cedulanit=:cedulanit", [":cedulanit" => $this->cedulanit])->andWhere("emailcliente!=:emailcliente", [':emailcliente' => $this->emailcliente]);
        //Si la identificacion existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El número de identificación ya existe");
        }
    }   

    public function email_existe($attribute, $params)
    {
        //Buscar el email en la tabla
        $table = Cliente::find()->where("emailcliente=:emailcliente", [":emailcliente" => $this->emailcliente])->andWhere("cedulanit!=:cedulanit", [':cedulanit' => $this->cedulanit]);
        //Si el email existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El email ya existe");
        }
    }
}
