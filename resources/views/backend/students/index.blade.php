@extends('backend.layouts.app')
@section('title', __('labels.backend.students.title').' | '.app_name())
@push('after-styles')
    <link rel="stylesheet" href="{{asset('assets/css/colors/switch.css')}}">
@endpush
@section('content')

    <div class="card">
        <div class="card-header">
                <h3 class="page-title d-inline">@lang('labels.backend.students.title')</h3>
            @can('course_create')
                <div class="float-right">
                    <a href="{{ route('admin.students.create') }}"
                       class="btn btn-success">@lang('strings.backend.general.app_add_new')</a>

                </div>
            @endcan
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <div class="d-block">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="{{ route('admin.teachers.index') }}"
                                       style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">{{trans('labels.general.all')}}</a>
                                </li>
                                |
                                <li class="list-inline-item">
                                    <a href="{{ route('admin.teachers.index') }}?show_deleted=1"
                                       style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">{{trans('labels.general.trash')}}</a>
                                </li>
                            </ul>
                        </div>


                        <table id="myTable"
                               class="table table-bordered table-striped @if(auth()->user()->isAdmin() || $logged_in_user->hasRole('manager')) @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                            <thead>
                            <tr>

                                @can('teachers_delete')
                                    @if ( request('show_deleted') != 1 )
                                        <th style="text-align:center;width: 5%;"><input type="checkbox" class="mass"
                                                                              id="select-all"/>
                                        </th>
                                    @endif
                                @endcan

                                <th style="width: 5%;">#</th>
                                <th>ID</th>
                                <th style="width: 25%;">@lang('labels.backend.teachers.fields.first_name')</th>
                                <th style="width: 20%;">@lang('labels.backend.teachers.fields.last_name')</th>
                                <th style="width: 25%;">@lang('labels.backend.teachers.fields.email')</th>
                                <th style="width: 5%;">@lang('labels.backend.teachers.fields.status')</th>
                                @if( request('show_deleted') == 1 )
                                    <th style="width: 15%;">&nbsp; @lang('strings.backend.general.actions')</th>
                                @else
                                    <th style="width: 15%;">&nbsp; @lang('strings.backend.general.actions')</th>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-scripts')
    <script>

        $(document).ready(function () {



            var route = '{{route('admin.students.get_data')}}';

            @if(request('show_deleted') == 1)
                route = '{{route('admin.teachers.get_data',['show_deleted' => 1])}}';
            @endif

           var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                retrieve: true,
                dom: 'lfrtip<"actions">',
                ajax: route,
                columns: [
                        @if(request('show_deleted') != 1)
                    {
                        "data": function (data) {
                            return '<input type="checkbox" class="single" name="id[]" value="' + data.id + '" />';
                        }, "orderable": false, "searchable": false, "name": "id"
                    },
                        @endif
                    {data: "DT_RowIndex", name: 'DT_RowIndex', searchable: false, orderable:false},
                    // {data: "id", name: 'id'},
                    {data: "first_name", name: 'first_name'},
                    {data: "last_name", name: 'last_name'},
                    {data: "email", name: 'email'},
                    {data: "status", name: 'status'},
                    {data: "actions", name: 'actions'}
                ],
                @if(request('show_deleted') != 1)
                columnDefs: [
                    {"width": "5%", "targets": 0},
                    {"className": "text-center", "targets": [0]}
                ],
                @endif

                createdRow: function (row, data, dataIndex) {
                    $(row).attr('data-entry-id', data.id);
                },
            });
            @if(auth()->user()->isAdmin() || $logged_in_user->hasRole('manager'))
            $('.actions').html('<a href="' + '{{ route('admin.students.mass_destroy') }}' + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">Delete selected</a>');
            @endif



        });
        $(document).on('click', '.switch-input', function (e) {
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.students.status') }}",
                data: {
                    _token:'{{ csrf_token() }}',
                    id: id,
                },
            }).done(function() {
                var table = $('#myTable').DataTable();
                table.ajax.reload();
            });
        })

    </script>

@endpush