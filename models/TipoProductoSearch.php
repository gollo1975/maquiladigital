<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TipoProducto;

/**
 * TipoProductoSearch represents the model behind the search form of `app\models\TipoProducto`.
 */
class TipoProductoSearch extends TipoProducto
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_producto', 'estado'], 'integer'],
            [['concepto'], 'safe'],
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
        $query = TipoProducto::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_tipo_producto' => $this->id_tipo_producto,
            'estado' => $this->estado,
        ]);

        $query->andFilterWhere(['like', 'concepto', $this->concepto]);
        $query->andFilterWhere(['=', 'estado', $this->estado]);

        return $dataProvider;
    }
}
