<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DespachoPedidos;

/**
 * DespachoPedidosSearch represents the model behind the search form of `app\models\DespachoPedidos`.
 */
class DespachoPedidosSearch extends DespachoPedidos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_despacho', 'id_pedido', 'idcliente', 'cantidad_despachada', 'user_name', 'subtotal', 'impuesto', 'total_despacho'], 'integer'],
            [['fecha_despacho', 'fecha_hora_registro'], 'safe'],
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
        $query = DespachoPedidos::find();

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
            'id_despacho' => $this->id_despacho,
            'id_pedido' => $this->id_pedido,
            'idcliente' => $this->idcliente,
            'fecha_despacho' => $this->fecha_despacho,
            'cantidad_despachada' => $this->cantidad_despachada,
            'fecha_hora_registro' => $this->fecha_hora_registro,
            'user_name' => $this->user_name,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total_despacho' => $this->total_despacho,
        ]);

        return $dataProvider;
    }
}
