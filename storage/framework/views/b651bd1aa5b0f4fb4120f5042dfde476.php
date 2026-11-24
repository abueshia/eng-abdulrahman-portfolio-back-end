<?php $__env->startSection('title', \App\Helpers\TranslationHelper::translate('Project Details')); ?>

<?php $__env->startSection('crumb'); ?>
    <?php if (isset($component)) { $__componentOriginalc2072a121b282e859e8bdea9c58b76d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2072a121b282e859e8bdea9c58b76d8 = $attributes; } ?>
<?php $component = App\View\Components\BreadCrumb::resolve(['breadcrumbs' => [
        ['text' => \App\Helpers\TranslationHelper::translate('Projects'), 'link' => route('admin.project.index')],
        ['text' => \App\Helpers\TranslationHelper::translate('Details')],
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
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card">
                    <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><?php echo e($project->title); ?></h4>
                        <?php if(auth()->guard('admin')->user()?->can('projects.edit', 'admin')): ?>
                            <a class="btn btn-primary btn-sm" href="<?php echo e(route('admin.project.edit', $project->id)); ?>">
                                <i class="fas fa-pen me-1"></i>
                                <?php echo e(\App\Helpers\TranslationHelper::translate('Edit')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body py-6">
                        <div class="row g-5">
                            <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Title in')); ?> <?php echo e(__('methods.' . $lang)); ?>:</strong>
                                    <p class="mt-1"><?php echo e($project->getTranslation('title', $key, false)); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="col-md-4">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Category')); ?>:</strong>
                                <p class="mt-1"><?php echo e($project->category?->name ?? '-'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Type')); ?>:</strong>
                                <p class="mt-1"><?php echo e($project->project_type?->label() ?? '-'); ?></p>
                            </div>
                            <div class="col-md-4">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Status')); ?>:</strong>
                                <p class="mt-1"><?php echo e($project->status?->label() ?? '-'); ?></p>
                            </div>

                            <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Short Description in')); ?> <?php echo e(__('methods.' . $lang)); ?>:</strong>
                                    <p class="mt-1"><?php echo e($project->getTranslation('short_description', $key, false)); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Challenges in')); ?> <?php echo e(__('methods.' . $lang)); ?>:</strong>
                                    <?php ($challenges = $project->getFormattedChallenges($key)); ?>
                                    <?php if(!empty($challenges)): ?>
                                        <div class="mt-2 d-flex flex-column gap-2">
                                            <?php $__currentLoopData = $challenges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="border rounded p-3 bg-light">
                                                    <?php if(!empty($ch['title'])): ?>
                                                        <div class="fw-bold text-dark mb-1"><?php echo e($ch['title']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($ch['description'])): ?>
                                                        <div class="text-muted fs-7"><?php echo e($ch['description']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="mt-1 text-muted">-</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Solution in')); ?> <?php echo e(__('methods.' . $lang)); ?>:</strong>
                                    <p class="mt-1"><?php echo e($project->getTranslation('solution', $key, false) ?: '-'); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Results in')); ?> <?php echo e(__('methods.' . $lang)); ?>:</strong>
                                    <p class="mt-1"><?php echo e($project->getTranslation('results', $key, false) ?: '-'); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <div class="col-md-12">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Technologies')); ?>:</strong>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <?php $__empty_1 = true; $__currentLoopData = $project->technologies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <span class="badge badge-light-primary"><?php echo e($tech->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Cover Image')); ?>:</strong>
                                <div class="mt-2">
                                    <?php if($project->getFirstMediaUrl('projects')): ?>
                                        <a href="<?php echo e($project->getFirstMediaUrl('projects')); ?>" target="_blank">
                                            <img src="<?php echo e($project->getFirstMediaUrl('projects')); ?>" alt="cover" class="rounded border shadow-sm" style="max-height: 180px; object-fit: cover;"/>
                                        </a>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <strong><?php echo e(\App\Helpers\TranslationHelper::translate('Gallery Screenshots')); ?>:</strong>
                                <div class="d-flex flex-wrap gap-3 mt-2">
                                    <?php $__empty_1 = true; $__currentLoopData = $project->getMedia('projects_gallery'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galleryMedia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a href="<?php echo e($galleryMedia->getUrl()); ?>" target="_blank">
                                            <img src="<?php echo e($galleryMedia->getUrl()); ?>" alt="gallery" class="rounded border shadow-sm" style="width: 140px; height: 90px; object-fit: cover;"/>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\eng-abdulrahman-portfolio-back-end\resources\views/admin/pages/project/show.blade.php ENDPATH**/ ?>