<?php

namespace app\models;
use Yii;
use yii\base\model;
use app\models\Users;

class FormEditRegister extends model{

    public $codusuario;
    public $username;
    public $nombrecompleto;
    public $role;
    public $documentousuario;
    public $emailusuario;    
    public $activo;    
    public $id_planta;

    public function rules()
    {
        return [
            [['username', 'emailusuario', 'nombrecompleto','role','documentousuario','activo'], 'required', 'message' => 'Campo requerido'],
            ['id_planta', 'integer'],
            ['username', 'match', 'pattern' => "/^.{3,50}$/", 'message' => 'Mínimo 3 y máximo 30 caracteres'],
            ['username', 'match', 'pattern' => "/^[0-9a-z]+$/i", 'message' => 'Sólo se aceptan letras y números'],
            ['username', 'usuario_existe'],

            ['nombrecompleto', 'match', 'pattern' => "/^.{3,50}$/", 'message' => 'Mínimo 3 y máximo 50 caracteres'],
            ['nombrecompleto', 'match', 'pattern' => "/^[a-z ]+$/i", 'message' => 'Sólo se aceptan letras'],

            ['emailusuario', 'match', 'pattern' => "/^.{5,80}$/", 'message' => 'Mínimo 5 y máximo 80 caracteres'],
            ['emailusuario', 'email', 'message' => 'Formato no válido'],
            ['emailusuario', 'email_existe'],                        
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Usuario:',
            'nombrecompleto' => 'Nombre:',
            'role' => 'Tipo Usuario:',
            'documentousuario' => 'Documento Usuario:',
            'emailusuario' => 'Email:',
            'activo' => 'Estado:',
            'id_planta' => 'Nombre planta:',
        ];
    }

    public function email_existe($attribute, $params)
    {

        //Buscar el email en la tabla
        //$table = Users::find()->where("emailusuario=:emailusuario", [":emailusuario" => $this->emailusuario]);
        $table = Users::find()->where("emailusuario=:emailusuario", [":emailusuario" => $this->emailusuario])->andWhere("documentousuario!=:documentousuario", [':documentousuario' => $this->documentousuario]);
        //Si el email existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El email seleccionado existe");
        }
    }

    public function usuario_existe($attribute, $params)
    {
        //Buscar el usuario en la tabla
        //$table = Users::find()->where("username=:username", [":username" => $this->username]);
        $table = Users::find()->where("username=:username", [":username" => $this->username])->andWhere("emailusuario!=:emailusuario", [':emailusuario' => $this->emailusuario]);
        //Si el username existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El usuario seleccionado existe");
        }
    }

}