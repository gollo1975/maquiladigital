<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "valor_prenda_unidad".
 *
 * @property int $id_valor
 * @property int $idordenproduccion
 * @property int $idtipo
 * @property double $vlr_vinculado
 * @property double $vlr_contrato
 * @property int $estado_valor
 * @property string $fecha_proceso
 * @property string $usuariosistema
 *
 * @property Ordenproduccion $ordenproduccion
 * @property Ordenproducciontipo $tipo
 */
class ValorPrendaUnidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'valor_prenda_unidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'idtipo','id_proceso_confeccion'], 'required'],
            [['idordenproduccion', 'idtipo', 'estado_valor','autorizado','cerrar_pago','cantidad_procesada','total_confeccion','total_ajuste',
                'total_operacion','total_pagar','cantidad','cantidad_operacion','debitar_salario_dia','costo_dia_operaria'], 'integer'],
            [['vlr_vinculado', 'vlr_contrato'], 'number'],
            [['fecha_proceso','fecha_editado'], 'safe'],
            [['usuariosistema','usuario_editado'], 'string', 'max' => 20],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['idtipo'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciontipo::className(), 'targetAttribute' => ['idtipo' => 'idtipo']],
            //[['id_proceso_confeccion'], 'exist', 'skipOnError' => true, 'targetClass' => ValorPrendaUnidad::className(), 'targetAttribute' => ['id_proceso_confeccion' => 'id_proceso_confeccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_valor' => 'Id',
            'idordenproduccion' => 'Nro Orden',
            'idtipo' => 'Servicio',
            'vlr_vinculado' => 'Vlr Vinculado',
            'vlr_contrato' => 'Vlr Contrato',
            'estado_valor' => 'Activo',
            'fecha_proceso' => 'Fecha Proceso',
            'usuariosistema' => 'Usuario creador',
            'fecha_editado' => 'Fecha editado',
            'usuario_editado' => 'Usuario editado',
            'total_confeccion' => 'Total confección',
            'autorizado' => 'Autorizado',
            'cerrar_pago' => 'Cerrar pago',
            'cantidad_procesada' => 'Conf-Term.',
            'total_ajuste' => 'Total ajuste',
            'total_operacion' => 'Total operacion',
            'total_pagar' => 'Total pagar',
            'cantidad' => 'Unidades',
            'cantidad_operacion' => 'Cantidad operacion',
            'id_proceso_confeccion' => 'Tipo proceso',
            'debitar_salario_dia' => 'Debitar dia:',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenproduccion()
    {
        return $this->hasOne(Ordenproduccion::className(), ['idordenproduccion' => 'idordenproduccion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Ordenproducciontipo::className(), ['idtipo' => 'idtipo']);
    }
    
     public function getProcesoConfeccion()
    {
        return $this->hasOne(ProcesoConfeccionPrenda::className(), ['id_proceso_confeccion' => 'id_proceso_confeccion']);
    }
    
    public function getEstadovalor() {
        if ($this->estado_valor == 0){
            $estadovalor = 'SI'; 
        }else{
            $estadovalor = 'NO';
        }
        return $estadovalor;
    }
    
     public function getAutorizadoPago() {
        if ($this->autorizado == 1){
            $autorizado = 'SI'; 
        }else{
            $autorizado = 'NO';
        }
        return $autorizado;
    }
      public function getCerradoPago() {
        if ($this->cerrar_pago == 1){
            $cerradopago = 'SI'; 
        }else{
            $cerradopago = 'NO';
        }
        return $cerradopago;
    }
    
  

}
