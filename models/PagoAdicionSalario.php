<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_adicion_salario".
 *
 * @property int $id_pago_adicion
 * @property int $id_contrato
 * @property int $id_formato_contenido
 * @property int $vlr_adicion
 * @property string $fecha_aplicacion
 * @property string $fecha_proceso
 * @property string $usuariosistema
 * @property int $codigo_salario
 *
 * @property Contrato $contrato
 * @property FormatoContenido $formatoContenido
 * @property ConceptoSalarios $codigoSalario
 */
class PagoAdicionSalario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_adicion_salario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_contrato', 'id_formato_contenido', 'vlr_adicion', 'codigo_salario','estado_adicion'], 'integer'],
            [['id_formato_contenido', 'vlr_adicion', 'fecha_aplicacion', 'codigo_salario'], 'required'],
            [['fecha_aplicacion', 'fecha_proceso'], 'safe'],
            [['usuariosistema'], 'string', 'max' => 30],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_formato_contenido'], 'exist', 'skipOnError' => true, 'targetClass' => FormatoContenido::className(), 'targetAttribute' => ['id_formato_contenido' => 'id_formato_contenido']],
            [['codigo_salario'], 'exist', 'skipOnError' => true, 'targetClass' => ConceptoSalarios::className(), 'targetAttribute' => ['codigo_salario' => 'codigo_salario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pago_adicion' => 'Id Pago Adicion',
            'id_contrato' => 'Id Contrato',
            'id_formato_contenido' => 'Id Formato Contenido',
            'vlr_adicion' => 'Vlr Adicion',
            'fecha_aplicacion' => 'Fecha Aplicacion',
            'fecha_proceso' => 'Fecha Proceso',
            'usuariosistema' => 'Usuariosistema',
            'codigo_salario' => 'Codigo Salario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormatoContenido()
    {
        return $this->hasOne(FormatoContenido::className(), ['id_formato_contenido' => 'id_formato_contenido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodigoSalario()
    {
        return $this->hasOne(ConceptoSalarios::className(), ['codigo_salario' => 'codigo_salario']);
    }
    
    public function getEstadoActivo() {
        if($this->estado_adicion == 0){
            $estadoactivo = 'NO';
        }else {
            $estadoactivo = 'SI';
        }
        return $estadoactivo;
                
        
    }
}
