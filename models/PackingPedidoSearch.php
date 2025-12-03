<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PackingPedido;

/**
 * PackingPedidoSearch represents the model behind the search form of `app\models\PackingPedido`.
 */
class PackingPedidoSearch extends PackingPedido
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_packing', 'id_pedido', 'id_despacho', 'id_transportadora', 'cantidad_despachadas'], 'integer'],
            [['fecha_proceso', 'fecha_hora_registro', 'user_name'], 'safe'],
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
        $query = PackingPedido::find();

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
            'id_packing' => $this->id_packing,
            'id_pedido' => $this->id_pedido,
            'id_despacho' => $this->id_despacho,
            'id_transportadora' => $this->id_transportadora,
            'fecha_proceso' => $this->fecha_proceso,
            'fecha_hora_registro' => $this->fecha_hora_registro,
            'cantidad_despachadas' => $this->cantidad_despachadas,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
