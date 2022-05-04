@extends('HeThong.main')

@section('custom-style')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/pages/dataTables.bootstrap.css') }}" />
    {{-- <link rel="stylesheet" type="text/css" href="{{ url('assets/css/pages/select2.css') }}" /> --}}
@stop

@section('custom-script-footer')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="/assets/js/pages/select2.js"></script>
    <script src="/assets/js/pages/jquery.dataTables.min.js"></script>
    <script src="/assets/js/pages/dataTables.bootstrap.js"></script>
    <script src="/assets/js/pages/table-lifesc.js"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script>
        jQuery(document).ready(function() {
            TableManaged3.init();
            $('#madonvi').change(function() {
                window.location.href = '/HoSoThiDua/ThongTin?madonvi=' + $('#madonvi').val() +
                    '&nam=' + $('#nam').val();
            });
            $('#nam').change(function() {
                window.location.href = '/HoSoThiDua/ThongTin?madonvi=' + $('#madonvi').val() +
                    '&nam=' + $('#nam').val();
            });
        });
    </script>
@stop

@section('content')
    <!--begin::Card-->
    <div class="card card-custom wave wave-animate-slow wave-info" style="min-height: 600px">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label text-uppercase">Danh sách hồ sơ đăng ký thi đua</h3>
            </div>
            <div class="card-toolbar">

            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-md-6">
                    <label style="font-weight: bold">Đơn vị</label>
                    <select class="form-control select2basic" id="madonvi">
                        @foreach ($m_diaban as $diaban)
                            <optgroup label="{{ $diaban->tendiaban }}">
                                <?php $donvi = $m_donvi->where('madiaban', $diaban->madiaban); ?>
                                @foreach ($donvi as $ct)
                                    <option {{ $ct->madonvi == $inputs['madonvi'] ? 'selected' : '' }}
                                        value="{{ $ct->madonvi }}">{{ $ct->tendonvi }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-weight: bold">Năm</label>
                    {!! Form::select('nam', getNam(true), $inputs['nam'], ['id' => 'nam', 'class' => 'form-control select2basic']) !!}
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12">

                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                        <thead>
                            <tr class="text-center">
                                <th rowspan="2" width="2%">STT</th>
                                <th rowspan="2">Đơn vị phát động</th>
                                <th rowspan="2">Nội dung hồ sơ</th>
                                <th colspan="4">Thông tin phong trào</th>
                                <th rowspan="2">Hồ sơ của đơn vị</th>
                                <th rowspan="2" width="10%">Thao tác</th>
                            </tr>
                            <tr class="text-center">
                                <th>Phạm vi phát động</th>
                                <th width="10%">Thời gian</th>
                                <th width="8%">Trạng thái</th>
                                <th width="8%">Số hồ sơ<br>đã nhận</th>
                            </tr>
                        </thead>
                        @foreach ($model as $key => $tt)
                            <tr class="{{ $tt->nhanhoso == 'DANGNHAN' ? 'text-success' : '' }}">
                                <td style="text-align: center">{{ $key + 1 }}</td>
                                <td>{{ $tt->tendonvi }}</td>
                                <td>{{ $tt->noidung }}</td>
                                <td class="text-center">{{ $a_phamvi[$tt->phamviapdung] }}</td>
                                <td class="text-center">Từ {{ getDayVn($tt->tungay) }}</br> đến
                                    {{ getDayVn($tt->denngay) }}</td>
                                <td class="text-center">{{ $a_trangthaihoso[$tt->nhanhoso] }}</td>
                                <td class="text-center">{{ chkDbl($tt->sohoso) }}</td>
                                @include('includes.td.td_trangthai_hoso')

                                <td style="text-align: center">
                                    <a title="Thông tin phong trào"
                                        href="{{ url('/PhongTraoThiDua/Xem?maphongtraotd=' . $tt->maphongtraotd . '&trangthai=false') }}"
                                        class="btn btn-sm btn-clean btn-icon" target="_blank">
                                        <i class="icon-lg la fa-eye text-dark"></i></a>
                                    @if ($tt->nhanhoso == 'DANGNHAN')
                                        @if (in_array($tt->trangthai, ['CC', 'BTL']))
                                            <a title="Xem hồ sơ đăng ký phong trào"
                                                href="{{ url('/HoSoThiDua/Sua?mahosotdkt=' . $tt->mahosotdkt) }}"
                                                class="btn btn-sm btn-clean btn-icon">
                                                <i class="icon-lg la fa-check-square text-primary"></i></a>
                                        @elseif (in_array($tt->trangthai, ['CXD']))
                                            <a title="Xem hồ sơ đăng ký phong trào"
                                                href="{{ url('/HoSoThiDua/Them?maphongtraotd=' . $tt->maphongtraotd . '&madonvi=' . $inputs['madonvi']) }}"
                                                class="btn btn-sm btn-clean btn-icon">
                                                <i class="icon-lg la fa-check-square text-primary"></i></a>
                                        @else
                                            <a title="Xem hồ sơ đăng ký phong trào"
                                                href="{{ url('/HoSoThiDua/Xem?mahosotdkt=' . $tt->mahosotdkt) }}"
                                                class="btn btn-sm btn-clean btn-icon">
                                                <i class="icon-lg la fa-check-square text-dark"></i></a>
                                        @endif

                                        @if ($tt->hosodonvi > 0 && in_array($tt->trangthai, ['CC', 'BTL']))
                                            <button title="Trình hồ sơ đăng ký" type="button"
                                                onclick="confirmChuyen('{{ $tt->mahosotdkt }}','/HoSoThiDua/ChuyenHoSo')"
                                                class="btn btn-sm btn-clean btn-icon" data-target="#chuyen-modal-confirm"
                                                data-toggle="modal">
                                                <i class="icon-lg la fa-share-square text-dark"></i></button>
                                        @endif

                                        @if (in_array($tt->trangthai, ['CC', 'BTL']))
                                            <button type="button"
                                                onclick="confirmDelete('{{ $tt->id }}','/HoSoThiDua/Xoa')"
                                                class="btn btn-sm btn-clean btn-icon" data-target="#delete-modal"
                                                data-toggle="modal">
                                                <i class="icon-lg la fa-trash text-danger"></i></button>
                                        @endif
                                    @else
                                        @if ($tt->trangthai != 'CXD')
                                            <a title="Xem hồ sơ đăng ký phong trào"
                                                href="{{ url('/HoSoThiDua/Xem?mahosotdkt=' . $tt->mahosotdkt) }}"
                                                class="btn btn-sm btn-clean btn-icon">
                                                <i class="icon-lg la fa-check-square text-dark"></i></a>
                                        @endif
                                        @if ($tt->mahosokt != null)
                                            <a title="Thông tin hồ sơ khen thưởng"
                                                href="{{ url('/KhenThuongHoSoThiDua/Xem?mahosokt=' . $tt->mahosokt) }}"
                                                class="btn btn-sm btn-clean btn-icon" target="_blank">
                                                <i class="icon-lg la fa-user-check text-dark"></i></a>
                                        @endif
                                    @endif

                                    @if ($tt->trangthai == 'BTL')
                                        <button title="Lý do hồ sơ bị trả lại" type="button"
                                            onclick="viewLiDo('{{ $tt->mahosotdkt }}','{{ $inputs['madonvi'] }}')"
                                            class="btn btn-sm btn-clean btn-icon" data-target="#lydo-show"
                                            data-toggle="modal">
                                            <i class="icon-lg la fa-archive text-info"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Card-->
    <!--Modal Nhận hồ sơ-->
    <div id="taohoso-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade kt_select2_modal">
        {!! Form::open(['url' => '/HoSoThiDua/Them', 'id' => 'frm_hoso']) !!}
        <input type="hidden" name="madonvi" />
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-primary">
                    <h4 id="modal-header-primary-label" class="modal-title">Đồng ý tạo hồ sơ đăng ký?</h4>
                    <button type="button" data-dismiss="modal" aria-hidden="true" class="close">&times;</button>

                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Tên đơn vị quyết định khen thưởng</label>
                            {!! Form::text('donvikhenthuong', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Cấp độ khen thưởng</label>
                            {!! Form::select('capkhenthuong', getPhamViApDung(), 'T', ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Ngày ra quyết định</label>
                            {!! Form::input('date', 'ngayhoso', date('Y-m-d'), ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>Nội dung khen thưởng</label>
                            {!! Form::textarea('noidung', null, ['class' => 'form-control', 'rows' => '3']) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>Chức vụ người ký</label>
                            {!! Form::text('chucvunguoiky', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-lg-6">
                            <label>Họ tên người ký</label>
                            {!! Form::text('hotennguoiky', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Hủy thao tác</button>
                    <button type="submit" data-dismiss="modal" class="btn btn-primary" onclick="clickKhenThuong()">Đồng
                        ý</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    @include('includes.modal.modal-delete')
    @include('includes.modal.modal_approve_hs')
@stop
