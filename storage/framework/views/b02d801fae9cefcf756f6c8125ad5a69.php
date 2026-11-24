<?php if($method === 'PUT'): ?>
    <?php $__env->startSection('title', \App\Helpers\TranslationHelper::translate('Edit Experience')); ?>
<?php else: ?>
    <?php $__env->startSection('title', \App\Helpers\TranslationHelper::translate('Create Experience')); ?>
<?php endif; ?>

<?php $__env->startSection('crumb'); ?>
    <?php if (isset($component)) { $__componentOriginalc2072a121b282e859e8bdea9c58b76d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2072a121b282e859e8bdea9c58b76d8 = $attributes; } ?>
<?php $component = App\View\Components\BreadCrumb::resolve(['breadcrumbs' => [
        ['text' => \App\Helpers\TranslationHelper::translate('Experiences'), 'link' => route('admin.experience.index')],
        ['text' => $method === 'PUT' ? \App\Helpers\TranslationHelper::translate('Edit') : \App\Helpers\TranslationHelper::translate('Create')],
    ],'button' => ['text' => \App\Helpers\TranslationHelper::translate('Go to Experiences'), 'link' => route('admin.experience.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                    <div class="card-body pt-6">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger d-flex align-items-start p-4 mb-6 rounded-3">
                                <i class="fa-solid fa-triangle-exclamation fs-2hx text-danger me-3 mt-1"></i>
                                <div>
                                    <div class="fw-bold mb-2">
                                        <?php echo e(\App\Helpers\TranslationHelper::translate('Please check the submitted fields:')); ?>

                                    </div>
                                    <ul class="mb-0 ps-4">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php if($method === 'PUT'): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?>

                            <div class="row g-6 mb-8">
                                <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Company Name in')); ?> <?php echo e(__('methods.' . $lang)); ?> *
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-solid"
                                               name="company_name[<?php echo e($key); ?>]"
                                               value="<?php echo e(old('company_name.' . $key, $experience->getTranslation('company_name', $key, false))); ?>"
                                               required/>
                                        <?php $__errorArgs = ['company_name.' . $key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Role Title in')); ?> <?php echo e(__('methods.' . $lang)); ?> *
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-solid"
                                               name="role_title[<?php echo e($key); ?>]"
                                               value="<?php echo e(old('role_title.' . $key, $experience->getTranslation('role_title', $key, false))); ?>"
                                               required/>
                                        <?php $__errorArgs = ['role_title.' . $key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Location in')); ?> <?php echo e(__('methods.' . $lang)); ?>

                                        </label>
                                        <input type="text"
                                               class="form-control form-control-solid"
                                               name="location[<?php echo e($key); ?>]"
                                               value="<?php echo e(old('location.' . $key, $experience->getTranslation('location', $key, false))); ?>"/>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <div class="col-md-3">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('Employment Type')); ?> *</label>
                                    <?php ($currentEmploymentType = old('employment_type', $experience->employment_type?->value ?? \App\Enums\EmploymentTypeEnum::FullTime->value)); ?>
                                    <select class="form-select form-select-solid" name="employment_type" required>
                                        <?php $__currentLoopData = $employmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeValue => $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($typeValue); ?>" <?php if($currentEmploymentType === $typeValue): echo 'selected'; endif; ?>><?php echo e($typeLabel); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['employment_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-3">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('Start Date')); ?> *</label>
                                    <input type="date" class="form-control form-control-solid"
                                           name="start_date" id="start_date"
                                           value="<?php echo e(old('start_date', $experience->start_date?->format('Y-m-d'))); ?>"
                                           required/>
                                    <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-3" id="end_date_wrapper">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('End Date')); ?></label>
                                    <input type="date" class="form-control form-control-solid"
                                           name="end_date" id="end_date_input"
                                           value="<?php echo e(old('end_date', $experience->end_date?->format('Y-m-d'))); ?>"/>
                                    <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-3 d-flex flex-column justify-content-end">
                                    <label class="fs-6 fw-bold form-label mb-2 d-none d-md-block">&nbsp;</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                        <input type="hidden" name="is_current" value="0"/>
                                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current"
                                               <?php if((bool) old('is_current', $experience->is_current)): echo 'checked'; endif; ?>/>
                                        <label class="form-check-label fw-bold ms-3" for="is_current">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Current Role')); ?>

                                        </label>
                                    </div>
                                </div>

                                <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Summary in')); ?> <?php echo e(__('methods.' . $lang)); ?> *
                                        </label>
                                        <textarea class="form-control form-control-solid"
                                                  name="summary[<?php echo e($key); ?>]"
                                                  rows="4"
                                                  required><?php echo e(old('summary.' . $key, $experience->getTranslation('summary', $key, false))); ?></textarea>
                                        <?php $__errorArgs = ['summary.' . $key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php $__currentLoopData = Config('language'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-6">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fs-6 fw-bold form-label mb-0">
                                                <?php echo e(\App\Helpers\TranslationHelper::translate('Achievements in')); ?> <?php echo e(__('methods.' . $lang)); ?>

                                            </label>
                                            <button type="button" class="btn btn-sm btn-light-primary add-achievement-btn" data-lang="<?php echo e($key); ?>">
                                                <i class="ki-duotone ki-plus fs-3"></i>
                                                <?php echo e(\App\Helpers\TranslationHelper::translate('Add Achievement')); ?>

                                            </button>
                                        </div>

                                        <?php
                                            $oldVal = old('achievements.' . $key);
                                            if (is_array($oldVal)) {
                                                $achList = array_values(array_filter($oldVal, static fn ($v): bool => is_string($v) && trim($v) !== ''));
                                            } elseif (is_string($oldVal)) {
                                                $achList = array_values(array_filter(array_map('trim', preg_split('/\R/u', $oldVal))));
                                            } else {
                                                $raw = (string) $experience->getTranslation('achievements', $key, false);
                                                $achList = $raw !== '' ? array_values(array_filter(array_map('trim', preg_split('/\R/u', $raw)))) : [];
                                            }
                                            if (empty($achList)) {
                                                $achList = [''];
                                            }
                                        ?>

                                        <div class="achievements-container" id="achievements_container_<?php echo e($key); ?>">
                                            <?php $__currentLoopData = $achList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="achievement-row d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge badge-light-primary fw-bold px-3 py-2">+</span>
                                                    <input type="text"
                                                           name="achievements[<?php echo e($key); ?>][]"
                                                           class="form-control form-control-solid flex-grow-1"
                                                           placeholder="<?php echo e($key === 'ar' ? 'اكتب إنجازاً مميزاً هنا...' : 'Write an achievement here...'); ?>"
                                                           value="<?php echo e($item); ?>"/>
                                                    <button type="button" class="btn btn-icon btn-light-danger remove-achievement-btn" title="<?php echo e(\App\Helpers\TranslationHelper::translate('Delete')); ?>">
                                                        <i class="fas fa-trash-alt fs-5"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        <div class="form-text text-muted mt-2">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Add key achievements for this role. Each item will be displayed with a bullet.')); ?>

                                        </div>
                                        
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <div class="col-md-6">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('Technologies Used')); ?></label>
                                    <?php ($chosenTechs = (array) old('technologies', $selectedTechnologyIds)); ?>
                                    <select class="form-select form-select-solid" name="technologies[]" multiple data-control="select2">
                                        <?php $__currentLoopData = $technologies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($tech->id); ?>" <?php if(in_array($tech->id, $chosenTechs, true)): echo 'selected'; endif; ?>>
                                                <?php echo e($tech->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('Company Website URL')); ?></label>
                                    <input type="url" class="form-control form-control-solid" name="company_url"
                                           value="<?php echo e(old('company_url', $experience->company_url)); ?>" dir="ltr"/>
                                    <?php $__errorArgs = ['company_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="fs-6 fw-bold form-label mb-2"><?php echo e(\App\Helpers\TranslationHelper::translate('Company Logo')); ?></label>
                                    <input type="file" class="form-control form-control-solid" name="image" accept="image/*"/>
                                    <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger d-block mt-1"><strong><?php echo e($message); ?></strong></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="col-md-6 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="is_active" value="0"/>
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                               <?php if((bool) old('is_active', $experience->exists ? $experience->is_active : true)): echo 'checked'; endif; ?>/>
                                        <label class="form-check-label fw-bold ms-3" for="is_active">
                                            <?php echo e(\App\Helpers\TranslationHelper::translate('Active')); ?>

                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-8">
                                    <?php echo e(\App\Helpers\TranslationHelper::translate('Save')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('admin_js'); ?>
    <script>
        $(document).on('click', '.add-achievement-btn', function () {
            const lang = $(this).data('lang');
            const container = $('#achievements_container_' + lang);
            const placeholder = lang === 'ar' ? 'اكتب إنجازاً مميزاً هنا...' : 'Write an achievement here...';
            const deleteTitle = '<?php echo e(\App\Helpers\TranslationHelper::translate("Delete")); ?>';

            const newRow = $(`
                <div class="achievement-row d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-light-primary fw-bold px-3 py-2">+</span>
                    <input type="text"
                           name="achievements[${lang}][]"
                           class="form-control form-control-solid flex-grow-1"
                           placeholder="${placeholder}"
                           value=""/>
                    <button type="button" class="btn btn-icon btn-light-danger remove-achievement-btn" title="${deleteTitle}">
                        <i class="fas fa-trash-alt fs-5"></i>
                    </button>
                </div>
            `);
            container.append(newRow);
            newRow.find('input').focus();
        });

        $(document).on('click', '.remove-achievement-btn', function () {
            const container = $(this).closest('.achievements-container');
            if (container.find('.achievement-row').length > 1) {
                $(this).closest('.achievement-row').remove();
            } else {
                $(this).closest('.achievement-row').find('input').val('');
            }
        });

        function syncDateFields() {
            const isCurrent = $('#is_current').is(':checked');
            const endDateInput = $('#end_date_input');
            const endDateWrapper = $('#end_date_wrapper');

            if (isCurrent) {
                endDateInput.val('').prop('readonly', true).addClass('bg-light');
                endDateWrapper.addClass('opacity-50');
            } else {
                endDateInput.prop('readonly', false).removeClass('bg-light');
                endDateWrapper.removeClass('opacity-50');
            }
        }

        $(document).on('change', '#is_current', function () {
            syncDateFields();
        });

        $(document).on('change input', '#end_date_input', function () {
            if ($(this).val()) {
                $('#is_current').prop('checked', false);
                syncDateFields();
            }
        });

        $(document).ready(function () {
            syncDateFields();
        });
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\eng-abdulrahman-portfolio-back-end\resources\views/admin/pages/experience/form.blade.php ENDPATH**/ ?>