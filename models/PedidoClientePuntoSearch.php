<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PedidoCliente;

/**
 * PedidoClientePuntoSearch represents the model behind the search form of `app\models\PedidoCliente`.
 */
class PedidoClientePuntoSearch extends PedidoCliente
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pedido', 'numero_pedido', 'idcliente', 'total_unidades', 'valor_total', 'impuesto', 'total_pedido', 'autorizado', 'pedido_cerrado', 'generar_orden'], 'integer'],
            [['fecha_pedido', 'fecha_proceso', 'user_name'], 'safe'],
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
        $query = PedidoCliente::find();

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
            'id_pedido' => $this->id_pedido,
            'numero_pedido' => $this->numero_pedido,
            'idcliente' => $this->idcliente,
            'fecha_pedido' => $this->fecha_pedido,
            'fecha_proceso' => $this->fecha_proceso,
            'total_unidades' => $this->total_unidades,
            'valor_total' => $this->valor_total,
            'impuesto' => $this->impuesto,
            'total_pedido' => $this->total_pedido,
            'autorizado' => $this->autorizado,
            'pedido_cerrado' => $this->pedido_cerrado,
            'generar_orden' => $this->generar_orden,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
