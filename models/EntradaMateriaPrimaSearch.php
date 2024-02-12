<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EntradaMateriaPrima;

/**
 * EntradaMateriaPrimaSearch represents the model behind the search form of `app\models\EntradaMateriaPrima`.
 */
class EntradaMateriaPrimaSearch extends EntradaMateriaPrima
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_entrada', 'idproveedor', 'subtotal', 'impuesto', 'total_salida', 'autorizado', 'enviar_materia_prima'], 'integer'],
            [['fecha_proceso', 'fecha_registro', 'numero_soporte', 'user_name_crear', 'user_name_edit', 'observacion'], 'safe'],
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
        $query = EntradaMateriaPrima::find();

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
            'id_entrada' => $this->id_entrada,
            'idproveedor' => $this->idproveedor,
            'fecha_proceso' => $this->fecha_proceso,
            'fecha_registro' => $this->fecha_registro,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'total_salida' => $this->total_salida,
            'autorizado' => $this->autorizado,
            'enviar_materia_prima' => $this->enviar_materia_prima,
        ]);

        $query->andFilterWhere(['like', 'numero_soporte', $this->numero_soporte])
            ->andFilterWhere(['like', 'user_name_crear', $this->user_name_crear])
            ->andFilterWhere(['like', 'user_name_edit', $this->user_name_edit])
            ->andFilterWhere(['like', 'observacion', $this->observacion]);

        return $dataProvider;
    }
}
