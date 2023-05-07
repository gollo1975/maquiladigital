<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PrestacionesSociales */

$this->title = 'Actualizar: ' . $model->id_adicion;
$this->params['breadcrumbs'][] = ['label' => 'Descuentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_adicion, 'url' => ['view', 'id_adicion' => $model->id_adicion]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="vacacions-update">

   <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?php  if($tipo_adicion == 1) {
          echo $this->render('_adicion', [
            'model' => $model,
            'tipo_adicion' => $tipo_adicion, 
            'id' => $id,
            'token' => $token,
           
           ]);
        }else{         
           echo $this->render('_adicion', [
            'model' => $model,
            'tipo_adicion' => $tipo_adicion,  
            'id' => $id,    
             'token' => $token,  
           
           ]);  
        }
    ?>

</div>
