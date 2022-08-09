<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mecanico;

/**
 * MecanicoSearch represents the model behind the search form of `app\models\Mecanico`.
 */
class MecanicoSearch extends Mecanico
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_mecanico', 'id_tipo_documento', 'estado'], 'integer'],
            [['documento', 'nombres', 'apellidos', 'email_mecanico', 'celular', 'direccion_mecanico', 'iddepartamento', 'idmunicipio', 'usuario', 'observacion'], 'safe'],
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
        $query = Mecanico::find();

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
            'id_mecanico' => $this->id_mecanico,
            'id_tipo_documento' => $this->id_tipo_documento,
            'estado' => $this->estado,
        ]);

        $query->andFilterWhere(['like', 'documento', $this->documento])
            ->andFilterWhere(['like', 'nombres', $this->nombres])
            ->andFilterWhere(['like', 'apellidos', $this->apellidos])
            ->andFilterWhere(['like', 'email_mecanico', $this->email_mecanico])
            ->andFilterWhere(['like', 'celular', $this->celular])
            ->andFilterWhere(['like', 'direccion_mecanico', $this->direccion_mecanico])
            ->andFilterWhere(['like', 'iddepartamento', $this->iddepartamento])
            ->andFilterWhere(['like', 'idmunicipio', $this->idmunicipio])
            ->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'observacion', $this->observacion]);

        return $dataProvider;
    }
}
