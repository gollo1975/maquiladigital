<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Departamento;
use app\models\Municipio;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MunicipioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Municipios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="municipio-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [                
                'attribute' => 'idmunicipio',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'municipio',
                'value' => function($model){
                    $municipios = Municipio::findOne($model->idmunicipio);
                    return $municipios->municipio;
                },
                'filter' => ArrayHelper::map(Municipio::find()->all(),'municipio','municipio'),
                'contentOptions' => ['class' => 'col-lg-4'],
            ],
            [
                'attribute' => 'iddepartamento',
                'value' => function($model){
                    $departamentos = Departamento::findOne($model->iddepartamento);
                    return $departamentos->departamento;
                },
                'filter' => ArrayHelper::map(Departamento::find()->all(),'iddepartamento','departamento'),
                'contentOptions' => ['class' => 'col-lg-4'],
            ],
            [               
                'attribute' => 'activo',
                'value' => function($model){
                    $municipio = Municipio::findOne($model->idmunicipio);
                    if ($municipio->activo == 1){$estado = "SI";}else{$estado = "NO";}
                    return $estado;
                },
                'contentOptions' => ['class' => 'col-lg-2'],
            ], 					
            [
                'class' => 'yii\grid\ActionColumn', 
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],
			
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],
        
    ]); ?>
</div>


