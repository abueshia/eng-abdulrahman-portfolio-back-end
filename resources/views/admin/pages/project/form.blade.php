@extends('admin.layouts.app')

@if ($method === 'PUT')
    @section('title', \App\Helpers\TranslationHelper::translate('Edit Project'))
@else
    @section('title', \App\Helpers\TranslationHelper::translate('Create Project'))
@endif

@section('crumb')
    <x-bread-crumb :breadcrumbs="[
        ['text' => \App\Helpers\TranslationHelper::translate('Projects'), 'link' => route('admin.project.index')],
        ['text' => $method === 'PUT' ? \App\Helpers\TranslationHelper::translate('Edit') : \App\Helpers\TranslationHelper::translate('Create')],
    ]" :button="['text' => \App\Helpers\TranslationHelper::translate('Go to Projects'), 'link' => route('admin.project.index')]">
    </x-bread-crumb>
@endsection

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card">
                    <div class="card-body pt-6">
                        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if ($method === 'PUT')
                                @method('PUT')
                            @endif

                            <div class="row g-6 mb-8">
                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Title in') }} {{ __('methods.' . $lang) }} *
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-solid"
                                               name="title[{{ $key }}]"
                                               value="{{ old('title.' . $key, $project->getTranslation('title', $key, false)) }}"
                                               required/>
                                        @error('title.' . $key)
                                            <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Subtitle in') }} {{ __('methods.' . $lang) }}
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-solid"
                                               name="subtitle[{{ $key }}]"
                                               value="{{ old('subtitle.' . $key, $project->getTranslation('subtitle', $key, false)) }}"/>
                                        @error('subtitle.' . $key)
                                            <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Category') }}</label>
                                    <select class="form-select form-select-solid" name="category_id">
                                        <option value="">{{ \App\Helpers\TranslationHelper::translate('Choose Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected((int) old('category_id', $project->category_id) === $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Project Type') }} *</label>
                                    @php($currentType = old('project_type', $project->project_type?->value ?? \App\Enums\ProjectTypeEnum::WebApp->value))
                                    <select class="form-select form-select-solid" name="project_type" required>
                                        @foreach ($projectTypes as $typeValue => $typeLabel)
                                            <option value="{{ $typeValue }}" @selected($currentType === $typeValue)>{{ $typeLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('project_type')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Status') }} *</label>
                                    @php($currentStatus = old('status', $project->status?->value ?? \App\Enums\ProjectStatusEnum::Published->value))
                                    <select class="form-select form-select-solid" name="status" required>
                                        @foreach ($projectStatuses as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}" @selected($currentStatus === $statusValue)>{{ $statusLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Short Description in') }} {{ __('methods.' . $lang) }} *
                                        </label>
                                        <textarea class="form-control form-control-solid"
                                                  name="short_description[{{ $key }}]"
                                                  rows="3"
                                                  required>{{ old('short_description.' . $key, $project->getTranslation('short_description', $key, false)) }}</textarea>
                                        @error('short_description.' . $key)
                                            <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Description in') }} {{ __('methods.' . $lang) }} *
                                        </label>
                                        <textarea class="form-control form-control-solid"
                                                  name="description[{{ $key }}]"
                                                  rows="5"
                                                  required>{{ old('description.' . $key, $project->getTranslation('description', $key, false)) }}</textarea>
                                        @error('description.' . $key)
                                            <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6 mb-6">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fs-6 fw-bold form-label mb-0">
                                                {{ \App\Helpers\TranslationHelper::translate('Challenges in') }} {{ __('methods.' . $lang) }}
                                            </label>
                                            <button type="button" class="btn btn-sm btn-light-primary add-challenge-btn" data-lang="{{ $key }}">
                                                <i class="ki-duotone ki-plus fs-3"></i>
                                                {{ \App\Helpers\TranslationHelper::translate('Add Challenge') }}
                                            </button>
                                        </div>

                                        <?php
                                            $oldChallenges = old('challenge.' . $key);
                                            if (is_array($oldChallenges)) {
                                                $challengeItems = [];
                                                foreach ($oldChallenges as $item) {
                                                    if (is_array($item)) {
                                                        $t = (string) ($item['title'] ?? '');
                                                        $d = (string) ($item['description'] ?? '');
                                                        if (trim($t) !== '' || trim($d) !== '') {
                                                            $challengeItems[] = ['title' => $t, 'description' => $d];
                                                        }
                                                    }
                                                }
                                            } else {
                                                $challengeItems = $project->exists ? $project->getFormattedChallenges($key) : [];
                                            }
                                            if (empty($challengeItems)) {
                                                $challengeItems = [['title' => '', 'description' => '']];
                                            }
                                        ?>

                                        <div class="challenges-container" id="challenges_container_{{ $key }}">
                                            @foreach ($challengeItems as $index => $item)
                                                <div class="challenge-card card card-bordered border-dashed p-4 mb-3 bg-light-subtle">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge badge-light-primary fw-bold">
                                                            {{ \App\Helpers\TranslationHelper::translate('Challenge') }} #<span class="challenge-index">{{ $index + 1 }}</span>
                                                        </span>
                                                        <button type="button" class="btn btn-icon btn-sm btn-light-danger remove-challenge-btn" title="{{ \App\Helpers\TranslationHelper::translate('Delete') }}">
                                                            <i class="fas fa-trash-alt fs-6"></i>
                                                        </button>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fs-7 fw-bold mb-1">{{ \App\Helpers\TranslationHelper::translate('Challenge Title') }}</label>
                                                        <input type="text"
                                                               name="challenge[{{ $key }}][{{ $index }}][title]"
                                                               class="form-control form-control-solid"
                                                               placeholder="{{ $key === 'ar' ? 'عنوان التحدي (مثال: عزل البيانات بدون تكرار الكود)...' : 'Challenge title (e.g. Data isolation without code duplication)...' }}"
                                                               value="{{ $item['title'] ?? '' }}"/>
                                                    </div>
                                                    <div>
                                                        <label class="form-label fs-7 fw-bold mb-1">{{ \App\Helpers\TranslationHelper::translate('Challenge Description') }}</label>
                                                        <textarea name="challenge[{{ $key }}][{{ $index }}][description]"
                                                                  class="form-control form-control-solid"
                                                                  rows="2"
                                                                  placeholder="{{ $key === 'ar' ? 'تفاصيل التحدي والمشكلة الهندسية...' : 'Challenge description and technical problem...' }}">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="form-text text-muted mt-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Add key technical challenges and architectural problems faced in this project.') }}
                                        </div>
                                    </div>
                                @endforeach

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Solution in') }} {{ __('methods.' . $lang) }}
                                        </label>
                                        <textarea class="form-control form-control-solid"
                                                  name="solution[{{ $key }}]"
                                                  rows="3">{{ old('solution.' . $key, $project->getTranslation('solution', $key, false)) }}</textarea>
                                    </div>
                                @endforeach

                                @foreach (Config('language') as $key => $lang)
                                    <div class="col-md-6">
                                        <label class="fs-6 fw-bold form-label mb-2">
                                            {{ \App\Helpers\TranslationHelper::translate('Results in') }} {{ __('methods.' . $lang) }}
                                        </label>
                                        <textarea class="form-control form-control-solid"
                                                  name="results[{{ $key }}]"
                                                  rows="3">{{ old('results.' . $key, $project->getTranslation('results', $key, false)) }}</textarea>
                                    </div>
                                @endforeach

                                <div class="col-md-12">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Technologies Used') }}</label>
                                    @php($chosenTechs = (array) old('technologies', $selectedTechnologyIds))
                                    <select class="form-select form-select-solid" name="technologies[]" multiple data-control="select2">
                                        @foreach ($technologies as $tech)
                                            <option value="{{ $tech->id }}" @selected(in_array($tech->id, $chosenTechs, true))>
                                                {{ $tech->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Live Preview URL') }}</label>
                                    <input type="url" class="form-control form-control-solid" name="live_url" value="{{ old('live_url', $project->live_url) }}" dir="ltr"/>
                                    @error('live_url')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Repository URL') }}</label>
                                    <input type="url" class="form-control form-control-solid" name="repo_url" value="{{ old('repo_url', $project->repo_url) }}" dir="ltr"/>
                                    @error('repo_url')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Completed At') }}</label>
                                    <input type="date" class="form-control form-control-solid" name="completed_at" value="{{ old('completed_at', $project->completed_at?->format('Y-m-d')) }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Cover Image') }}</label>
                                    <input type="file" class="form-control form-control-solid" name="image" accept="image/*"/>
                                    @error('image')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    @if ($project->exists && $project->getFirstMediaUrl('projects'))
                                        <div class="mt-3">
                                            <a href="{{ $project->getFirstMediaUrl('projects') }}" target="_blank">
                                                <img src="{{ $project->getFirstMediaUrl('projects') }}" alt="cover" class="rounded border shadow-sm" width="130" style="height: 80px; object-fit: cover;"/>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label class="fs-6 fw-bold form-label mb-2">{{ \App\Helpers\TranslationHelper::translate('Gallery Screenshots') }}</label>
                                    <input type="file" class="form-control form-control-solid" name="gallery[]" multiple accept="image/*"/>
                                    @error('gallery.*')
                                        <span class="text-danger d-block mt-1"><strong>{{ $message }}</strong></span>
                                    @enderror

                                    @if ($project->exists && $project->getMedia('projects_gallery')->isNotEmpty())
                                        <div class="mt-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-7 fw-bold text-gray-700">
                                                    {{ \App\Helpers\TranslationHelper::translate('Current Gallery Screenshots') }} ({{ $project->getMedia('projects_gallery')->count() }}):
                                                </span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach ($project->getMedia('projects_gallery') as $galleryMedia)
                                                    <div class="position-relative border rounded p-1 shadow-sm bg-white gallery-item-{{ $galleryMedia->id }}" style="width: 100px;">
                                                        <a href="{{ $galleryMedia->getUrl() }}" target="_blank" title="{{ $galleryMedia->file_name }}">
                                                            <img src="{{ $galleryMedia->getUrl() }}" alt="gallery" class="rounded" style="width: 100%; height: 65px; object-fit: cover;"/>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-icon btn-danger btn-circle btn-sm position-absolute top-0 end-0 translate-middle delete-gallery-btn" 
                                                                data-url="{{ route('admin.project.media.destroy', ['project' => $project->id, 'media' => $galleryMedia->id]) }}"
                                                                data-target=".gallery-item-{{ $galleryMedia->id }}"
                                                                style="width: 22px; height: 22px; font-size: 10px;"
                                                                title="{{ \App\Helpers\TranslationHelper::translate('Delete') }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="is_featured" value="0"/>
                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                                               @checked((bool) old('is_featured', $project->is_featured))/>
                                        <label class="form-check-label fw-bold ms-3" for="is_featured">
                                            {{ \App\Helpers\TranslationHelper::translate('Featured Project') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="is_active" value="0"/>
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                               @checked((bool) old('is_active', $project->exists ? $project->is_active : true))/>
                                        <label class="form-check-label fw-bold ms-3" for="is_active">
                                            {{ \App\Helpers\TranslationHelper::translate('Active') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-8">
                                    {{ \App\Helpers\TranslationHelper::translate('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('admin_js')
<script>
    $(document).on('click', '.add-challenge-btn', function () {
        const lang = $(this).data('lang');
        const container = $('#challenges_container_' + lang);
        const nextIndex = container.find('.challenge-card').length;
        const placeholderTitle = lang === 'ar' ? 'عنوان التحدي (مثال: عزل البيانات بدون تكرار الكود)...' : 'Challenge title (e.g. Data isolation without code duplication)...';
        const placeholderDesc = lang === 'ar' ? 'تفاصيل التحدي والمشكلة الهندسية...' : 'Challenge description and technical problem...';
        const deleteTitle = "{{ \App\Helpers\TranslationHelper::translate('Delete') }}";
        const challengeBadge = "{{ \App\Helpers\TranslationHelper::translate('Challenge') }}";
        const titleLabel = "{{ \App\Helpers\TranslationHelper::translate('Challenge Title') }}";
        const descLabel = "{{ \App\Helpers\TranslationHelper::translate('Challenge Description') }}";

        const newCard = $(`
            <div class="challenge-card card card-bordered border-dashed p-4 mb-3 bg-light-subtle">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge badge-light-primary fw-bold">
                        ${challengeBadge} #<span class="challenge-index">${nextIndex + 1}</span>
                    </span>
                    <button type="button" class="btn btn-icon btn-sm btn-light-danger remove-challenge-btn" title="${deleteTitle}">
                        <i class="fas fa-trash-alt fs-6"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <label class="form-label fs-7 fw-bold mb-1">${titleLabel}</label>
                    <input type="text"
                           name="challenge[${lang}][${nextIndex}][title]"
                           class="form-control form-control-solid"
                           placeholder="${placeholderTitle}"
                           value=""/>
                </div>
                <div>
                    <label class="form-label fs-7 fw-bold mb-1">${descLabel}</label>
                    <textarea name="challenge[${lang}][${nextIndex}][description]"
                              class="form-control form-control-solid"
                              rows="2"
                              placeholder="${placeholderDesc}"></textarea>
                </div>
            </div>
        `);
        container.append(newCard);
        newCard.find('input').focus();
    });

    $(document).on('click', '.remove-challenge-btn', function () {
        const container = $(this).closest('.challenges-container');
        const cards = container.find('.challenge-card');
        if (cards.length > 1) {
            $(this).closest('.challenge-card').remove();
            container.find('.challenge-card').each(function (idx) {
                $(this).find('.challenge-index').text(idx + 1);
            });
        } else {
            $(this).closest('.challenge-card').find('input').val('');
            $(this).closest('.challenge-card').find('textarea').val('');
        }
    });

    $(document).on('click', '.delete-gallery-btn', function (e) {
        e.preventDefault();
        const btn = $(this);
        const url = btn.data('url');
        const target = btn.data('target');

        Swal.fire({
            title: "{{ \App\Helpers\TranslationHelper::translate('Are you sure?') }}",
            text: "{{ \App\Helpers\TranslationHelper::translate('This gallery image will be deleted.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ \App\Helpers\TranslationHelper::translate('Yes, delete it!') }}",
            cancelButtonText: "{{ \App\Helpers\TranslationHelper::translate('Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $(target).fadeOut(300, function () {
                            $(this).remove();
                        });
                        Swal.fire({
                            title: "{{ \App\Helpers\TranslationHelper::translate('Deleted!') }}",
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        Swal.fire({
                            title: "{{ \App\Helpers\TranslationHelper::translate('Error!') }}",
                            text: "{{ \App\Helpers\TranslationHelper::translate('Failed to delete image.') }}",
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
</script>
@endpush

