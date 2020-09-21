<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\ConceptoSalarios;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConceptoSalariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Concepto Salarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="concepto-salarios-index">

   <?= $this->render('_search', ['model' => $searchModel]); ?>
   <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'codigo_salario',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'nombre_concepto',
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [
                'attribute' => 'porcentaje',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'porcentaje_tiempo_extra',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
           [
                'attribute' => 'inicio_nomina',
                'value' => function($model) {
                    $inicionomina = ConceptoSalarios::findOne($model->codigo_salario);
                    return $inicionomina->inicionomina;
                },
                'filter' => ArrayHelper::map(ConceptoSalarios::find()->all(), 'inicio_nomina', 'inicionomina'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            
            [
                'attribute' => 'prestacional',
                'value' => function($model) {
                    $prestacion = ConceptoSalarios::findOne($model->codigo_salario);
                    return $prestacion->prestacion;
                },
                'filter' => ArrayHelper::map(ConceptoSalarios::find()->all(), 'prestacional', 'prestacion'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'tipo_adicion',
                'value' => function($model) {
                    $tipoadicion = ConceptoSalarios::findOne($model->codigo_salario);
                    return $tipoadicion->tipoadicion;
                },
                'filter' => ArrayHelper::map(ConceptoSalarios::find()->all(), 'tipo_adicion', 'tipoadicion'),
                'contentOptions' => ['class' => 'col-lg-1.5'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
            ],
            
        ],
       'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]); ?>
</div>
