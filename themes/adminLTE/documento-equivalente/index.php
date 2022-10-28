<?php
use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentoEquivalenteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documento equivalente';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="documentos-equivalentes-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'consecutivo',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [                
                'attribute' => 'identificacion',
                'contentOptions' => ['class' => 'col-lg-1'],                
            ],
            [               
                'attribute' => 'nombre_completo',
                'contentOptions' => ['class' => 'col-lg-3 '],                
            ],
            [               
                'attribute' => 'fecha',
                'contentOptions' => ['class' => 'col-lg-1'],                
            ],
            [               
                'attribute' => 'valor',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'descripcion',
                'contentOptions' => ['class' => 'col-lg-3.5 '],                
            ],				
            [
                'class' => 'yii\grid\ActionColumn',    
                'contentOptions' => ['class' => 'col-lg-1 '], 
            ],
			
        ],
        //'tableOptions' => ['class' => 'table table-success'],
        'tableOptions'=>['class'=>'table table-bordered table-success'],        
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: <span class="badge">{totalCount}</span></div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],
        
    ]); ?>
</div>


