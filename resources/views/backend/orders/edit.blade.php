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

{{--    {!! Form::open(['method' => 'GET', 'route' => ['admin.orders.update', $order->id],]) !!}--}}
    {!! Form::model($order, ['method' => 'PUT', 'route' => ['admin.orders.update', $order->id]]) !!}

    {!! Form::hidden('model_id',0,['id'=>'lesson_id']) !!}

    <div class="card">
        <div class="card-header">
            <h3 class="page-title float-left mb-0">@lang('labels.backend.orders.edit')</h3>
            <div class="float-right">
                <a href="{{ route('admin.orders.index') }}"
                   class="btn btn-success">@lang('labels.backend.orders.view')</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
{{--                @dd($order->user)--}}
                <div class="col-12 col-lg-12 form-group" hidden>
                    {!! Form::label('user_id', trans('labels.backend.orders.fields.user'), ['class' => 'control-label']) !!}
                    {!! Form::select('user_id', $users, $order->user->id, ['class' => 'form-control select2']) !!}
{{--                    {!! Form::select('user_id', $users,  (request('user_id')) ? request('user_id') : old('user_id'), ['class' => 'form-control select2']) !!}--}}
                </div>

{{--                @dd($order->items,$courses)--}}
{{--                @dd($courses->contains($order))--}}
                <div class="col-12 col-lg-12 form-group">
                    {!! Form::label('course_id', trans('labels.backend.orders.fields.course'), ['class' => 'control-label']) !!}
                    {!! Form::select('course_id[]', $courses, old('course_id'), ['class' => 'form-control select2 js-example-placeholder-multiple', 'multiple' => 'multiple', 'required' => true]) !!}

{{--                    {!! Form::select('course_id[]', $courses,  $order->items()->get()->contains($courses), ['class' => 'form-control select2 js-example-placeholder-multiple', 'multiple' => 'multiple', 'required' => true]) !!}--}}
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
    <script>
        $(document).ready(function() {
            var selectedCourses = [];
            @foreach ($order->items as $item)
                @if ($item->item_type == 'App\Models\Course')
                    selectedCourses.push({{ $item->item_id }});
                @endif
            @endforeach

            $('.select2').val(selectedCourses).trigger('change');
        });
    </script>
@endpush
