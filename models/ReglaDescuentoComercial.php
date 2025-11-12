<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regla_descuento_comercial".
 *
 * @property int $id_regla
 * @property int $id_inventario
 * @property string $fecha_inicio
 * @property string $fecha_final
 * @property int $tipo_descuento
 * @property int $nuevo_valor
 * @property int $estado_regla
 * @property string $user_name
 * @property string $fecha_registro
 *
 * @property InventarioPuntoVenta $inventario
 */
class ReglaDescuentoComercial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regla_descuento_comercial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inventario', 'tipo_descuento', 'nuevo_valor', 'estado_regla'], 'integer'],
            [['fecha_inicio', 'fecha_final', 'fecha_registro'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_inventario'], 'exist', 'skipOnError' => true, 'targetClass' => InventarioPuntoVenta::className(), 'targetAttribute' => ['id_inventario' => 'id_inventario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_regla' => 'Id',
            'id_inventario' => 'Codigo',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_final' => 'Fecha Final',
            'tipo_descuento' => 'Tipo Descuento',
            'nuevo_valor' => 'Nuevo Valor',
            'estado_regla' => 'Estado Regla',
            'user_name' => 'User Name',
            'fecha_registro' => 'Fecha Registro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventario()
    {
        return $this->hasOne(InventarioPuntoVenta::className(), ['id_inventario' => 'id_inventario']);
    }
    
    public function getEstadoRegla() {
        if($this->estado_regla == 0){
            $estadoregla = 'SI';
        }else{
            $estadoregla = 'NO';
        }
        return $estadoregla;
    }
    
    public function getTipoDescuento() {
        if($this->tipo_descuento == 1){
            $tipodescuento = 'PORCENTAJE';
        }else{
           $tipodescuento = 'VALORES'; 
        }
        return $tipodescuento;
        
    }
}
