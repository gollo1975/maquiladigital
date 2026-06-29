<?php
use yii\bootstrap;
use yii\bootstrap\Html;
use app\models\Matriculaempresa;
use app\models\Users;

/* @var $this yii\web\View */
$cartera = app\models\CarteraEmpresa::findOne(1);
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

<?php
// Validación de cartera y alertas flash
if (isset($cartera) && !Yii::$app->user->isGuest) {
    $fecha_actual = date('Y-m-d');
    $usuario = Yii::$app->user->identity;

    if ($cartera->estado_registro == 0 && $usuario->role != 3) {
        if (strtotime($fecha_actual) > strtotime($cartera->fecha_vencimiento)) {
            Yii::$app->getSession()->setFlash('error', 
                "La empresa se encuentra en MORA con la factura Electrónica No ({$cartera->numero_factura}), " .
                "dicho documento electrónico se venció el día ({$cartera->fecha_vencimiento}). " .
                "Lo invitamos a ponerse al día con la cartera. " .
                "Fecha de suspensión de los servicios el ({$cartera->fecha_suspension})."
            );
        }
    }
}

    // Ejemplo de datos (En producción, reemplazarías esto con una consulta a tu Base de Datos)
     $ventasData = \app\models\Facturaventa::find()
        ->select(['fecha_inicio', 'SUM(totalpagar) as total'])
        ->groupBy('fecha_inicio')
        ->orderBy(['fecha_inicio' => SORT_DESC]) // Es buena práctica ordenarlos cronológicamente
        ->limit(7)
        ->asArray() // Lo convertimos a array para manipularlo más fácil y rápido
        ->all();

    // 2. Inicializamos los arreglos vacíos que irán al gráfico
    $ventasData = array_reverse($ventasData);

    // 2. Inicializamos los arreglos vacíos
    $diasVentas = [];
    $valoresVentas = [];

    $diasEnEspanol = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];
    // 3. Llenamos los arreglos con la información real de la base de datos
    // 3. Llenamos los arreglos
    foreach ($ventasData as $venta) {
        $timestamp = strtotime($venta['fecha_inicio']);
        $numeroDia = (int)date('w', $timestamp);

        // Guardamos el Día de la semana + el día del mes (Ej: "Lunes 22") para que no se repitan si hay saltos de semanas
        $diaMes = date('d', $timestamp);
        $diasVentas[] = $diasEnEspanol[$numeroDia] . ' ' . $diaMes; 

        $valoresVentas[] = (float)$venta['total'];
    }

    if (empty($diasVentas)) {
        $diasVentas = ['Sin datos'];
        $valoresVentas = [0];
    }?>

