@if (in_array($tt->trangthai_hoso, ['DD', 'CD', 'CXKT']))
    <button title="Trả lại hồ sơ" type="button"
        onclick="confirmTraLai('{{ $tt->mahosotdkt }}', '{{ $inputs['madonvi'] }}', '{{ $inputs['url_xd'] . 'TraLai' }}')"
        class="btn btn-sm btn-clean btn-icon" data-target="#modal-tralai" data-toggle="modal">
        <i class="icon-lg la la-reply text-danger"></i>
    </button>
@endif

@if (in_array($tt->trangthai, ['CXKT']))
    <a title="Tạo dự thảo tờ trình" href="{{ url($inputs['url_xd'] . 'ToTrinhPheDuyet?mahosotdkt=' . $tt->mahosotdkt) }}"
        class="btn btn-sm btn-clean btn-icon {{ $tt->soluongkhenthuong == 0 ? 'disabled' : '' }}">
        <i class="icon-lg la flaticon-edit-1 text-success"></i>
    </a>

    <a title="Tạo dự thảo quyết định khen thưởng"
        href="{{ url($inputs['url_xd'] . 'QuyetDinh?mahosotdkt=' . $tt->mahosotdkt) }}"
        class="btn btn-sm btn-clean btn-icon {{ $tt->soluongkhenthuong == 0 ? 'disabled' : '' }}">
        <i class="icon-lg la flaticon-edit-1 text-success"></i>
    </a>

    <a title="Trình kết quả khen thưởng"
        href="{{ url($inputs['url_xd'] . 'TrinhKetQua?mahosotdkt=' . $tt->mahosotdkt) }}"
        class="btn btn-sm btn-clean btn-icon">
        <i class="icon-lg la flaticon-internet text-success"></i>
    </a>
@endif