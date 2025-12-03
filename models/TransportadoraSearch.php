<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transportadora;

/**
 * TransportadoraSearch represents the model behind the search form of `app\models\Transportadora`.
 */
class TransportadoraSearch extends Transportadora
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_transportadora', 'id_tipo_documento', 'dv'], 'integer'],
            [['nitcedula', 'razon_social', 'direccion', 'email_transportadora', 'telefono', 'celular', 'iddepartamento', 'idmunicipio', 'contacto', 'celular_contacto', 'user_name', 'fecha_registro'], 'safe'],
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
        $query = Transportadora::find();

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
            'id_transportadora' => $this->id_transportadora,
            'id_tipo_documento' => $this->id_tipo_documento,
            'dv' => $this->dv,
            'fecha_registro' => $this->fecha_registro,
        ]);

        $query->andFilterWhere(['like', 'nitcedula', $this->nitcedula])
            ->andFilterWhere(['like', 'razon_social', $this->razon_social])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'email_transportadora', $this->email_transportadora])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'celular', $this->celular])
            ->andFilterWhere(['like', 'iddepartamento', $this->iddepartamento])
            ->andFilterWhere(['like', 'idmunicipio', $this->idmunicipio])
            ->andFilterWhere(['like', 'contacto', $this->contacto])
            ->andFilterWhere(['like', 'celular_contacto', $this->celular_contacto])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
