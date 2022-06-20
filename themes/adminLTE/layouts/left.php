<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/avatar5.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->nombrecompleto ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
        
        <!-- /.search form -->

        <?=
        dmstr\widgets\Menu::widget(
                [
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                    'items' => [
                        ['label' => 'MENÚ PRINCIPAL', 'options' => ['class' => 'header']],
                        //['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                        //['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                        ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                        [
                            'label' => ' Herramientas ',
                            'icon' => 'share',
                            'url' => '#',
                            'items' => [
                                //['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                                //['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                                [
                                    'label' => 'Contratacion',
                                    'icon' => 'database',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                
                                                ['label' => 'Banco Empresa', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                                
                                                ////
                                                [
                                                'label' => 'Empleado',
                                                'icon' => 'database',
                                                'url' => '#',
                                                'items' => [
                                                    ['label' => 'Empleado', 'icon' => 'plus-square-o', 'url' => ['/empleado/indexempleado']],
                                                    ['label' => 'Estudios', 'icon' => 'plus-square-o', 'url' => ['/estudio-empleado/index']],
                                                    ['label' => 'Banco Empleado', 'icon' => 'plus-square-o', 'url' => ['/banco-empleado/index']],
                                                    ['label' => 'Centro Costo', 'icon' => 'plus-square-o', 'url' => ['/centro-costo/index']],
                                                    ['label' => 'Sucursal', 'icon' => 'plus-square-o', 'url' => ['/sucursal/index']],
                                                ]],
                                                                                                
                                                ['label' => 'Departamento', 'icon' => 'plus-square-o', 'url' => ['/departamento/index']],                                                                                                
                                                ['label' => 'Horario', 'icon' => 'plus-square-o', 'url' => ['/horario/index']],                                                
                                                ['label' => 'Municipio', 'icon' => 'plus-square-o', 'url' => ['/municipio/index']],
                                                ['label' => 'Resolución', 'icon' => 'plus-square-o', 'url' => ['/resolucion/index']],                                                                                                
                                                ['label' => 'Tipo Documento', 'icon' => 'plus-square-o', 'url' => ['/tipo-documento/index']],
                                                ['label' => 'Tipo Cargo', 'icon' => 'plus-square-o', 'url' => ['tipocargo/index']],
                                                
                                                
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [
                                            ['label' => 'Configuracion salario', 'icon' => 'plus-square-o', 'url' => ['/configuracion-salario/index']],                                            
                                            ['label' => 'Parametro contrato', 'icon' => 'plus-square-o', 'url' => ['/contrato/parametrocontrato']],    
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                              //  ['label' => 'Cliente', 'icon' => 'plus-square-o', 'url' => ['/clientes/indexconsulta']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimientos',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],   
                                                [
                                                'label' => 'Contrato',
                                                'icon' => 'database',
                                                'url' => '#',
                                                'items' => [
                                                    ['label' => 'Arl', 'icon' => 'plus-square-o', 'url' => ['arl/index']],
                                                    ['label' => 'Caja Compensación', 'icon' => 'plus-square-o', 'url' => ['/caja-compensacion/index']],
                                                    ['label' => 'Cargo', 'icon' => 'plus-square-o', 'url' => ['/cargo/index']],
                                                    ['label' => 'Cesantia', 'icon' => 'plus-square-o', 'url' => ['/cesantia/index']],                                                    
                                                    ['label' => 'Centro Trabajo', 'icon' => 'plus-square-o', 'url' => ['/centro-trabajo/index']],
                                                    ['label' => 'Contrato', 'icon' => 'plus-square-o', 'url' => ['/contrato/index']],
                                                    ['label' => 'Entidad Pensión', 'icon' => 'plus-square-o', 'url' => ['/entidad-pension/index']],
                                                    ['label' => 'Entidad Salud', 'icon' => 'plus-square-o', 'url' => ['/entidad-salud/index']],
                                                    ['label' => 'Motivo Terminación', 'icon' => 'plus-square-o', 'url' => ['/motivo-terminacion/index']],
                                                    ['label' => 'Tipo Contrato', 'icon' => 'plus-square-o', 'url' => ['tipo-contrato/index']],
                                                    ['label' => 'Tiempo servicio', 'icon' => 'plus-square-o', 'url' => ['tiempo-servicio/index']],
                                                    ['label' => 'Tipo Cotizante', 'icon' => 'plus-square-o', 'url' => ['tipo-cotizante/index']],
                                                    ['label' => 'Subtipo Cotizante', 'icon' => 'plus-square-o', 'url' => ['subtipo-cotizante/index']],
                                                ]],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'label' => 'Produccion',
                                    'icon' => 'flask',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Talla', 'icon' => 'plus-square-o', 'url' => ['talla/index']],
                                                ['label' => 'Prenda', 'icon' => 'plus-square-o', 'url' => ['prendatipo/index']],
                                                ['label' => 'Operación prenda', 'icon' => 'plus-square-o', 'url' => ['proceso-produccion/index']],
                                                ['label' => 'Tipos Orden', 'icon' => 'plus-square-o', 'url' => ['/ordenproducciontipo/index']],
                                                ['label' => 'Productos', 'icon' => 'plus-square-o', 'url' => ['/producto/index']],
                                                ['label' => 'Calificación Ficha', 'icon' => 'plus-square-o', 'url' => ['/fichatiempocalificacion/index']],
                                                ['label' => 'Colores', 'icon' => 'plus-square-o', 'url' => ['/color/index']],
                                                ['label' => 'Operarios', 'icon' => 'plus-square-o', 'url' => ['/operarios/index']],
                                                ['label' => 'Planta/Bodega', 'icon' => 'plus-square-o', 'url' => ['/planta-empresa/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Costo Laboral', 'icon' => 'plus-square-o', 'url' => ['costo-laboral/costolaboraldetalle', 'id' => 1]],
                                                ['label' => 'Costo Laboral Hora', 'icon' => 'plus-square-o', 'url' => ['costo-laboral-hora/costolaboralhora', 'id' => 1]],
                                                ['label' => 'Costo Fijo', 'icon' => 'plus-square-o', 'url' => ['costo-fijo/costofijodetalle', 'id' => 1]],
                                                ['label' => 'Costo Prod Diaria', 'icon' => 'plus-square-o', 'url' => ['costo-produccion-diaria/costodiario']],
                                                ['label' => 'Resumen Costos', 'icon' => 'plus-square-o', 'url' => ['resumen-costos/resumencostos', 'id' => 1]],
                                                ['label' => 'Ficha Tiempo', 'icon' => 'plus-square-o', 'url' => ['fichatiempo/index']],
                                                ['label' => 'Seguimiento Producción', 'icon' => 'plus-square-o', 'url' => ['seguimiento-produccion/index']],
                                                ['label' => 'Valor prenda', 'icon' => 'plus-square-o', 'url' => ['valor-prenda-unidad/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Orden Producción', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indexconsulta']],
                                                 [
                                                'label' => 'Operaciones',
                                                'icon' => 'database',
                                                'url' => '#',
                                                'items' => [
                                                    ['label' => 'Ficha Tiempo', 'icon' => 'plus-square-o', 'url' => ['fichatiempo/indexconsulta']],
                                                    ['label' => 'Seguimiento', 'icon' => 'plus-square-o', 'url' => ['seguimiento-produccion/indexconsulta']],
                                                    ['label' => 'Ficha Operaciones', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indexconsultaficha']],
                                                    ['label' => 'Operaciones x Prenda', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indexoperacionprenda']],
                                                    ['label' => 'Balanceo', 'icon' => 'plus-square-o', 'url' => ['/balanceo/indexbalanceoperario']],
                                                    ['label' => 'Reprocesos', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/searchreprocesos']],
                                                ]],
                                                ['label' => 'Unidades confeccionadas', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/consultaunidadconfeccionada']],
                                                ['label' => 'Pago x prenda', 'icon' => 'plus-square-o', 'url' => ['/valor-prenda-unidad/searchpageprenda']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            ['label' => 'Flujo de operaciones', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/produccionbalanceo']], 
                                            ['label' => 'Balanceo', 'icon' => 'plus-square-o', 'url' => ['/balanceo/index']],    
                                            ['label' => 'Pago de prenda', 'icon' => 'plus-square-o', 'url' => ['/valor-prenda-unidad/indexsoporte']], 
                                            ['label' => 'Prestamo operarios', 'icon' => 'plus-square-o', 'url' => ['/credito-operarios/index']],
                                            ['label' => 'Reprocesos', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indexreprocesoproduccion']],      
                                            ['label' => 'Costos y gastos', 'icon' => 'plus-square-o', 'url' => ['/costos-gastos-empresa/index']],      
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimientos',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Orden Producción', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/index']],
                                                 ['label' => 'Orden Tercero', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indextercero']],
                                                 ['label' => 'Entrada / Salida', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/indexentradasalida']],
                                                ['label' => 'Ficha Operaciones', 'icon' => 'plus-square-o', 'url' => ['/orden-produccion/proceso']],
                                            ],
                                        ],
                                    ],
                                ], // termina el menu produccion
                                
                                // comienza en menu de nomina
                                [
                                    'label' => 'Nomina',
                                    'icon' => 'money',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Concepto salarios', 'icon' => 'plus-square-o', 'url' => ['concepto-salarios/index']],
                                                ['label' => 'Configuracion pension', 'icon' => 'plus-square-o', 'url' => ['configuracion-pension/index']],
                                                ['label' => 'Configuracion eps', 'icon' => 'plus-square-o', 'url' => ['configuracion-eps/index']],
                                                ['label' => 'Configuracion de credito', 'icon' => 'plus-square-o', 'url' => ['/configuracion-credito/index']],
                                                ['label' => 'Periodo Pago', 'icon' => 'plus-square-o', 'url' => ['/periodo-pago/index']],
                                                ['label' => 'Grupo Pago', 'icon' => 'plus-square-o', 'url' => ['/grupo-pago/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimiento',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                              ['label' => 'Programacion nómina', 'icon' => 'plus-square-o', 'url' => ['/programacion-nomina/index']],
                                              ['label' => 'Prestaciones sociales', 'icon' => 'plus-square-o', 'url' => ['/prestaciones-sociales/index']],  
                                              ['label' => 'Vacaciones', 'icon' => 'plus-square-o', 'url' => ['/vacaciones/index']],  
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Comprobante de pago', 'icon' => 'plus-square-o', 'url' => ['/programacion-nomina/comprobantepagonomina']],
                                                ['label' => 'Liquidaciones', 'icon' => 'plus-square-o', 'url' => ['/prestaciones-sociales/comprobantepagoprestaciones']],
                                                ['label' => 'Vacaciones', 'icon' => 'plus-square-o', 'url' => ['/vacaciones/searchindex']],

                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Pago adicional permanente', 'icon' => 'plus-square-o', 'url' => ['/pago-adicional-permanente/index']],                                            
                                                ['label' => 'Pago adicional fechas', 'icon' => 'plus-square-o', 'url' => ['/pago-adicional-fecha/index']],                                            
                                                ['label' => 'Créditos', 'icon' => 'plus-square-o', 'url' => ['/credito/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [
                                              
                                                ['label' => 'Periodo de nomina', 'icon' => 'plus-square-o', 'url' => ['/periodo-nomina/indexconsulta']],
                                            ],
                                        ],
                                    ],
                                ],
                                //termina el menu de nomina
                                // comienza el menu de salud ocupacional
                                [
                                    'label' => 'Sg - Sst',
                                    'icon' => 'medkit',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Configuracion licencias', 'icon' => 'plus-square-o', 'url' => ['configuracion-licencia/index']],
                                                ['label' => 'Configuracion incapacidades', 'icon' => 'plus-square-o', 'url' => ['configuracion-incapacidad/index']],                                               
                                                ['label' => 'Diagnostico', 'icon' => 'plus-square-o', 'url' => ['diagnostico-incapacidad/index']],
                                                ['label' => 'Sintomas Covid', 'icon' => 'plus-square-o', 'url' => ['sintomas-covid/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimiento',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                              ['label' => 'Incapacidades', 'icon' => 'plus-square-o', 'url' => ['/incapacidad/index']],
                                              ['label' => 'Licencias', 'icon' => 'plus-square-o', 'url' => ['/licencia/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Control Acceso Covid', 'icon' => 'plus-square-o', 'url' => ['control-acceso/index']],

                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            ['label' => 'Pago incapacidad', 'icon' => 'plus-square-o', 'url' => ['/incapacidad/indexpagoincapacidad']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [                                              
                                                ['label' => 'Control Acceso Covid', 'icon' => 'plus-square-o', 'url' => ['control-acceso/validar']],
                                            ],
                                        ],
                                    ],
                                ],
                                 
                                //termina el menu de salud ocupacional
                                
                                //ACA COMIENZA EN MODULO DE COSTOS E INVENTARIO
                                [
                                    'label' => 'Inventarios',
                                    'icon' => 'film',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Insumos', 'icon' => 'plus-square-o', 'url' => ['/insumos/index']],
                                                
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimiento',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                              ['label' => 'Costo producto', 'icon' => 'plus-square-o', 'url' => ['/costo-producto/index']],
                                              ['label' => 'Remision x producto', 'icon' => 'plus-square-o', 'url' => ['/remision-entrega-prendas/index']],
                                              ['label' => 'Referencias', 'icon' => 'plus-square-o', 'url' => ['/referencias/index']],
                                              
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                               // ['label' => 'Control Acceso Covid', 'icon' => 'plus-square-o', 'url' => ['control-acceso/index']],

                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [                                              
                                               // ['label' => 'Periodo de nomina', 'icon' => 'plus-square-o', 'url' => ['/periodo-nomina/indexconsulta']],
                                               
                                            ],
                                        ],
                                    ],
                                ],
                                //INICIO DEL MENU CONTABILIDAD
                                 [
                                    'label' => 'Contabilidad',
                                    'icon' => 'bank',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Cuentas', 'icon' => 'plus-square-o', 'url' => ['/cuenta-pub/index']],
                                                ['label' => 'Tipo Recibo', 'icon' => 'plus-square-o', 'url' => ['/tipo-recibo/index']],
                                                ['label' => 'Tipo Compra', 'icon' => 'plus-square-o', 'url' => ['/compra-tipo/index']],
                                                ['label' => 'Concepto Compra', 'icon' => 'plus-square-o', 'url' => ['/compra-concepto/index']],
                                                ['label' => 'Tipo Comprobante Egreso', 'icon' => 'plus-square-o', 'url' => ['/comprobante-egreso-tipo/index']],
                                                ['label' => 'Tipo Comprobante (Exportar)', 'icon' => 'plus-square-o', 'url' => ['/contabilidad-comprobante-tipo/index']],
                                                ['label' => 'Proveedor', 'icon' => 'plus-square-o', 'url' => ['/proveedor/index']],
                                                ['label' => 'Doc Equivalente', 'icon' => 'plus-square-o', 'url' => ['/documento-equivalente/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Contabiizar', 'icon' => 'plus-square-o', 'url' => ['/contabilizar/contabilizar']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Recibo Caja', 'icon' => 'plus-square-o', 'url' => ['/recibocaja/indexconsulta']],
                                                ['label' => 'Compras', 'icon' => 'plus-square-o', 'url' => ['/compra/indexconsulta']],
                                                ['label' => 'Comprobante Egreso', 'icon' => 'plus-square-o', 'url' => ['/comprobante-egreso/indexconsulta']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimientos',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Recibo Caja', 'icon' => 'plus-square-o', 'url' => ['/recibocaja/index']],
                                                ['label' => 'Compras', 'icon' => 'plus-square-o', 'url' => ['/compra/index']],
                                                ['label' => 'Comprobante Egreso', 'icon' => 'plus-square-o', 'url' => ['/comprobante-egreso/index']],
                                            ],
                                        ]
                                    ],
                                ],
                                //TERMINA CONTABILIDAD
                              // mdulo de facturacion
                                [
                                    'label' => 'Facturacion',
                                    'icon' => 'dollar',
                                    'url' => '#',
                                    'items' => [
                                        [
                                            'label' => 'Administración',
                                            'icon' => 'database',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Conceptos Notas', 'icon' => 'plus-square-o', 'url' => ['conceptonota/index']],
                                                ['label' => 'Factura de Venta Tipo', 'icon' => 'plus-square-o', 'url' => ['facturaventatipo/index']],
                                                ['label' => 'Cliente', 'icon' => 'plus-square-o', 'url' => ['/clientes/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Utilidades',
                                            'icon' => 'cube',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Consultas',
                                            'icon' => 'question',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Cliente', 'icon' => 'plus-square-o', 'url' => ['/clientes/indexconsulta']],
                                                ['label' => 'Factura Venta', 'icon' => 'plus-square-o', 'url' => ['/facturaventa/indexconsulta']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Procesos',
                                            'icon' => 'exchange',
                                            'url' => '#',
                                            'items' => [
                                            //['label' => 'Banco', 'icon' => 'plus-square-o', 'url' => ['/banco/index']],                                            
                                            ],
                                        ],
                                        [
                                            'label' => 'Movimientos',
                                            'icon' => 'book',
                                            'url' => '#',
                                            'items' => [
                                                ['label' => 'Factura Venta', 'icon' => 'plus-square-o', 'url' => ['/facturaventa/index']],
                                                ['label' => 'Nota Crédito', 'icon' => 'plus-square-o', 'url' => ['/notacredito/index']],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'label' => 'General',
                                    'icon' => 'wrench',
                                    'url' => '#',
                                    'items' => [
                                        ['label' => 'Configuración', 'icon' => 'cog', 'url' => ['parametros/parametros', 'id' => 1]],
                                        ['label' => 'Empresa', 'icon' => 'nav-icon fas fa-file', 'url' => ['empresa/empresa', 'id' => 1]],
                                         [
                                        'label' => 'Contenido',
                                        'icon' => 'comment',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Formato principal', 'icon' => 'tumblr-square', 'url' => ['formato-contenido/index']],
                                        ]],
                                    ],
                                ],
                                 
                            ],
                        ],
                    ],
                ]
        )
        ?>

    </section>

</aside>
