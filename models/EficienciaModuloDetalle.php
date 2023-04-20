<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eficiencia_modulo_detalle".
 *
 * @property int $id_carga
 * @property int $id_eficiencia
 * @property int $id_balanceo
 * @property int $idordenproduccion
 * @property double $total_eficiencia_hora
 * @property double $total_eficiencia_diario
 * @property string $fecha_carga
 * @property int $total_unidades
 * @property string $usuario
 *
 * @property EficienciaModuloDiario $eficiencia
 * @property Balanceo $balanceo
 * @property Ordenproduccion $ordenproduccion
 */
class EficienciaModuloDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eficiencia_modulo_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_eficiencia'], 'required'],
            [['id_eficiencia', 'id_balanceo', 'idordenproduccion', 'total_unidades'], 'integer'],
            [['total_eficiencia_diario'], 'number'],
            [['fecha_carga'], 'safe'],
            [['usuario','hora_inicio_modulo'], 'string', 'max' => 15],
            [['id_eficiencia'], 'exist', 'skipOnError' => true, 'targetClass' => EficienciaModuloDiario::className(), 'targetAttribute' => ['id_eficiencia' => 'id_eficiencia']],
            [['id_balanceo'], 'exist', 'skipOnError' => true, 'targetClass' => Balanceo::className(), 'targetAttribute' => ['id_balanceo' => 'id_balanceo']],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_carga' => 'Id Carga',
            'id_eficiencia' => 'Id Eficiencia',
            'id_balanceo' => 'Id Balanceo',
            'idordenproduccion' => 'Idordenproduccion',
            'total_eficiencia_diario' => 'Total Eficiencia Diario',
            'fecha_carga' => 'Fecha Carga',
            'total_unidades' => 'Total Unidades',
            'usuario' => 'Usuario',
            'hora_inicio_modulo' => 'Hora inicio modulo',
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
}
