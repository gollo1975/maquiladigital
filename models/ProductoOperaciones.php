<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto_operaciones".
 *
 * @property int $id_operacion
 * @property int $idproceso
 * @property int $idtipo
 * @property int $id_tipo
 * @property int $id_producto
 * @property int $segundos
 * @property double $minutos
 * @property string $fecha_creacion
 * @property string $usuario
 *
 * @property ProcesoProduccion $proceso
 * @property Ordenproducciontipo $tipo
 * @property TiposMaquinas $tipo0
 * @property CostoProducto $producto
 */
class ProductoOperaciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto_operaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idproceso', 'idtipo', 'id_tipo', 'id_producto', 'segundos'], 'integer'],
            [['minutos'], 'number'],
            [['fecha_creacion'], 'safe'],
            [['usuario'], 'string', 'max' => 15],
            [['id_producto'], 'unique'],
            [['idproceso'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoProduccion::className(), 'targetAttribute' => ['idproceso' => 'idproceso']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
            [['id_tipo'], 'exist', 'skipOnError' => true, 'targetClass' => TiposMaquinas::className(), 'targetAttribute' => ['id_tipo' => 'id_tipo']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => CostoProducto::className(), 'targetAttribute' => ['id_producto' => 'id_producto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operacion' => 'Id Operacion',
            'idproceso' => 'Idproceso',
            'idtipo' => 'Idtipo',
            'id_tipo' => 'Id Tipo',
            'id_producto' => 'Id Producto',
            'segundos' => 'Segundos',
            'minutos' => 'Minutos',
            'fecha_creacion' => 'Fecha Creacion',
            'usuario' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProceso()
    {
        return $this->hasOne(ProcesoProduccion::className(), ['idproceso' => 'idproceso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoMaquinas()
    {
        return $this->hasOne(TiposMaquinas::className(), ['id_tipo' => 'id_tipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(CostoProducto::className(), ['id_producto' => 'id_producto']);
    }
}
