<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eficiencia_modulo_diario_detalle".
 *
 * @property int $id_entrada
 * @property int $id_eficiencia
 * @property int $id_carga
 * @property int $id_balanceo
 * @property int $idordenproduccion
 * @property int $unidades_confeccionadas
 * @property int $iddetalleorden
 * @property int $id_proceso_confeccion
 * @property int $numero_operarios
 * @property string $fecha_dia_confeccion
 * @property string $fecha_registro
 * @property string $hora_corte
 * @property string $hora_inicio_dia
 * @property int $usuario
 * @property string $observacion
 *
 * @property EficienciaModuloDiario $eficiencia
 * @property EficienciaModuloDetalle $carga
 * @property Balanceo $balanceo
 * @property Ordenproduccion $ordenproduccion
 * @property Ordenproducciondetalle $detalleorden
 * @property ProcesoConfeccionPrenda $procesoConfeccion
 */
class EficienciaModuloDiarioDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eficiencia_modulo_diario_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_eficiencia', 'id_carga', 'id_balanceo', 'idordenproduccion', 'unidades_confeccionadas', 'iddetalleorden', 'id_proceso_confeccion',
                'numero_operarios','real_confeccion','aplica_alimento'], 'integer'],
            [['fecha_dia_confeccion', 'fecha_registro'], 'safe'],
            [['hora_corte', 'hora_inicio_dia'], 'string'],
            [['observacion','usuario'], 'string', 'max' => 18],
            [['id_eficiencia'], 'exist', 'skipOnError' => true, 'targetClass' => EficienciaModuloDiario::className(), 'targetAttribute' => ['id_eficiencia' => 'id_eficiencia']],
            [['id_carga'], 'exist', 'skipOnError' => true, 'targetClass' => EficienciaModuloDetalle::className(), 'targetAttribute' => ['id_carga' => 'id_carga']],
            [['id_balanceo'], 'exist', 'skipOnError' => true, 'targetClass' => Balanceo::className(), 'targetAttribute' => ['id_balanceo' => 'id_balanceo']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['iddetalleorden'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproducciondetalle::className(), 'targetAttribute' => ['iddetalleorden' => 'iddetalleorden']],
            [['id_proceso_confeccion'], 'exist', 'skipOnError' => true, 'targetClass' => ProcesoConfeccionPrenda::className(), 'targetAttribute' => ['id_proceso_confeccion' => 'id_proceso_confeccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_entrada' => 'Id Entrada',
            'id_eficiencia' => 'Id Eficiencia',
            'id_carga' => 'Id Carga',
            'id_balanceo' => 'Id Balanceo',
            'idordenproduccion' => 'Idordenproduccion',
            'unidades_confeccionadas' => 'Unidades Confeccionadas',
            'iddetalleorden' => 'Iddetalleorden',
            'id_proceso_confeccion' => 'Id Proceso Confeccion',
            'numero_operarios' => 'Numero Operarios',
            'fecha_dia_confeccion' => 'Fecha Dia Confeccion',
            'fecha_registro' => 'Fecha Registro',
            'hora_corte' => 'Hora Corte',
            'hora_inicio_dia' => 'Hora Inicio Dia',
            'usuario' => 'Usuario',
            'observacion' => 'Observacion',
            'aplica_alimento' => 'Aplica alimento:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEficiencia()
    {
        return $this->hasOne(EficienciaModuloDiario::className(), ['id_eficiencia' => 'id_eficiencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarga()
    {
        return $this->hasOne(EficienciaModuloDetalle::className(), ['id_carga' => 'id_carga']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceo()
    {
        return $this->hasOne(Balanceo::className(), ['id_balanceo' => 'id_balanceo']);
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
    public function getDetalleorden()
    {
        return $this->hasOne(Ordenproducciondetalle::className(), ['iddetalleorden' => 'iddetalleorden']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesoConfeccion()
    {
        return $this->hasOne(ProcesoConfeccionPrenda::className(), ['id_proceso_confeccion' => 'id_proceso_confeccion']);
    }
    
    public function getAplicaAlimento() {
        if($this->aplica_alimento == 0){
            $aplicaalimento = 'NO';
        }else{
            $aplicaalimento = 'SI';
        }
        return $aplicaalimento;
    }
}
