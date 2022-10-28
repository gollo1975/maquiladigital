<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConfiguracionSalarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configuracion Salarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuracion-salario-index">

   <!--<h1><?= Html::encode($this->title) ?></h1>-->
   <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>
   
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_salario',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'salario_minimo_actual',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
           
            [
                'attribute' => 'auxilio_transporte_actual',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'auxilio_transporte_anterior',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'salario_incapacidad',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'anio',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'estado',
                'value' => function($model) {
                    $estado = \app\models\ConfiguracionSalario::findOne($model->id_salario);
                    return $estado->activo;
                },
                'filter' => ArrayHelper::map(\app\models\ConfiguracionSalario::find()->all(), 'estado', 'activo'),        
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'usuario',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: <span class="badge">{totalCount}</span></div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]);
    ?>
</div>