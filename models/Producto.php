<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto".
 *
 * @property int $idproducto
 * @property int $idcliente
 * @property string $observacion
 * @property int $activo
 * @property string $fechaproceso
 * @property string $usuariosistema
 * @property string $codigo
 *
 * @property Facturaventadetalle[] $facturaventadetalles
 * @property Ordenproducciondetalle[] $ordenproducciondetalles
 * @property Cliente $cliente
 * @property Stockdescargas[] $stockdescargas
 */
class Producto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcliente', 'observacion'], 'required'],
            [['idcliente', 'activo'], 'integer'],
            ['idcliente', 'cliente_existe'],
            [['observacion','codigo'], 'string'],
            [['fechaproceso'], 'safe'],
            [['usuariosistema','codigo'], 'string', 'max' => 15],
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idproducto' => 'Id',
            'idcliente' => 'Nombre del cliente:',
            'observacion' => 'Observacion',
            'activo' => 'Activo',
            'fechaproceso' => 'Fecha Proceso',
            'usuariosistema' => 'User name:',
            'codigo' => 'Código /Referencia:',
        ];
    }      

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['idcliente' => 'idcliente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockdescargas()
    {
        return $this->hasMany(Stockdescargas::className(), ['idproducto' => 'idproducto']);
    }
    
    public function cliente_existe($attribute, $params)
    {
        //Buscar la cedula/nit en la tabla
        $table = Producto::find()->where("idcliente=:idcliente", [":idcliente" => $this->idcliente])->andWhere("fechaproceso!=:fechaproceso", [':fechaproceso' => $this->fechaproceso]);
        //Si la identificacion existe mostrar el error
        if ($table->count() == 1)
        {
            $this->addError($attribute, "El cliente ya existe");
        }
    }
    
    public function getEstado()
    {
        if($this->activo == 1){
            $activo = "SI";
        }else{
            $activo = "NO";
        }
        return $activo;
    }
    
    public function getCodigonombre()
    {
        $productoprendas = Productodetalle::find()->where(['=','idproducto',$this->idproducto])->one();
        if ($productoprendas){
            return $nombreproducto = $this->codigo.' - '.$productoprendas->prendatipo->prenda;
        }else{
            return $nombreproducto = $this->codigo.' - '."sin detalles, por favor ingresar detalles al producto";
            $this->addError($attribute, "El producto no tiene detalles");
        }
    }
        
}
