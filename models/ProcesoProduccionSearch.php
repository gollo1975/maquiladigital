<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProcesoProduccion;

/**
 * ProcesoProduccionSearch represents the model behind the search form of `app\models\ProcesoProduccion`.
 */
class ProcesoProduccionSearch extends ProcesoProduccion
{
    public $tipoProductoId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estandarizado', 'estado','id_tipo_producto'], 'integer'],
            [['segundos', 'minutos'], 'number'],
            [['tipoProductoId','proceso'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {  
         $query = ProcesoProduccion::find();

        // Carga la relación 'tipoProducto'
        $query->joinWith(['tipoProducto']);

        $dataProvider = new ActiveDataProvider([
                'query' => $query,
        ]);
        
       
        // add conditions that should always apply here

        $dataProvider->sort->attributes['tipoProductoId'] = [
            'asc' => ['tipo_producto.concepto' => SORT_ASC],
            'desc' => ['tipo_producto.concepto' => SORT_DESC],
        ];

        $this->load($params);

         if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['=', 'tipo_producto.id_tipo_producto', $this->tipoProductoId]);
        $query->andFilterWhere(['like', 'proceso', $this->proceso]);
        $query->andFilterWhere(['=', 'segundos', $this->segundos]);
        $query->andFilterWhere(['=', 'minutos', $this->minutos]);
        $query->andFilterWhere(['=', 'estandarizado', $this->estandarizado]);
       
        return $dataProvider;
    }
}
