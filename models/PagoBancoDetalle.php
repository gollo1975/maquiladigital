<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_banco_detalle".
 *
 * @property int $id_detalle
 * @property int $id_pago_banco
 * @property int $tipo_documento
 * @property string $documento
 * @property string $nombres
 * @property int $tipo_transacion
 * @property int $codigo_banco
 * @property string $numero_cuenta
 * @property string $valor_transacion
 * @property string $fecha_aplicacion
 *
 * @property PagoBanco $pagoBanco
 */
class PagoBancoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_banco_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pago_banco', 'tipo_documento', 'tipo_transacion', 'codigo_banco','tipo_pago','id_colilla'], 'integer'],
            [['fecha_aplicacion'], 'safe'],
            [['documento','banco'], 'string', 'max' => 15],
            [['nombres'], 'string', 'max' => 30],
            [['concepto_documento'], 'max' => 3],
            [['numero_cuenta', 'valor_transacion'], 'string', 'max' => 17],
            [['id_pago_banco'], 'exist', 'skipOnError' => true, 'targetClass' => PagoBanco::className(), 'targetAttribute' => ['id_pago_banco' => 'id_pago_banco']],
            [['id_tipo_'], 'exist', 'skipOnError' => true, 'targetClass' => PagoBanco::className(), 'targetAttribute' => ['id_pago_banco' => 'id_pago_banco']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_detalle' => 'Id Detalle',
            'id_pago_banco' => 'Id Pago Banco',
            'tipo_documento' => 'Tipo Documento',
            'documento' => 'Documento',
            'nombres' => 'Nombres',
            'tipo_transacion' => 'Tipo Transacion',
            'codigo_banco' => 'Codigo Banco',
            'numero_cuenta' => 'Numero Cuenta',
            'valor_transacion' => 'Valor Transacion',
            'fecha_aplicacion' => 'Fecha Aplicacion',
            'tipo_pago' => 'Tipo pago',
            'id_colilla' => 'Id colilla',
            'concepto_documento' => 'concepto_documento',
            'banco' =>'Banco:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagoBanco()
    {
        return $this->hasOne(PagoBanco::className(), ['id_pago_banco' => 'id_pago_banco']);
    }

//PROCESO PARA BUSCAR EL TIPO D EPAGO
    
    public function getTipoPago()
    {
        if($this->tipo_pago == 1){
            $tipopago = 'NOMINA';
        }else{
            if($this->tipo_pago == 2){
                $tipopago = 'PRIMAS';    
            }else{
                if($this->tipo_pago == 3){
                    $tipopago = 'CESANTIAS';
                }else{
                    $tipopago = 'PRESTACION DE SERVICIOS';
                }
            }
        }
        return $tipopago;
    }
    
     public function getTipoTransacion()
    {
        if($this->tipo_transacion == 27){
            $tipotransacion = 'ABONO A CTA CORRIENTE';
        }else{
            $tipotransacion = 'ABONO A CTA DE AHORRO';
        }
        return $tipotransacion;
    }
}
