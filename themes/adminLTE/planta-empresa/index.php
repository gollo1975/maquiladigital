<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PlantaEmpresaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Planta Empresas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="planta-empresa-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Planta Empresa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_planta',
            'nombre_planta',
            'direccion_planta',
            'telefono_planta',
            'celular_planta',
            //'usuariosistema',
            //'fecha_registro',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
