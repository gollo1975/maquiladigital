<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entrada_materia_prima".
 *
 * @property int $id_entrada
 * @property int $idproveedor
 * @property string $fecha_proceso
 * @property string $fecha_registro
 * @property string $numero_soporte
 * @property int $subtotal
 * @property int $impuesto
 * @property int $total_salida
 * @property int $autorizado
 * @property int $enviar_materia_prima
 * @property string $user_name_crear
 * @property string $user_name_edit
 * @property string $observacion
 *
 * @property Proveedor $proveedor
 * @property EntradaMateriaPrimaDetalle[] $entradaMateriaPrimaDetalles
 */
class EntradaMateriaPrima extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrada_materia_prima';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproveedor', 'fecha_proceso'], 'required'],
            [['idproveedor', 'subtotal', 'impuesto', 'total_salida', 'autorizado', 'enviar_materia_prima'], 'integer'],
            [['fecha_proceso', 'fecha_registro'], 'safe'],
            [['numero_soporte'], 'string', 'max' => 10],
            [['user_name_crear', 'user_name_edit'], 'string', 'max' => 15],
            [['observacion'], 'string', 'max' => 100],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrada' => 'Codigo',
            'idproveedor' => 'Proveedor:',
            'fecha_proceso' => 'Fecha proceso:',
            'fecha_registro' => 'Fecha registro:',
            'numero_soporte' => 'Numero soporte:',
            'subtotal' => 'Subtotal:',
            'impuesto' => 'Impuesto:',
            'total_salida' => 'Gran total:',
            'autorizado' => 'Autorizado',
            'enviar_materia_prima' => 'Enviar Materia Prima',
            'user_name_crear' => 'User Name Crear',
            'user_name_edit' => 'User Name Edit',
            'observacion' => 'Observacion:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProveedor()
    {
        return $this->hasOne(Proveedor::className(), ['idproveedor' => 'idproveedor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntradaMateriaPrimaDetalles()
    {
        return $this->hasMany(EntradaMateriaPrimaDetalle::className(), ['id_entrada' => 'id_entrada']);
    }
    // autorizado
    public function getAutorizadoEntrada(){
        if($this->autorizado == 0){
            $autorizadocompra = 'NO';
        }else{
            $autorizadocompra = 'SI';
        }
        return $autorizadocompra;
    }
    
    // autorizado
    public function getEnviarMateria(){
        if($this->enviar_materia_prima == 0){
            $enviarmateriaprima = 'NO';
        }else{
            $enviarmateriaprima = 'SI';
        }
        return $enviarmateriaprima;
    }
}