<div class="panel panel-success shadow-sm">
    <div class="panel-heading text-center" style="background-color: #136C5D; color: white; padding: 20px 0;">
        <h2 class="panel-title" style="font-size: 28px; font-weight: bold; letter-spacing: 1px;">
            <?= Html::encode($empresa->nombresistema) ?>
        </h2>
    </div>
    <!-- INICIO DE TARJETASt -->
    <div class="panel-body" style="padding: 40px 15px;">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="panel panel-info text-center custom-box" style="border-color: #2B5DB0; border-radius: 10px; overflow: hidden;">
                            <div class="panel-heading" style="background-color: #2B5DB0; color: white;">
                                <h4 class="margin-0"><span class="glyphicon glyphicon-home"></span> CLIENTES</h4>
                            </div>
                            <div class="panel-body">
                                <h2 class="text-primary" style="font-weight: bold; margin: 10px 0;"><?= count($cliente) ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="panel panel-success text-center custom-box" style="border-color: #136C5D;">
                            <div class="panel-heading" style="background-color: #136C5D; color: white;">
                                <h4 class="margin-0"><span class="glyphicon glyphicon-globe"></span> DEPARTAMENTOS</h4>
                            </div>
                            <div class="panel-body">
                                <h2 class="text-primary" style="font-weight: bold; margin: 10px 0;"><?= count($departamento) ?></h2>
                            </div>
                        </div>
                    </div>    

                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="panel panel-warning text-center custom-box" style="border-color: #f0ad4e;">
                            <div class="panel-heading" style="background-color: #f0ad4e; color: white;">
                                <h4 class="margin-0"><span class="glyphicon glyphicon-user"></span> OPERARIOS</h4>
                            </div>
                            <div class="panel-body">
                                <h2 class="text-warning" style="font-weight: bold; margin: 10px 0;"><?= count($operario) ?></h2>
                                
                            </div>
                        </div>
                    </div>  

                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="panel panel-danger text-center custom-box" style="border-color: #d9534f;">
                            <div class="panel-heading" style="background-color: #d9534f; color: white;">
                                <h4 class="margin-0"><span class="glyphicon glyphicon-file"></span> ÓRDENES COMPRA</h4>
                            </div>
                            <div class="panel-body">
                                <h2 class="text-danger" style="font-weight: bold; margin: 10px 0;"><?= count($ordenConfeccion) ?></h2>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </section> 
    </div> 
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="row" style="margin-top: 10px; margin-bottom: 30px;">
            <div class="col-md-12">
                <div class="panel panel-default shadow-sm" style="border-radius: 10px; overflow: hidden; border-color: #e7e7e7;">
                    <div class="panel-heading" style="background-color: #fcfcfc; border-bottom: 1px solid #e7e7e7; padding: 15px;">
                        <h3 class="panel-title" style="font-weight: bold; color: #136C5D;">
                            <span class="glyphicon glyphicon-stats"></span> Rendimiento de Ventas por Día
                        </h3>
                    </div>
                    <div class="panel-body" style="padding: 20px; background-color: #fff;">
                        <div style="position: relative; height:300px; width:100%">
                            <canvas id="graficoVentas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('graficoVentas').getContext('2d');

            // Pasamos los arreglos de PHP a JavaScript de forma segura con json_encode
            var etiquetasDias = <?= json_encode($diasVentas) ?>;
            var datosValores = <?= json_encode($valoresVentas) ?>;
            

            var chart = new Chart(ctx, {
                type: 'line', // Tipo de gráfico: línea para ver la tendencia diaria
                data: {
                    labels: etiquetasDias,
                    datasets: [{
                        label: 'Ventas Diarias ($)',
                        data: datosValores,
                        backgroundColor: 'rgba(19, 108, 93, 0.1)', // Color verde de tu empresa con transparencia
                        borderColor: '#136C5D', // Color sólido de la línea
                        borderWidth: 3,
                        pointBackgroundColor: '#2B5DB0', // Puntos azules en los nodos
                        pointRadius: 5,
                        tension: 0.3, // Curvatura elegante de la línea
                        fill: true // Rellena el área debajo de la línea
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false // Oculta líneas verticales para diseño más limpio
                            }
                        }
                    }
                }
            });
        });
    </script>
    
    <!-- FINAL DE TARJETAS -->
    
    <footer class="text-center text-lg-start" style="background-color: #f9f9f9; border-top: 1px solid #e7e7e7; color: #333;">
        <div class="text-center p-3" style="background-color: #136C5D; color: white; padding: 15px;">
            <h4 class="margin-0" style="letter-spacing: 2px; font-weight: bold;">DIAMANTE SJ SAS</h4>
        </div>

        <div class="container text-center text-md-left" style="padding-top: 30px; padding-bottom: 20px;">
            <div class="row">
                <div class="col-md-3 mx-auto mb-4 text-justify">
                    <h5 class="text-uppercase" style="color: #136C5D; font-weight: bold;">Nuestra Compañía</h5>
                    <hr style="width: 60px; border-top: 3px solid #136C5D; margin-top: 5px; margin-bottom: 15px; display: inline-block;">
                    <p style="color: #666; font-size: 13px; line-height: 1.6;">
                        <strong>DIAMANTE SJ SAS</strong> es una empresa especializada en diseño de software con altos estándares en tecnología y enfocada al desarrollo de productos en la web.
                    </p>
                </div>

                <div class="col-md-2 mx-auto mb-4">
                    <h5 class="text-uppercase" style="color: #136C5D; font-weight: bold;">Productos</h5>
                    <hr style="width: 60px; border-top: 3px solid #136C5D; margin-top: 5px; margin-bottom: 15px;">
                    <ul class="list-unstyled" style="font-size: 13px; line-height: 2;">
                        <li><a href="https://www.diamantesj.com.co/systime/" class="text-muted">Diamante Textil</a></li>
                        <li><a href="https://www.diamantesj.com.co/systime/" class="text-muted">Diamante Finanzas</a></li>
                        <li><a href="https://www.diamantesj.com.co/diamante-suit/" class="text-muted">Diamante Gestión</a></li>
                        <li><a href="https://www.diamantesj.com.co/diamante-erp/" class="text-muted">Diamante Belleza</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mx-auto mb-4">
                    <h5 class="text-uppercase" style="color: #136C5D; font-weight: bold;">Servicios</h5>
                    <hr style="width: 60px; border-top: 3px solid #136C5D; margin-top: 5px; margin-bottom: 15px;">
                    <ul class="list-unstyled" style="font-size: 13px; line-height: 2;">
                        <li><a href="#!" class="text-muted">Desarrollo a la medida</a></li>
                        <li><a href="#!" class="text-muted">Análisis de BD</a></li>
                        <li><a href="#!" class="text-muted">Venta de servicios TIC</a></li>
                    </ul>
                </div>

                <div class="col-md-4 mx-auto mb-md-0 mb-4 text-md-left">
                    <h5 class="text-uppercase" style="color: #136C5D; font-weight: bold;">Contacto</h5>
                    <hr style="width: 60px; border-top: 3px solid #136C5D; margin-top: 5px; margin-bottom: 15px;">
                    <p style="font-size: 13px; color: #666;"><span class="glyphicon glyphicon-map-marker text-success"></span> Medellín - Colombia</p>
                    <p style="font-size: 13px; color: #666;"><span class="glyphicon glyphicon-envelope text-success"></span> contacto@diamantesj.com.co</p>
                    <p style="font-size: 13px; color: #666;">
                    <span class="glyphicon glyphicon-phone text-success"></span><a href="https://api.whatsapp.com/send?phone=573235522823" target="_blank" class="text-muted" style="text-decoration: none;">+57 323 552 28 23</a>
                </div>
            </div>
        </div>

        <div class="text-center p-3" style="background-color: #0e5246; color: rgba(255,255,255,0.8); padding: 12px; font-size: 12px;">
            © <?= date('Y') ?> Copyright | Todos los derechos reservados | <strong>DIAMANTE SJ SAS</strong>. Version 2.0.55
        </div>
    </footer>
</div>

<style>
    
    .custom-box {
        transition: transform .2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .custom-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .margin-0 { margin: 0; }
    .text-justify { text-align: justify; }
    
    
    .custom-box {
        transition: transform .2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border-radius: 10px; /* <--- Esto redondea las esquinas */
        overflow: hidden;    /* <--- Esto evita que el fondo azul tape el redondeado */
    }
    .custom-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .margin-0 { margin: 0; }
    .text-justify { text-align: justify; }

</style>