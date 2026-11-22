@extends('admin.layouts.app')
@section('title', \App\Helpers\TranslationHelper::translate('Testimonials'))

@section('crumb')
    <x-bread-crumb :breadcrumbs="[
        ['text' => \App\Helpers\TranslationHelper::translate('Testimonials'), 'link' => route('admin.testimonial.index')],
        ['text' => \App\Helpers\TranslationHelper::translate('List')],
    ]" :button="[]">
    </x-bread-crumb>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @if (isset($pending_reviews) && $pending_reviews > 0)
                <div class="alert alert-dismissible bg-light-warning d-flex align-items-center p-5 mb-7 border border-warning">
                    <i class="ki-duotone ki-notification-bing fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-warning fw-bold">{{ \App\Helpers\TranslationHelper::translate('Pending Client Reviews') }}</h4>
                        <span class="text-gray-700">
                            {{ \App\Helpers\TranslationHelper::translate('There are') }} <strong>{{ $pending_reviews }}</strong> {{ \App\Helpers\TranslationHelper::translate('new client reviews submitted from the website awaiting your review and approval.') }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="card-toolbar w-100 mb-7">
                <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-3">
                    <div class="left-part d-flex gap-3">
                        @if (auth()->guard('admin')->user()?->can('testimonial.create', 'admin'))
                            <a href="{{ route('admin.testimonial.create') }}" class="btn btn-primary text-gray-800">
                                <i class="ki-duotone ki-plus fs-2 text-gray-800"></i>
                                {{ \App\Helpers\TranslationHelper::translate('Create Testimonial') }}
                            </a>
                        @endif

                        @if (auth()->guard('admin')->user()?->can('testimonial.sort', 'admin'))
                            <a href="{{ route('admin.testimonial.reorder') }}" class="btn btn-light-primary">
                                <i class="ki-solid ki-sort fs-2"></i>
                                {{ \App\Helpers\TranslationHelper::translate('Sort Items') }}
                            </a>
                        @endif
                    </div>

                    <div class="right-part d-flex align-items-center gap-3">
                        <select id="filter_source" class="form-select form-select-solid form-select-sm w-150px">
                            <option value="">{{ \App\Helpers\TranslationHelper::translate('All Sources') }}</option>
                            <option value="website">{{ \App\Helpers\TranslationHelper::translate('Website') }}</option>
                            <option value="dashboard">{{ \App\Helpers\TranslationHelper::translate('Dashboard') }}</option>
                        </select>

                        <select id="filter_status" class="form-select form-select-solid form-select-sm w-150px">
                            <option value="">{{ \App\Helpers\TranslationHelper::translate('All Status') }}</option>
                            <option value="pending">{{ \App\Helpers\TranslationHelper::translate('Pending Approval') }}</option>
                            <option value="active">{{ \App\Helpers\TranslationHelper::translate('Active') }}</option>
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
                            <th>{{ \App\Helpers\TranslationHelper::translate('Client Name') }}</th>
                            <th>{{ \App\Helpers\TranslationHelper::translate('Project') }}</th>
                            <th>{{ \App\Helpers\TranslationHelper::translate('Rating') }}</th>
                            <th>{{ \App\Helpers\TranslationHelper::translate('Source') }}</th>
                            <th>{{ \App\Helpers\TranslationHelper::translate('Status') }}</th>
                            <th>{{ \App\Helpers\TranslationHelper::translate('Actions') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.layouts.delete-modal', ['action_message' => \App\Helpers\TranslationHelper::translate('This Item')])
@endsection

@push('admin_js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        const table = $('#kt_table_testimonials').DataTable({
            serverSide: true,
            processing: true,
            fixedHeader: true,
            orderCellsTop: true,
            language: {
                @if (App::getLocale() === 'ar')
                url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json",
                @else
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json",
                @endif
                search: "",
                searchPlaceholder: "{{ App::getLocale() === 'ar' ? 'بحث...' : 'Search...' }}"
            },
            dom: `<'row align-items-center mb-5'<'col-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-12 col-md-6 d-flex align-items-center justify-content-end'B>>
                  <'row'<'col-sm-12'tr>>
                  <'row mt-5'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end dataTables_pager'lp>>`,
            buttons: ['print', 'copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[0, "asc"]],
            ajax: {
                url: '{{ route('admin.testimonial.table') }}',
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
    <script src="{{ asset('dashboard/assets/js/delete-item.js') }}" type="text/javascript"></script>
@endpush
