@extends('backend.layouts.app')
@section('title', __('labels.backend.orders.title').' | '.app_name())

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="{{asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.css')}}">
    <style>
        .select2-container--default .select2-selection--single {
            height: 35px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 35px;
        }

        .bootstrap-tagsinput {
            width: 100% !important;
            display: inline-block;
        }

        .bootstrap-tagsinput .tag {
            line-height: 1;
            margin-right: 2px;
            background-color: #2f353a;
            color: white;
            padding: 3px;
            border-radius: 3px;
        }
        /*.custom-select .select2-container--default .select2-selection--multiple {*/
        /*    min-height: 10em; !* Thay đổi giá trị này để điều chỉnh chiều cao *!*/
        /*}*/

    </style>

@endpush

@section('content')

    {!! Form::open(['method' => 'POST', 'route' => ['admin.orders.store'], 'files' => true,]) !!}
    {!! Form::hidden('model_id',0,['id'=>'lesson_id']) !!}

    <div class="card">
        <div class="card-header">
            <h3 class="page-title float-left mb-0">@lang('labels.backend.orders.create')</h3>
            <div class="float-right">
                <a href="{{ route('admin.orders.index') }}"
                   class="btn btn-success">@lang('labels.backend.orders.view')</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12 form-group">
                    {!! Form::label('user_id', trans('labels.backend.orders.fields.user'), ['class' => 'control-label']) !!}
                    {!! Form::select('user_id', [], (request('user_id')) ? request('user_id') : old('user_id'), ['class' => 'form-control user-search']) !!}
{{--                    {!! Form::select('user_id', $users,  (request('user_id')) ? request('user_id') : old('user_id'), ['class' => 'form-control select2']) !!}--}}
                </div>

                <div class="col-12 col-lg-12 form-group">
                    {!! Form::label('course_id', trans('labels.backend.orders.fields.course'), ['class' => 'control-label']) !!}
                    {!! Form::select('course_id[]', $courses, old('course_id'), ['class' => 'form-control select2 js-example-placeholder-multiple', 'multiple' => 'multiple', 'required' => true]) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-12  text-center form-group">

                    {!! Form::submit(trans('strings.backend.general.app_save'), ['class' => 'btn btn-lg btn-danger']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}



@stop

@push('after-scripts')
    <!-- Select2 -->
    <script>
        $(document).ready(function () {
            function formatRepo (repo) {
                if (repo.loading) {
                    return repo.text;
                }
                return repo.text;
            }

            function formatRepoSelection (repo) {
                return repo.text || repo.username;
            }

            $('.user-search').select2({
                ajax: {
                    url: '{{asset('user/auth/user/search')}}', // thay bằng URL đúng của route tìm kiếm người dùng
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '', // tìm kiếm theo từ khóa
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                placeholder: 'Tìm kiếm người dùng',
            }).on('select2:open', function (e) {
                if (!$(this).data('preloaded')) {
                    $(this).data('preloaded', true);
                    $('.user-search').select2('search', '');
                }
            });
        });
    </script>
@endpush
