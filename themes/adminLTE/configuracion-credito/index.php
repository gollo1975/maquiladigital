<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\ConceptoSalarios;
use app\models\ConfiguracionCredito;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConfiguracionCreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configuracion Credito';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuracion-credito-index">

    <?= $this->render('_search', ['model' => $searchModel]); ?>
   <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'codigo_credito',
                'contentOptions' => ['class' => 'col-lg-2.5'],
            ],
           [
                'attribute' => 'nombre_credito',
                'contentOptions' => ['class' => 'col-lg-2.5'],
            ],
           [
                'attribute' => 'codigo_salario',
                'value' => function($model){
                   $codigoSalario = ConceptoSalarios::findOne($model->codigo_salario);
                   return $codigoSalario->nombre_concepto;
                },
                'filter' => ArrayHelper::map(ConceptoSalarios::find()->where(['=','debito_credito',2 ])
                                                                     ->andWhere(['=','adicion',1])
                                                                      ->all(),'codigo_salario','nombre_concepto'),
                'contentOptions' => ['class' => 'col-lg-3.8'],
            ],
           [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            
        ],
       'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading"> Registros: <spam class="badge">{totalCount}</spam></div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]); ?>
</div>
