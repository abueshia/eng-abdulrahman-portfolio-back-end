<?php $__env->startSection('title', \App\Helpers\TranslationHelper::translate('Testimonials')); ?>

<?php $__env->startSection('crumb'); ?>
    <?php if (isset($component)) { $__componentOriginalc2072a121b282e859e8bdea9c58b76d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2072a121b282e859e8bdea9c58b76d8 = $attributes; } ?>
<?php $component = App\View\Components\BreadCrumb::resolve(['breadcrumbs' => [
        ['text' => \App\Helpers\TranslationHelper::translate('Testimonials'), 'link' => route('admin.testimonial.index')],
        ['text' => \App\Helpers\TranslationHelper::translate('List')],
    ],'button' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bread-crumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\BreadCrumb::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2072a121b282e859e8bdea9c58b76d8)): ?>
<?php $attributes = $__attributesOriginalc2072a121b282e859e8bdea9c58b76d8; ?>
<?php unset($__attributesOriginalc2072a121b282e859e8bdea9c58b76d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2072a121b282e859e8bdea9c58b76d8)): ?>
<?php $component = $__componentOriginalc2072a121b282e859e8bdea9c58b76d8; ?>
<?php unset($__componentOriginalc2072a121b282e859e8bdea9c58b76d8); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <?php if(isset($pending_reviews) && $pending_reviews > 0): ?>
                <div class="alert alert-dismissible bg-light-warning d-flex align-items-center p-5 mb-7 border border-warning">
                    <i class="ki-duotone ki-notification-bing fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-warning fw-bold"><?php echo e(\App\Helpers\TranslationHelper::translate('Pending Client Reviews')); ?></h4>
                        <span class="text-gray-700">
                            <?php echo e(\App\Helpers\TranslationHelper::translate('There are')); ?> <strong><?php echo e($pending_reviews); ?></strong> <?php echo e(\App\Helpers\TranslationHelper::translate('new client reviews submitted from the website awaiting your review and approval.')); ?>

                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card-toolbar w-100 mb-7">
                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3">
                    <div class="left-part d-flex gap-3">
                        <?php if(auth()->guard('admin')->user()?->can('testimonial.create', 'admin')): ?>
                            <a href="<?php echo e(route('admin.testimonial.create')); ?>" class="btn btn-primary text-gray-800">
                                <i class="ki-duotone ki-plus fs-2 text-gray-800"></i>
                                <?php echo e(\App\Helpers\TranslationHelper::translate('Create Testimonial')); ?>

                            </a>
                        <?php endif; ?>

                        <?php if(auth()->guard('admin')->user()?->can('testimonial.sort', 'admin')): ?>
                            <a href="<?php echo e(route('admin.testimonial.reorder')); ?>" class="btn btn-light-primary">
                                <i class="ki-solid ki-sort fs-2"></i>
                                <?php echo e(\App\Helpers\TranslationHelper::translate('Sort Items')); ?>

                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="right-part d-flex align-items-center gap-3">
                        <select id="filter_source" class="form-select form-select-solid form-select-sm w-150px">
                            <option value=""><?php echo e(\App\Helpers\TranslationHelper::translate('All Sources')); ?></option>
                            <option value="website"><?php echo e(\App\Helpers\TranslationHelper::translate('Website')); ?></option>
                            <option value="dashboard"><?php echo e(\App\Helpers\TranslationHelper::translate('Dashboard')); ?></option>
                        </select>

                        <select id="filter_status" class="form-select form-select-solid form-select-sm w-150px">
                            <option value=""><?php echo e(\App\Helpers\TranslationHelper::translate('All Status')); ?></option>
                            <option value="pending"><?php echo e(\App\Helpers\TranslationHelper::translate('Pending Approval')); ?></option>
                            <option value="active"><?php echo e(\App\Helpers\TranslationHelper::translate('Active')); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-rounded align-middle table-striped border gy-5 gs-7 dataTable no-footer"
                           id="kt_table_testimonials">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Client Name')); ?></th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Project')); ?></th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Rating')); ?></th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Source')); ?></th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Status')); ?></th>
                            <th><?php echo e(\App\Helpers\TranslationHelper::translate('Actions')); ?></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('admin.layouts.delete-modal', ['action_message' => \App\Helpers\TranslationHelper::translate('This Item')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('admin_js'); ?>
    <script src="<?php echo e(asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>
    <script>
        const table = $('#kt_table_testimonials').DataTable({
            serverSide: true,
            processing: true,
            fixedHeader: true,
            orderCellsTop: true,
            language: {
                <?php if(App::getLocale() === 'ar'): ?>
                url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json",
                <?php else: ?>
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json",
                <?php endif; ?>
                search: "",
                searchPlaceholder: "<?php echo e(App::getLocale() === 'ar' ? 'بحث...' : 'Search...'); ?>"
            },
            dom: `<'row align-items-center mb-5'<'col-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-12 col-md-6 d-flex align-items-center justify-content-end'B>>
                  <'row'<'col-sm-12'tr>>
                  <'row mt-5'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end dataTables_pager'lp>>`,
            buttons: ['print', 'copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[0, "asc"]],
            ajax: {
                url: '<?php echo e(route('admin.testimonial.table')); ?>',
                data: function (d) {
                    d.source = $('#filter_source').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', defaultContent: '-'},
                {data: 'name', name: 'name', defaultContent: '-'},
                {data: 'project', name: 'project', defaultContent: '-'},
                {data: 'rating', name: 'rating', defaultContent: '-'},
                {data: 'source', name: 'source', defaultContent: '-'},
                {data: 'is_active', name: 'is_active', defaultContent: '-'},
                {data: 'action', orderable: false, searchable: false, className: 'text-center'}
            ],
            createdRow: function (row, data) {
                $(row).attr('id', 'row-' + data['id']);
            }
        });

        $('#filter_source, #filter_status').on('change', function () {
            table.ajax.reload();
        });
    </script>
    <script src="<?php echo e(asset('dashboard/assets/js/delete-item.js')); ?>" type="text/javascript"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\eng-abdulrahman-portfolio-back-end\resources\views/admin/pages/testimonial/index.blade.php ENDPATH**/ ?>