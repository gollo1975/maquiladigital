<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroNovedadOperarios extends Model
{
    public $id_operario;
    public $documento;
    public $autorizado;
    public $cerrado;
    public $tipo_novedad;
    public $desde;
    public $hasta;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operario', 'documento','autorizado','cerrado','tipo_novedad'], 'integer'],
            ['documento', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['desde','hasta'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operario' => 'Operario:',
            'documento' => 'Documento:',
            'cerrado' => 'Cerrado:',
            'autorizado' => 'Autorizado:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
            'tipo_novedad' => 'Tipo novedad:',
            
        ];
    }
     
    
}
