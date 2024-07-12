<?php
use yii\bootstrap;
use yii\bootstrap\Html;
use app\models\Matriculaempresa;
use app\models\Users;
/* @var $this yii\web\View */
$empresa = Matriculaempresa::findOne(1);
$municipio = \app\models\Municipio::find()->all();
$departamento = app\models\Departamento::find()->all();
$operario = app\models\Operarios::find()->where(['=','estado', 1])->all();
$ordenConfeccion = \app\models\Ordenproduccion::find()->where(['=','cerrar_orden', 0])->andWhere(['=','idtipo', 1])->all();
$ordenTerminacion = \app\models\Ordenproduccion::find()->where(['=','cerrar_orden', 0])->andWhere(['=','idtipo', 2])->all();
$cliente = \app\models\Cliente::find()->where(['=','proceso', 1])->all();
$this->title = $empresa->nombresistema;
$this->params['breadcrumbs'][] = ['label' => 'Systime', 'url' => ['index']];
?>

<div class="panel panel-success">
    <div class="login-logo">
        <a href="#"><b><?= $empresa->nombresistema ?></a>
   </div>
    <div class="panel-body">
        <section class="content">
            <div class="container-fluid" style="text-align: center">
        <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-light-blue">
                        <div class="inner">
                            <h4 style="text-align: center; color: #FFFFFF;"><span class='glyphicon glyphicon-home'> <font face="arial">CLIENTES</font></span></h4>  
                            <h3 style="text-align: center;"><?= count($cliente)?></h3>
                        </div>
                        <div class="icon">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h4 style="text-align: center; color: #2B5DB0;"><span class='glyphicon glyphicon-globe'> <font face="arial">DEPARTAMENTOS</font></span></h4>  
                        <h3 style="text-align: center;"><?= count($departamento)?></h3>
                      </div>
                      <div class="icon">
                      </div>
                    </div>
                </div>    
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-olive-active">
                      <div class="inner">
                        <h4 style="text-align: center; color: white"><span class='glyphicon glyphicon-user'> <font face="arial">OPERARIOS</font></span></h4>
                        <h3 style="text-align: center;"><?= count($operario)?></h3>
                      </div>
                      <div class="overlay">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                      </div>
                    </div>
                </div>  
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                          <h4 style="text-align: center; color: #253886;"><span class='glyphicon glyphicon-user'> <font face="arial">ORDENES DE COMPRA</font></span></h4>
                        <h3 style="text-align: center;"><?= count($ordenConfeccion)?></h3>
                      </div>
                      <div class="overlay">
                        <i ></i>
                      </div>
                    </div>
                </div>  
            </div>
        </div>
    </section> 
    </div>  
    
<div class="container my-5">

  <!-- Footer -->
  <footer
          class="text-center text-lg-start text-white"
          style="background-color: white; width: 100%; text-align: center"
          >
    <!-- Section: Social media -->
    <section
             class="d-flex justify-content-between p-4"
             style="background-color: #136C5D; text-align: center"
             >
      <!-- Left -->
      <div class="me-5">
          <span class="badge"> <h4><font face="arial">DIAMANTE SJ SAS</font></h4></span>
      </div>
      <!-- Left -->

      <!-- Right -->
      
      <!-- Right -->
    </section>
    <!-- Section: Social media -->

    <!-- Section: Links  -->
    <section class="">
      <div class="container text-center text-md-start mt-5">
        <!-- Grid row -->
        <div class="row mt-3">
          <!-- Grid column -->
          <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <!-- Content -->
            <h4 class="text-uppercase fw-bold"><b><span class="badge"><font face="arial">NUESTRA COMPAÑIA</font></span></b></h4>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                style="width: 100px; background-color: black; height: 3px"
                />
            <p align="justify">
                <b>DIAMANTE SJ SAS</b>, Es una empresa especializada en diseño de software con altos estándares en tecnología y enfocada al desarrollo de productos en la web.
            </p>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
            <!-- Links -->
            <h6 class="text-uppercase fw-bold"><span class="badge"><font face="arial">PRODUCTOS</font></span></h6>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                style="width: 100px; background-color: black; height: 3px"
                />
            <p>
              <a href="#!" class="text-black">Systime </a>
            </p>
            <p>
              <a href="#!" class="text-black">Diamante ERP</a>
            </p>
            <p>
              <a href="#!" class="text-black">Producción</a>
            </p>
            <p>
              <a href="#!" class="text-black">Inventarios</a>
            </p>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
            <!-- Links -->
            <h4 class="text-uppercase fw-bold"><span class="badge"><font face="arial">SERVICIOS</font></span></h4>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                style="width: 100px; background-color: black; height: 3px"
                />
            <p>
              <a href="#!" class="text-black">Desarrollo a la medida</a>
            </p>
            <p>
              <a href="#!" class="text-black">Analisis de bd.</a>
            </p>
            <p>
              <a href="#!" class="text-black">Venta de servicios TIC</a>
            </p>
           
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
            <!-- Links -->
            <h4 class="text-uppercase fw-bold"><span class="badge"><font face="arial">CONTACTO</font></span></h4>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                style="width: 100px; background-color: black; height: 3px"
                />
            <p><i class='glyphicon glyphicon-user'></i>  Medellin - Colombia</p>
            <p><i class='glyphicon glyphicon-envelope'></i>  soporte@diamantesj.com</p>
            <p><i class='glyphicon glyphicon-phone'></i>  323 308 36 29</p>
          </div>
          <!-- Grid column -->
        </div>
        <!-- Grid row -->
      </div>
    </section>
    <!-- Section: Links  -->

    <!-- Copyright -->
    <div
         class="text-center p-4"
         style="background-color: #136C5D"
         >
        <h4 class="text-uppercase fw-bold"><span class="badge"> <font face="arial">© 2024 Copyright | Todos los dereechos reservados | DIAMANTE SJ SAS . Version 2.0</font> </span></h4>
    </div>
    <!-- Copyright -->
  </footer>
  <!-- Footer -->
</div>

</div>   


 
          
  