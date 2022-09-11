<?php
use yii\bootstrap;
use yii\bootstrap\Html;
use app\models\Matriculaempresa;
use app\models\Users;
/* @var $this yii\web\View */
$empresa = Matriculaempresa::findOne(1);
$operario = app\models\Operarios::find()->where(['=','estado', 1])->all();
$orden = \app\models\Ordenproduccion::find()->where(['=','cerrar_orden', 0])->all();
$cliente = \app\models\Cliente::find()->where(['=','proceso', 1])->all();
$this->title = $empresa->nombresistema;
$this->params['breadcrumbs'][] = ['label' => 'Systime', 'url' => ['index']];
?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="jumbotron">
                        <h1>Bienvenidos!</h1>
                        <img src="dist/images/logos/logomaquila.png" align ='center' width="200px;">
                   </div>
              </div>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-green-active">
                    <div class="inner">
                        <h3 style="text-align: center; color: #FFFFFF;">OPERARIOS</h3>
                         <h3 style="text-align: center;"><?= count($operario)?></h3>
                    </div>
                    <div class="icon">
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 style="text-align: center; color: #2B5DB0;">CONFECCION</h3>
                    <h3 style="text-align: center;"><?= count($orden)?></h3>
                  </div>
                  <div class="icon">
                   
                  </div>
                </div>
            </div>    
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3 style="text-align: center; color: #7458A7;">TERMINACION</h3>
                    <h3 style="text-align: center;"><?= count($orden)?></h3>
                  </div>
                 <div class="overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                  </div>
                </div>
            </div>  
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3 style="text-align: center; color: #253886;">CLIENTES</h3>
                    <h3 style="text-align: center;"><?= count($cliente)?></h3>
                  </div>
                 <div class="overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                  </div>
                </div>
            </div>  
           
        </div>
    </div>
</section> 

<footer class="main-footer">
    <strong>Copyright &copy; 2022 | Todos los derechos reservados <a href="https://www.tiendaupfashion.com"> | Maquila Digital sas</a>. <b>Version</b> 2.0</strong>
</footer>





 
          
  