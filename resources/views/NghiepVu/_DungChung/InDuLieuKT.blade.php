{{-- In dữ liệu --}}
<div id="indulieu-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade">
    {!! Form::open(['url' => '', 'id' => 'frm_InDuLieu']) !!}
    <input type="hidden" name="madonvi" value="{{ $inputs['madonvi'] }}" />
    <input type="hidden" name="mahosotdkt" />
    <input type="hidden" name="maphongtraotd" />
    <input type="hidden" name="mahosokt" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-primary">
                <h4 id="modal-header-primary-label" class="modal-title">Thông tin in dữ liệu</h4>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close">&times;</button>
            </div>
            <div class="modal-body">
                {{-- <div class="row">
                    <div class="col-lg-12">
                        <a onclick="setInPT($(this), '')"
                            class="btn btn-sm btn-clean text-dark font-weight-bold" target="_blank">
                            <i class="la flaticon2-print"></i>Thông tin phong trào thi đua
                        </a>
                    </div>
                </div> --}}

                <div class="row">
                    <div class="col-lg-12">
                        <a onclick="setInDL($(this), '{{ $inputs['url_hs'] . 'InHoSo' }}')"
                            class="btn btn-sm btn-clean text-dark font-weight-bold" target="_blank">
                            <i class="la flaticon2-print"></i>Thông tin hồ sơ khen thưởng
                        </a>
                    </div>
                </div>

                <div id="div_inDuLieu">
                    <div class="row">
                        <div class="col-lg-12">
                            <a onclick="setInDL($(this), '{{ $inputs['url_hs'] . 'InHoSoKT' }}')"
                                class="btn btn-sm btn-clean text-dark font-weight-bold" target="_blank">
                                <i class="la flaticon2-print"></i>Thông tin hồ sơ phê duyệt khen thưởng
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <a id="btnInQD" onclick="setInDL($(this), '{{ $inputs['url_hs'] . 'InQuyetDinh' }}')"
                                class="btn btn-sm btn-clean text-dark font-weight-bold" target="_blank">
                                <i class="la flaticon2-print"></i>Quyết định khen thưởng
                            </a>
                        </div>
                    </div>
                </div>

                <div id="div_inPhoi">
                    <div class="row">
                        <div class="col-lg-12">
                            <a onclick="setInDL($(this), '{{ $inputs['url_hs'] . 'InPhoi' }}')"
                                class="btn btn-sm btn-clean text-dark font-weight-bold" target="_blank">
                                <i class="la flaticon2-print"></i>In phôi bằng khen, giấy khen
                            </a>
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default">Đóng</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>


<script>
    function setInDuLieu(mahosotdkt, maphongtraotd, trangthai, inphoi = false) {
        $('#div_inDuLieu').hide();
        $('#div_inPhoi').hide();
        $('#frm_InDuLieu').find("[name='mahosotdkt']").val(mahosotdkt);
        $('#frm_InDuLieu').find("[name='maphongtraotd']").val(maphongtraotd);
        if (trangthai == 'DKT') {
            $('#div_inDuLieu').show();
            if (inphoi)
                $('#div_inPhoi').show();
        }
    }

    function setInDL(e, url) {
        e.prop('href', url + '?mahosotdkt=' + $('#frm_InDuLieu').find("[name='mahosotdkt']").val());
    }

    function setInPhoi(e, url) {
        e.prop('href', url + '?mahosotdkt=' + $('#frm_InDuLieu').find("[name='mahosotdkt']").val());
    }    
</script>