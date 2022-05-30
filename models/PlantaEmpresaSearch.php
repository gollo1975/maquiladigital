<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PlantaEmpresa;

/**
 * PlantaEmpresaSearch represents the model behind the search form of `app\models\PlantaEmpresa`.
 */
class PlantaEmpresaSearch extends PlantaEmpresa
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_planta', 'telefono_planta', 'celular_planta'], 'integer'],
            [['nombre_planta', 'direccion_planta', 'usuariosistema', 'fecha_registro'], 'safe'],
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
        $query = PlantaEmpresa::find();

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
            'id_planta' => $this->id_planta,
           
        ]);

        $query->andFilterWhere(['like', 'nombre_planta', $this->nombre_planta])
            ->andFilterWhere(['like', 'direccion_planta', $this->direccion_planta])
            ->andFilterWhere(['like', 'telefono_planta', $this->telefono_planta]);

        return $dataProvider;
    }
}
