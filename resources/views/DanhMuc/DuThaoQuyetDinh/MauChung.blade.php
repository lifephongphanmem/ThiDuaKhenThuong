@extends('HeThong.main')

@section('custom-style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/pages/dataTables.bootstrap.css') }}" />
    {{-- <link rel="stylesheet" type="text/css" href="{{ url('assets/css/pages/select2.css') }}" /> --}}
@stop

@section('custom-script')

@stop

@section('custom-script-footer')
<script>
    
</script>
    <script src="/assets/js/pages/custom/ckeditor/ckeditor.js"></script>
    <script src="/assets/js/pages/custom/ckeditor/ckeditor-custom.js"></script>  
@stop

@section('content')
    <!--begin::Card-->

    <div class="card card-custom" style="min-height: 600px">
        <div class="card-header flex-wrap border-1 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label text-uppercase">Thông tin dự thảo quyết định khen thưởng</h3>
            </div>
            <div class="card-toolbar">                

            </div>
        </div>

        <div class="card-body">
            <div class="form-group row">
                <div class="col-12">
                    <label style="font-weight: bold">Tên dự thảo</label>
                    {!! Form::text('tenduthao',  $model->noidung, ['id' => 'tenduthao', 'class' => 'form-control muted']) !!}
                </div>
            </div>
            <hr>
            {!! Form::model($model, [
                'method' => 'POST',
                'url' => $inputs['url'] . 'Luu',
                'class' => 'form',
                'id' => 'frm_In',
                'files' => true,
                'enctype' => 'multipart/form-data',
            ]) !!}
            {{ Form::hidden('maduthao', null) }}
            {{ Form::hidden('codehtml', null) }}

            <div class="document-editor__toolbar"></div>
            <div class="form-control editor" style="height: auto; border: 1px solid #E4E6EF;">
                {!! html_entity_decode($model->codehtml) !!}
            </div>
            
            {{-- <div id="kt-ckeditor-1-toolbar"></div>
            <div id="kt-ckeditor-1">
                {!! html_entity_decode($model->thongtinquyetdinh) !!}
            </div> --}}
        </div>
        <div class="card-footer">
            <div class="row text-center">
                <div class="col-lg-12">
                    <a href="{{ url($inputs['url'] . 'ThongTin') }}"
                        class="btn btn-danger mr-5"><i class="fa fa-reply"></i>&nbsp;Quay lại</a>
                    <button type="submit" onclick="setGiaTri()" class="btn btn-primary"><i class="fa fa-check"></i>Hoàn
                        thành</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Card-->
    {!! Form::close() !!}
    <script>
        function setGiaTri() {
            $('#frm_In').find("[name='codehtml']").val(myEditor.getData());
        }
    </script>
@stop