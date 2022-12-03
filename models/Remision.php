<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "remision".
 *
 * @property int $id_remision
 * @property int $idordenproduccion
 * @property int $numero
 * @property int $total_tulas
 * @property double $total_exportacion
 * @property double $totalsegundas
 * @property double $total_colombia
 * @property double $total_confeccion
 * @property double $total_despachadas
 * @property string $fechacreacion
 * @property string $color
 * @property int $id_color
 *
 * @property Ordenproduccion $ordenproduccion
 * @property Color $color0
 * @property Remisiondetalle[] $remisiondetalles
 */
class Remision extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'remision';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'numero', 'total_tulas', 'id_color','cerrar_remision'], 'integer'],
            [['total_exportacion', 'totalsegundas', 'total_colombia', 'total_confeccion', 'total_despachadas'], 'number'],
            [['fechacreacion'], 'safe'],
            [['color'], 'string', 'max' => 25],
            [['idordenproduccion'], 'exist', 'skipOnError' => true, 'targetClass' => Ordenproduccion::className(), 'targetAttribute' => ['idordenproduccion' => 'idordenproduccion']],
            [['id_color'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['id_color' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_remision' => 'Remision',
            'idordenproduccion' => 'Op Interna',
            'numero' => 'Numero',
            'total_tulas' => 'Total Tulas',
            'total_exportacion' => 'Total Exportacion',
            'totalsegundas' => 'Totalsegundas',
            'total_colombia' => 'Total Colombia',
            'total_confeccion' => 'Total Confeccion',
            'total_despachadas' => 'Total Despachadas',
            'fechacreacion' => 'Fecha creacion',
            'color' => 'Color',
            'id_color' => 'Id Color',
            'cerrar_remision' => 'Cerrar remision',
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
    public function getColor0()
    {
        return $this->hasOne(Color::className(), ['id' => 'id_color']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRemisiondetalles()
    {
        return $this->hasMany(Remisiondetalle::className(), ['id_remision' => 'id_remision']);
    }
    
}
