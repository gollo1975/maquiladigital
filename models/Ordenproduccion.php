<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ordenproduccion".
 *
 * @property int $idordenproduccion
 * @property int $idcliente
 * @property string $fechallegada
 * @property string $fechaprocesada
 * @property string $fechaentrega
 * @property int $cantidad
 * @property double $totalorden
 * @property string $valorletras
 * @property string $observacion
 * @property int $estado
 * @property int $autorizado
 * @property string $ordenproduccion
 * @property string $ordenproduccionext
 * @property int $idtipo
 * @property string $usuariosistema
 * @property int $facturado
 * @property int $proceso_control
 * @property int $porcentaje_proceso
 * @property int $porcentaje_cantidad
 * @property int $ponderacion
 * @property int $segundosficha
 * @property string $codigoproducto
 * @property string $duracion
 *
 * @property Facturaventa[] $facturaventas
 * @property Cliente $cliente
 * @property Ordenproducciontipo $tipo
 */
class Ordenproduccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ordenproduccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcliente', 'fechallegada', 'fechaprocesada', 'fechaentrega', 'observacion', 'idtipo','ponderacion','ordenproduccion','ordenproduccionext','codigoproducto','exportacion'], 'required', 'message' => 'Campo requerido'],            
            [['idcliente', 'estado', 'idtipo','autorizado','facturado','proceso_control','cantidad','aplicar_balanceo','faltante','cerrar_orden','pagada',
                'proceso_sin_lavanderia','proceso_lavanderia','exportacion','lavanderia'], 'integer'],
            [['fechallegada', 'fechaprocesada', 'fechaentrega'], 'safe'],            
            [['totalorden','ponderacion','porcentaje_proceso','porcentaje_cantidad','segundosficha','duracion','sam_balanceo','sam_preparacion','sam_operativo','porcentaje_exportacion'], 'number'],
            [['valorletras', 'observacion','codigoproducto'], 'string'],
            [['ordenproduccion'], 'string', 'max' => 25],
            [['usuariosistema'], 'string', 'max' => 50],            
            [['idcliente'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['idcliente' => 'idcliente']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idordenproduccion' => 'OP Int.',
            'idcliente' => 'Cliente:',
            'fechallegada' => 'Fecha llegada:',
            'fechaprocesada' => 'Fecha procesada:',
            'fechaentrega' => 'Fecha entrega:',
            'cantidad' => 'Cantidad:',
            'totalorden' => 'Total Orden',
            'valorletras' => 'Valor Letras',
            'observacion' => 'Observacion:',
            'estado' => 'Estado',
            'facturado' => 'Fact.',
            'proceso_control' => 'Control:',
            'autorizado' => 'Aut.',
            'ordenproduccion' => 'Op cliente:',
            'ordenproduccionext' => 'Op Externa:',
            'idtipo' => 'Tipo servicio:',
            'usuariosistema' => 'Usuario Sistema',
            'porcentaje_proceso' => '% Proceso:',
            'porcentaje_cantidad' => '% cantidad:',
            'ponderacion' => 'Ponderación:',
            'codigoproducto' => 'Referencia:',
            'duracion' => 'Duración:',
            'aplicar_balanceo' => 'Aplicar balanceo:',
            'faltante' => 'Faltante',
            'sam_balanceo' => 'Sam Balanceo:',
            'sam_preparacion' => 'Sam Preparacion:',
            'sam_operativo' => 'Sam Operativo:',
            'exportacion' => 'Export/Ppk:',
            'porcentaje_exportacion' => '% Export/Ppk:',
            'lavanderia' => 'Lavanderia',
            'proceso_lavanderia' => 'proceso_lavanderia',
            'proceso_sin_lavanderia' => 'proceso_sin_lavanderia',
          
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaventas()
    {
        return $this->hasMany(Facturaventa::className(), ['idordenproduccion' => 'idordenproduccion']);
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
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }
//proceso que va en la creacion de la orden de produccion
    public function getOrdenProduccion()
    {
        return "Ref: {$this->codigoproducto} - Orden Producción: {$this->ordenproduccion} - Tipo: {$this->tipo->tipo}";
    }
    
    //proceso que busca la orden de produccion del cliente y el codigo en la salida
    public function getSalidaOrden()
    {
        return " Codigo: {$this->codigoproducto} - Orden Producción: {$this->ordenproduccion}";
    }
    // proceso para traer la OP y el codigo en el valor prenda
    public function getOrdenValorPrenda()
    {
        return "{$this->codigoproducto} - Op: {$this->idordenproduccion} - Servicio: {$this->tipo->tipo}";
    }
    
    public function getAutorizar()
    {
        if($this->autorizado == 1){
            $autorizar = "SI";
        }else{
            $autorizar = "NO";
        }
        return $autorizar;
    }
    
    public function getFacturar()
    {
        if($this->facturado == 1){
            $facturar = "SI";
        }else{
            $facturar = "NO";
        }
        return $facturar;
    }     
    
    public function getExportarOrden()
    {
        if($this->exportacion == 2){
            $exportar = "SI";
        }else{
            $exportar = "NO";
        }
        return $exportar;
    }
    
    public function getLavanderiaPrenda()
    {
        if($this->lavanderia == 0){
            $lavanderiaprenda = "NO";
        }else{
            $lavanderiaprenda = "SI";
        }
        return $lavanderiaprenda;
    }
        
}
