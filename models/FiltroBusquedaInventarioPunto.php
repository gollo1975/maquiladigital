<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroBusquedaInventarioPunto extends Model
{        
   
    public $codigo;
    public $producto;
    public $fecha_inicio;
    public $fecha_corte;
    public $inventario_inicial;
    public $punto_venta;
    public $proveedor;
    public $marca;
    public $unidades;
    public $cantidad_mostrar;


    public function rules()
    {
        return [  
           [['codigo', 'punto_venta','inventario_inicial','proveedor','marca','unidades','cantidad_mostrar'], 'integer'],
           [['fecha_inicio','fecha_corte'], 'safe'],
           ['producto', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'codigo' => 'Código producto:',
            'punto_venta' => 'Punto de venta:',
            'producto' => 'Nombre producto:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'inventario_inicial' => 'Inventario inicial:',
            'proveedor' =>'Nombre de proveedor:',
            'marca' => 'Nombre de marca:',
            'unidades' => 'Unidades a trasladar:',
            'cantidad_mostrar' => 'Registros a mostrar:',
           
        ];
    }
    
}