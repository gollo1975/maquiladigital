<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormAdicionPermanente extends Model
{        
    public $id_pago_permanente;
    public $id_empleado;
    public $codigo_salario;            
    public $vlr_adicion;    
    public $aplicar_dia_laborado;
    public $aplicar_prima;
    public $aplicar_cesantias;    
    public $detalle;
    public $homologar;
    public $codigo_homologado;
    public $dias_contrato;





    public function rules()
    {
        return [            
            [['id_empleado','codigo_salario','vlr_adicion'], 'required'],
            [['vlr_adicion','aplicar_dia_laborado','aplicar_prima','aplicar_cesantias','homologar','codigo_homologado','dias_contrato'], 'integer'],
            [['detalle'],'string', 'max'=>50],
        ];
    }

    public function attributeLabels()
    {
        return [                                                       
            'id_empleado' => 'Empleado:',
            'codigo_salario' => 'Concepto salario:',
            'vlr_adicion' => 'Valor:',                        
            'aplicar_dia_laborado' => 'Dia laborado:',
            'aplicar_prima'=>'Prima semestral:',
            'aplicar_cesantias'=>'Cesantias:',            
            'detalle'=>'Detalle:',
            'homologar' => 'Homologar:',
            'codigo_homologado' => 'Concepto homologado:',
            'dias_contrato' => 'Dias contrato:'
            
            
        ];
    }
    
}
