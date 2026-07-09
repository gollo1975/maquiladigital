<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\PlantaEmpresa; 
use app\models\Operarios;

$this->title = 'Control de Eficiencia & Producción';
$this->params['breadcrumbs'][] = ['label' => 'Valor Prenda Unidad', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$empresa = app\models\Matriculaempresa::findOne(1);

// Registro de Chart.js y FontAwesome para Iconos Profesionales
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');


// Estilos personalizados en línea para la apariencia del Dashboard Premium
// Estilos personalizados actualizados
// Estilos CSS Actualizados (Más claros y sutiles)
$this->registerCss("
    /* --- RESALTADO SUAVE Y PASTEL --- */
    table-danger-light, .table-danger-light td {
        background-color: #fff5f5 !important;
        color: #b71c1c !important;
        border-left: 4px solid #e53935 !important;
    }

    .row-active {
        border-left: 4px solid #28a745 !important;
    }
    
    .filter-highlight { 
        background-color: #f4faf6 !important; /* Fondo verde pastel ultra suave */
        border: 1px solid #c3e6cb !important; /* Borde verde claro muy sutil */
        border-radius: 12px; 
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.04) !important; /* Sombra casi imperceptible */
        margin-bottom: 25px;
    }
    /* Cabecera del filtro limpia y profesional */
    .filter-highlight .card-header {
        background-color: #e6f4ea !important; /* Fondo verde claro sutil para la pestaña */
        color: #1e7e34 !important; /* Texto verde oscuro elegante para excelente contraste */
        border-bottom: 1px solid #c3e6cb !important;
        font-weight: 600; 
        font-size: 1.1rem; 
        border-top-left-radius: 11px; 
        border-top-right-radius: 11px; 
        padding: 15px 20px; 
    }

    /* --- El resto de tus estilos se mantienen iguales --- */
    .dash-card { border: none; border-radius: 12px; transition: transform 0.2s; color: #fff; position: relative; overflow: hidden; }
    .dash-card:hover { transform: translateY(-3px); }
    .dash-icon { position: absolute; right: 15px; bottom: 10px; font-size: 3.5rem; opacity: 0.18; }
    .bg-gradient-success { background: linear-gradient(135deg, #28a745, #1e7e34); }
    .bg-gradient-info { background: linear-gradient(135deg, #17a2b8, #117a8b); }
    .bg-gradient-warning { background: linear-gradient(135deg, #ffc107, #d39e00); color: #212529 !important; }
    .bg-gradient-dark { background: linear-gradient(135deg, #6c757d, #495057); }
    .panel-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 25px; }
    .panel-custom .card-header { background: #fff; border-bottom: 1px solid #f2f2f2; font-weight: 600; font-size: 1.1rem; color: #495057; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px; }
    .badge-premium { padding: 6px 14px; font-size: 13px; font-weight: 600; border-radius: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
");
?>

<div class="valor-prenda-unidad-dashboard container-fluid" style="background-color: #f8f9fa; padding-top: 20px; padding-bottom: 40px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
     
        <div class="text-muted font-weight-bold">
            <i class="fa-regular fa-calendar-check me-1"></i> <?= date('d M, Y') ?>
        </div>
    </div>

    <div class="card filter-highlight"> <div class="card-header">
        <div class="card-header">
            <i class="fa-solid fa-sliders me-2"></i> Parámetros de Consulta
        </div>
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['dashboard'],
                'options' => ['class' => 'row g-3 align-items-end']
            ]); ?>

            <div class="col-md-4">
                <?= $form->field($searchModel, 'id_planta')->dropDownList(
                    ArrayHelper::map(PlantaEmpresa::find()->all(), 'id_planta', 'nombre_planta'),
                    ['prompt' => '--- Todas las Plantas ---', 'class' => 'form-select form-control form-control-lg']
                )->label('<i class="fa-solid fa-industry text-muted me-1"></i> Planta / Sucursal') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($searchModel, 'fecha')->input('date', ['class' => 'form-control form-control-lg'])->label('<i class="fa-regular fa-calendar text-muted me-1"></i> Fecha Laboral') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($searchModel, 'id_operario')->widget(\kartik\select2\Select2::classname(), [
                    'data' => ArrayHelper::map(Operarios::find()->all(), 'id_operario', 'nombrecompleto'),
                    'options' => [
                        'placeholder' => 'Buscar operario por nombre...', 
                        'class' => 'form-control-lg'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true, // Permite borrar la selección con una 'X'
                    ],
                ])->label('<i class="fa-solid fa-user-gear text-muted me-1"></i> Operario') ?>
            </div>

            <div class="panel-footer text-right">
                <?= Html::submitButton('<i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar Dashboard', ['class' => 'btn btn-primary btn-lg px-4 shadow-sm']) ?>
                <?= Html::a('<i class="fa-solid fa-rotate-left me-1"></i> Restablecer', ['dashboard'], ['class' => 'btn btn-outline-secondary btn-lg px-4 ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div> 

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dash-card bg-gradient-success p-4 shadow-sm">
                <h6 class="text-uppercase font-weight-bold mb-1 opacity-75 small">Total Facturado Empresa</h6>
                <h2 class="font-weight-bold mb-0">$ <?= number_format($kpis['total_facturado'], 2) ?></h2>
                <i class="fa-solid fa-wallet dash-icon"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dash-card bg-gradient-info p-4 shadow-sm">
                <h6 class="text-uppercase font-weight-bold mb-1 opacity-75 small">Total Ganado Operarios</h6>
                <h2 class="font-weight-bold mb-0">$ <?= number_format($kpis['total_ganado'], 2) ?></h2>
                <i class="fa-solid fa-money-bill-trend-up dash-icon"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dash-card bg-gradient-warning p-4 shadow-sm">
                <h6 class="text-uppercase font-weight-bold mb-1 opacity-75 small">Eficiencia Promedio</h6>
                <h2 class="font-weight-bold mb-0"><?= $kpis['eficiencia_promedio'] ?> %</h2>
                <i class="fa-solid fa-chart-line dash-icon"></i>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card dash-card bg-gradient-dark p-4 shadow-sm">
                <h6 class="text-uppercase font-weight-bold mb-1 opacity-75 small">Operarios Laborando</h6>
                <h2 class="font-weight-bold mb-0">  <?= $kpis['operarios_activos'] ?> Activos</h2>
                <i class="fa-solid fa-users-gear dash-icon"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card panel-custom h-100">
                <div class="card-header">
                    <i class="fa-solid fa-chart-bar text-info me-2"></i> Eficiencia Relativa por Operario
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; position: relative;">
                        <canvas id="canvasEficiencia" style="max-height: 380px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card panel-custom h-100">
                <div class="card-header">
                    <i class="fa-solid fa-list-check text-success me-2"></i> Desglose Diario de Nómina y Producción
                </div>
                <div class="card-body p-0">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => '<div class="text-muted p-3 small border-bottom"><i class="fa-solid fa-circle-info me-1"></i> Mostrando {begin} - {end} de {totalCount} registros de operarios.</div>',
                        'tableOptions' => ['class' => 'table table-hover table-striped table-borderless align-middle mb-0', 'style' => 'margin-top:0;'],

                        // === SE AGREGA EL COLOR DE LA FILA SEGÚN EL ESTADO ===
                        'rowOptions' => function ($model, $key, $index, $grid) {
                            // Si el estado existe y es diferente de 1 (Inactivo, Suspendido, etc.)
                            if (isset($model['estado']) && $model['estado'] != 1) {
                                return ['class' => 'table-danger-light']; 
                            }
                            // Si está activo (estado == 1), le asignamos la clase estándar con borde verde
                            return ['class' => 'row-active'];
                        },

                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['class' => 'text-muted small ps-3', 'style' => 'width: 50px;']
                            ],
                            [
                                'attribute' => 'operario_nombre',
                                'label' => 'Operario',
                                'headerOptions' => ['class' => 'text-muted font-weight-bold']
                            ],
                            [
                                'attribute' => 'planta_nombre',
                                'label' => 'Planta',
                                'headerOptions' => ['class' => 'text-muted font-weight-bold']
                            ],
                            [
                                'attribute' => 'eficiencia',
                                'label' => 'Eficiencia',
                                'headerOptions' => ['class' => 'text-muted font-weight-bold text-center'],
                                'contentOptions' => ['class' => 'text-center'],
                                'value' => function ($model) use ($empresa) {
                                    // CORRECCIÓN 1: Aseguramos que los valores de la BD se traten como números para la comparación
                                    $eficiencia = (float)$model['eficiencia'];
                                    $metaEmpresa = (float)$empresa->porcentaje_empresa;
                                    $metaMinima = (float)$empresa->porcentaje_minima_eficiencia;

                                    // CORRECCIÓN 2: Clases compatibles con Bootstrap 4/5 y colores de texto explícitos
                                    if ($eficiencia >= $metaEmpresa) {
                                        $clase = 'bg-success text-white text-light';
                                        $colorHex = '#28a745'; // Verde fallback
                                    } elseif ($eficiencia >= $metaMinima && $eficiencia < $metaEmpresa) {
                                        $clase = 'bg-warning text-dark';
                                        $colorHex = '#ffc107'; // Amarillo fallback
                                    } else {
                                        $clase = 'bg-danger text-white text-light';
                                        $colorHex = '#dc3545'; // Rojo fallback
                                    }

                                    // CORRECCIÓN 3: Agregamos inline style 'background-color' por si las clases de Bootstrap están siendo anuladas por tu plantilla/AdminLTE
                                    return "<span class='badge badge-premium {$clase}' style='background-color: {$colorHex} !important; color: " . ($clase === 'bg-warning text-dark' ? '#212529' : '#ffffff') . " !important; padding: 6px 12px; display: inline-block; min-width: 55px;'>" . $model['eficiencia'] . "%</span>";
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'valor_facturado',
                                'label' => 'Facturado',
                                'headerOptions' => ['class' => 'text-muted font-weight-bold text-end'],
                                'contentOptions' => ['class' => 'text-end pr-3 font-weight-bold text-success'],
                                'value' => function ($model) {
                                    return '$ ' . number_format($model['valor_facturado'], 2);
                                }
                            ],
                            [
                                'attribute' => 'valor_ganado',
                                'label' => 'Ganado',
                                'headerOptions' => ['class' => 'text-muted font-weight-bold text-end'],
                                'contentOptions' => ['class' => 'text-end pr-3 font-weight-bold text-primary'],
                                'value' => function ($model) {
                                    return '$ ' . number_format($model['valor_ganado'], 2);
                                }
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>  
</div>

<?php
$jsonLabels = json_encode($chartData['labels']);
$jsonEficiencia = json_encode($chartData['eficiencia']);

$scriptJs = <<<JS
    const ctx = document.getElementById('canvasEficiencia').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: $jsonLabels,
            datasets: [{
                label: 'Eficiencia (%)',
                data: $jsonEficiencia,
                backgroundColor: 'rgba(23, 162, 184, 0.25)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) { return value + '%'; }
                    }
                }
            }
        }
    });
JS;
$this->registerJs($scriptJs);
?>

    <?php
// ... Tu código anterior de Chart.js se mantiene igual aquí ...

$scriptJs = <<<JS
    // --- Código de tu Chart.js (déjalo aquí) ---
    // ...

    // --- NUEVO: Recarga automática inteligente cada 1 minuto (60000 ms) ---
    setInterval(function() {
        // Captura el formulario de filtros actual
        const form = document.querySelector('.filter-highlight form');
        
        if (form) {
            // Convierte los datos del formulario en parámetros de URL
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Recarga la página manteniendo los filtros aplicados suavemente
            window.location.href = window.location.pathname + '?' + params.toString();
        } else {
            // Si por alguna razón no encuentra el formulario, recarga la URL actual
            window.location.reload();
        }
    }, 60000); 
JS;
$this->registerJs($scriptJs);
?>