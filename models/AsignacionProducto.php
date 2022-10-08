<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asignacion_producto".
 *
 * @property int $id_asignacion
 * @property int $idcliente
 * @property string $documento
 * @property string $razonzocial
 * @property string $fecha_asignacion
 * @property string $fecha_registro
 * @property int $unidades
 * @property int $idtipo
 * @property int $orden_produccion
 * @property int $autorizado
 * @property string $usuario
 * @property int $total_orden
 *
 * @property Cliente $cliente
 * @property Ordenproducciontipo $tipo
 */
class AsignacionProducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asignacion_producto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproveedor', 'fecha_asignacion', 'idtipo'], 'required'],
            [['idproveedor', 'unidades', 'idtipo', 'orden_produccion', 'autorizado', 'total_orden'], 'integer'],
            [['fecha_asignacion', 'fecha_registro','fecha_editado'], 'safe'],
            [['documento', 'usuario','usuario_editado'], 'string', 'max' => 15],
            [['razon_social'], 'string', 'max' => 60],
            [['observacion'], 'string', 'max' => 250],
            [['idproveedor'], 'exist', 'skipOnError' => true, 'targetClass' => Proveedor::className(), 'targetAttribute' => ['idproveedor' => 'idproveedor']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_asignacion' => 'Id',
            'idproveedor' => 'Proveedor:',
            'documento' => 'Documento',
            'razon_social' => 'Razon social',
            'fecha_asignacion' => 'Fecha Asignacion',
            'fecha_registro' => 'Fecha Registro',
            'unidades' => 'Unidades',
            'idtipo' => 'Proceso',
            'orden_produccion' => 'Orden Produccion',
            'autorizado' => 'Autorizado',
            'usuario' => 'Usuario',
            'total_orden' => 'Total Orden',
            'observacion' => 'Observación:',
            'usuario_editado' => 'Usuario editado:',
            'fecha_editado' => 'Fecha editada:',
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
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }
    
    public function getEstadoautorizado() {
        if($this->autorizado == 1){
            $estado = 'SI';
        }else{
            $estado = 'NO';
        }
        return $estado;
    }
}
