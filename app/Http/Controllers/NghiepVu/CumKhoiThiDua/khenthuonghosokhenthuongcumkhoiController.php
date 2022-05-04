<?php

namespace App\Http\Controllers\NghiepVu\CumKhoiThiDua;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DanhMuc\dmdanhhieuthidua;
use App\Model\DanhMuc\dmdanhhieuthidua_tieuchuan;
use App\Model\DanhMuc\dmhinhthuckhenthuong;
use App\Model\DanhMuc\dmloaihinhkhenthuong;
use App\Model\DanhMuc\dscumkhoi;
use App\Model\DanhMuc\dsdiaban;
use App\Model\DanhMuc\dsdonvi;
use App\Model\NghiepVu\CumKhoiThiDua\dshosotdktcumkhoi;
use App\Model\NghiepVu\CumKhoiThiDua\dshosotdktcumkhoi_khenthuong;
use App\Model\NghiepVu\CumKhoiThiDua\dshosotdktcumkhoi_tieuchuan;
use App\Model\NghiepVu\ThiDuaKhenThuong\dshosokhenthuong;
use App\Model\NghiepVu\ThiDuaKhenThuong\dshosokhenthuong_chitiet;
use App\Model\NghiepVu\ThiDuaKhenThuong\dshosokhenthuong_khenthuong;
use App\Model\View\viewdiabandonvi;
use Illuminate\Support\Facades\Session;

class khenthuonghosokhenthuongcumkhoiController extends Controller
{
    public function ThongTin(Request $request)
    {
        if (Session::has('admin')) {
            if (!chkPhanQuyen()) {
                return view('errors.noperm');
            }
            $inputs = $request->all();
            $m_donvi = getDonViXetDuyetHoSoCumKhoi(session('admin')->capdo, null, null, 'MODEL');
            $m_diaban = dsdiaban::wherein('madiaban', array_column($m_donvi->toarray(), 'madiaban'))->get();
            $inputs['madonvi'] = $inputs['madonvi'] ?? $m_donvi->first()->madonvi;
            $inputs['nam'] = $inputs['nam'] ?? 'ALL';
            $m_cumkhoi = dscumkhoi::where('madonviql', $inputs['madonvi'])->get();
            $inputs['macumkhoi'] = $inputs['macumkhoi'] ?? $m_cumkhoi->first()->macumkhoi;
            //Trường hợp chọn lại đơn vị nhưng mã cụm khối vẫn theo đơn vị cũ
            $inputs['macumkhoi'] = $m_cumkhoi->where('macumkhoi', $inputs['macumkhoi'])->first() != null ? $inputs['macumkhoi'] : $m_cumkhoi->first()->macumkhoi;
            //$donvi = $m_donvi->where('madonvi', $inputs['madonvi'])->first();
            //$capdo = $donvi->capdo ?? '';
            //dd($inputs);
            $model = dshosotdktcumkhoi::where('macumkhoi', $inputs['macumkhoi'])
                ->wherein('mahosotdkt', function ($qr) use ($inputs) {
                    $qr->select('mahosotdkt')->from('dshosotdktcumkhoi')
                        ->where('madonvi_nhan', $inputs['madonvi'])
                        ->orwhere('madonvi_nhan_h', $inputs['madonvi'])
                        ->orwhere('madonvi_nhan_t', $inputs['madonvi'])->get();
                })->get();
            foreach ($model as $chitiet) {
                getDonViChuyen($inputs['madonvi'], $chitiet);

                //$chitiet->trangthai = $donvi->capdo == 'H' ? $chitiet->trangthai_h : $chitiet->trangthai_t;
            }
            //dd($model);
            return view('NghiepVu.CumKhoiThiDua.KhenThuongHoSoKhenThuong.ThongTin')
                ->with('inputs', $inputs)
                ->with('model', $model)
                ->with('m_donvi', $m_donvi)
                ->with('m_diaban', $m_diaban)
                ->with('m_cumkhoi', $m_cumkhoi)
                ->with('a_donvi', array_column(dsdonvi::all()->toArray(), 'tendonvi', 'madonvi'))
                ->with('a_loaihinhkt', array_column(dmloaihinhkhenthuong::all()->toArray(), 'tenloaihinhkt', 'maloaihinhkt'))
                //->with('a_trangthaihoso', getTrangThaiTDKT())
                //->with('a_phamvi', getPhamViPhongTrao())
                ->with('pageTitle', 'Danh sách hồ sơ thi đua');
        } else
            return view('errors.notlogin');
    }

    public function LuuKhenThuong(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $model = dshosokhenthuong::where('mahosokt', $inputs['mahosokt'])->first();
            if (isset($inputs['totrinh'])) {
                $filedk = $request->file('totrinh');
                $inputs['totrinh'] = $model->mahosokt . '_' . $filedk->getClientOriginalExtension();
                $filedk->move(public_path() . '/data/totrinh/', $inputs['totrinh']);
            }
            if (isset($inputs['qdkt'])) {
                $filedk = $request->file('qdkt');
                $inputs['qdkt'] = $model->mahosokt . '_' . $filedk->getClientOriginalExtension();
                $filedk->move(public_path() . '/data/qdkt/', $inputs['qdkt']);
            }
            if (isset($inputs['bienban'])) {
                $filedk = $request->file('bienban');
                $inputs['bienban'] = $model->mahosokt . '_' . $filedk->getClientOriginalExtension();
                $filedk->move(public_path() . '/data/bienban/', $inputs['bienban']);
            }
            if (isset($inputs['tailieukhac'])) {
                $filedk = $request->file('tailieukhac');
                $inputs['tailieukhac'] = $model->mahosokt . '_' . $filedk->getClientOriginalExtension();
                $filedk->move(public_path() . '/data/tailieukhac/', $inputs['tailieukhac']);
            }
            $model->update($inputs);
            return redirect('/CumKhoiThiDua/KhenThuongHoSoKhenThuong/ThongTin?madonvi='.$model->madonvi.'&macumkhoi='.$model->macumkhoi);
        } else
            return view('errors.notlogin');
    }

    public function KhenThuong(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $chk = dshosokhenthuong::where('mahosotdkt', $inputs['mahosotdkt'])
                ->where('madonvi', $inputs['madonvi'])->first();

            if ($chk == null) {
                //chưa hoàn thiện
                $m_hosokt = dshosotdktcumkhoi::where('mahosotdkt', $inputs['mahosotdkt'])->first();
                $inputs['mahosokt'] = (string)getdate()[0];
                $inputs['macumkhoi'] = $m_hosokt->macumkhoi;
                $khenthuong = new dshosokhenthuong_chitiet();
                $khenthuong->mahosokt = $inputs['mahosokt'];
                $khenthuong->mahosotdkt = $inputs['mahosotdkt'];
                $khenthuong->ketqua = 0;
                $khenthuong->madonvi = $inputs['madonvi'];
                $khenthuong->save();

                dshosokhenthuong::create($inputs);
                $model = dshosotdktcumkhoi::where('mahosotdkt', $inputs['mahosotdkt'])->first();
                $model->mahosokt = $inputs['mahosokt'];
                $thoigian = date('Y-m-d H:i:s');
                setTrangThaiHoSo($inputs['madonvi'], $model, ['madonvi' => $inputs['madonvi'], 'thoigian' => $thoigian, 'trangthai' => 'DXKT']);
                setTrangThaiHoSo($model->madonvi, $model, ['trangthai' => 'DXKT']);
                $model->save();
            }
            $request->merge(['mahosokt' => $inputs['mahosokt']]);
            return $this->DanhSach($request);
        } else
            return view('errors.notlogin');
    }

    public function DanhSach(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $model =  dshosokhenthuong::where('mahosokt', $inputs['mahosokt'])->first();
            $m_chitiet = dshosokhenthuong_chitiet::where('mahosokt', $model->mahosokt)->get();
            $m_hosokt = dshosotdktcumkhoi::where('mahosotdkt',  $model->mahosotdkt)->get();
            foreach ($m_chitiet as $chitiet) {
                $chitiet->madonvi_kt = $m_hosokt->first()->madonvi;
            }
            $m_khenthuong = dshosokhenthuong_khenthuong::where('mahosokt',  $model->mahosokt)->get();
            foreach ($m_khenthuong as $chitiet) {
                $chitiet->madonvi_kt = $m_hosokt->first()->madonvi;
            }
            $m_danhhieu = dmdanhhieuthidua::all();
            $m_donvi = dsdonvi::all();
            $m_diaban = dsdiaban::all();
            //dd($model);
            return view('NghiepVu.CumKhoiThiDua.KhenThuongHoSoKhenThuong.DanhSach')
                ->with('model', $model)
                ->with('m_chitiet', $m_chitiet)
                ->with('m_danhhieu', $m_danhhieu)
                ->with('m_donvi', $m_donvi)
                ->with('m_diaban', $m_diaban)
                ->with('model_canhan', $m_khenthuong->where('phanloai', 'CANHAN'))
                ->with('model_tapthe', $m_khenthuong->where('phanloai', 'TAPTHE'))
                ->with('a_hinhthuckt', array_column(dmhinhthuckhenthuong::all()->toArray(), 'tenhinhthuckt', 'mahinhthuckt'))
                ->with('a_loaihinhkt', array_column(dmloaihinhkhenthuong::all()->toArray(), 'tenloaihinhkt', 'maloaihinhkt'))
                ->with('a_donvi', array_column(viewdiabandonvi::all()->toArray(), 'tendonvi', 'madonvi'))
                ->with('a_danhhieu', array_column($m_danhhieu->toArray(), 'tendanhhieutd', 'madanhhieutd'))
                ->with('inputs', $inputs)
                ->with('pageTitle', 'Thông tin hồ sơ khen thưởng');
        } else
            return view('errors.notlogin');
    }

    public function XemHoSo(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $model =  dshosokhenthuong::where('mahosokt', $inputs['mahosokt'])->first();
            $m_chitiet = dshosokhenthuong_chitiet::where('mahosokt', $model->mahosokt)->get();
            $m_hosokt = dshosotdktcumkhoi::where('mahosotdkt',  $model->mahosotdkt)->get();
            foreach ($m_chitiet as $chitiet) {
                $chitiet->madonvi_kt = $m_hosokt->first()->madonvi;
            }
            $m_khenthuong = dshosokhenthuong_khenthuong::where('mahosokt',  $model->mahosokt)->get();
            foreach ($m_khenthuong as $chitiet) {
                $chitiet->madonvi_kt = $m_hosokt->first()->madonvi;
            }
            $m_danhhieu = dmdanhhieuthidua::all();
            $m_donvi = dsdonvi::all();
            $m_diaban = dsdiaban::all();
            //dd($model);
            return view('NghiepVu.CumKhoiThiDua.KhenThuongHoSoKhenThuong.Xem')
                ->with('model', $model)
                ->with('m_chitiet', $m_chitiet)
                ->with('m_danhhieu', $m_danhhieu)
                ->with('m_donvi', $m_donvi)
                ->with('m_diaban', $m_diaban)
                ->with('model_canhan', $m_khenthuong->where('phanloai', 'CANHAN'))
                ->with('model_tapthe', $m_khenthuong->where('phanloai', 'TAPTHE'))
                ->with('a_hinhthuckt', array_column(dmhinhthuckhenthuong::all()->toArray(), 'tenhinhthuckt', 'mahinhthuckt'))
                ->with('a_loaihinhkt', array_column(dmloaihinhkhenthuong::all()->toArray(), 'tenloaihinhkt', 'maloaihinhkt'))
                ->with('a_donvi', array_column(viewdiabandonvi::all()->toArray(), 'tendonvi', 'madonvi'))
                ->with('a_danhhieu', array_column($m_danhhieu->toArray(), 'tendanhhieutd', 'madanhhieutd'))
                ->with('inputs', $inputs)
                ->with('pageTitle', 'Thông tin hồ sơ khen thưởng');
        } else
            return view('errors.notlogin');
    }

    public function HoSo(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $m_chitiet = dshosokhenthuong_chitiet::where('mahosokt', $inputs['mahosokt'])->where('mahosotdkt', $inputs['mahosotdkt'])->first();
            if ($inputs['khenthuong'] == 0) {
                dshosokhenthuong_khenthuong::where('mahosokt', $inputs['mahosokt'])->where('mahosotdkt', $inputs['mahosotdkt'])->delete();
            }
            if ($inputs['khenthuong'] == 1 && $m_chitiet->ketqua == 0) {
                $m_hosotdkt = dshosotdktcumkhoi_khenthuong::where('mahosotdkt', $inputs['mahosotdkt'])->get();
                $a_khenthuong = [];
                foreach ($m_hosotdkt as $khenthuong) {
                    $a_khenthuong[] = [
                        'mahosokt' => $inputs['mahosokt'],
                        'mahosotdkt' => $inputs['mahosotdkt'],
                        'madanhhieutd' => $khenthuong->madanhhieutd,
                        'noidungkhenthuong' => '',
                        'phanloai' => $khenthuong->phanloai,
                        //Thông tin cá nhân 
                        'madoituong' => $khenthuong->madoituong,
                        'maccvc' => $khenthuong->maccvc,
                        'tendoituong' => $khenthuong->tendoituong,
                        'ngaysinh' => $khenthuong->ngaysinh,
                        'gioitinh' => $khenthuong->gioitinh,
                        'chucvu' => $khenthuong->chucvu,
                        'lanhdao' => $khenthuong->lanhdao,
                        //Thông tin tập thể
                        'matapthe' => $khenthuong->matapthe,
                        'tentapthe' => $khenthuong->tentapthe,
                        //Kết quả đánh giá
                        'ketqua' => '1',
                        'mahinhthuckt' => $khenthuong->mahinhthuckt,
                    ];
                }
                //dd($a_khenthuong);
                dshosokhenthuong_khenthuong::insert($a_khenthuong);               
            }
            $m_chitiet->ketqua = $inputs['khenthuong'];
            $m_chitiet->save();
            //dd($inputs);
            return redirect('/CumKhoiThiDua/KhenThuongHoSoKhenThuong/DanhSach?mahosokt=' . $inputs['mahosokt']);
        } else
            return view('errors.notlogin');
    }

    public function KetQua(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $model = dshosokhenthuong_khenthuong::findorfail($inputs['id']);
            $model->ketqua = isset($inputs['dieukien']) ? 1 : 0;
            $model->mahinhthuckt = $inputs['mahinhthuckt'];
            $model->save();
            //dd($inputs);
            return redirect('/CumKhoiThiDua/KhenThuongHoSoKhenThuong/DanhSach?mahosokt=' . $model->mahosokt);
        } else
            return view('errors.notlogin');
    }

    public function PheDuyet(Request $request)
    {
        if (Session::has('admin')) {
            $inputs = $request->all();
            $model = dshosokhenthuong::where('mahosokt', $inputs['mahosokt'])->first();
            $donvi = viewdiabandonvi::where('madonvi', $model->madonvi)->first();
            $model->trangthai = 'DKT';
            $model_chitiet = dshosokhenthuong_chitiet::where('mahosokt', $inputs['mahosokt'])->get();
            $m_hosokt = dshosotdktcumkhoi::wherein('mahosotdkt', array_column($model_chitiet->toarray(), 'mahosotdkt'))->get();
            foreach ($m_hosokt as $hoso) {
                $hoso->trangthai = $model->trangthai;
                //khen thương cấp nào thì lưu cấp đó để sau còn thống kê khen thưởng ở các cấp
                setChuyenHoSo($donvi->capdo, $hoso, ['trangthai' => $model->trangthai]);
                //setNhanHoSo();
                $hoso->save();
            }
            $model->save();
            return redirect('/CumKhoiThiDua/KhenThuongHoSoKhenThuong/ThongTin?madonvi='.$model->madonvi.'&macumkhoi='.$model->macumkhoi);
        } else
            return view('errors.notlogin');
    }

    public function LayTieuChuan(Request $request)
    {
        $result = array(
            'status' => 'fail',
            'message' => 'error',
        );
        if (!Session::has('admin')) {
            $result = array(
                'status' => 'fail',
                'message' => 'permission denied',
            );
            die(json_encode($result));
        }
        //dd($request);
        $inputs = $request->all();
        //$m_danhhieu = dmdanhhieuthidua::where('madanhhieutd', $inputs['madanhhieutd'])->first();
        //Chưa tối ưu và tìm kiếm trùng đối tượng
        $m_doituong = dshosokhenthuong_khenthuong::findorfail($inputs['id']);
        //dd($m_doituong);
        $model = dshosotdktcumkhoi_tieuchuan::where('madoituong', $m_doituong->madoituong)
            ->where('madanhhieutd', $m_doituong->madanhhieutd)
            ->where('mahosotdkt', $m_doituong->mahosotdkt)->get();

        $model_tieuchuan = dmdanhhieuthidua_tieuchuan::where('madanhhieutd', $m_doituong->madanhhieutd)->get();

        if (isset($model_tieuchuan)) {

            $result['message'] = '<div class="row" id="dstieuchuan">';

            $result['message'] .= '<div class="col-md-12">';
            $result['message'] .= '<table id="sample_4" class="table table-striped table-bordered table-hover" >';
            $result['message'] .= '<thead>';
            $result['message'] .= '<tr>';
            $result['message'] .= '<th width="2%" style="text-align: center">STT</th>';
            $result['message'] .= '<th style="text-align: center">Tên tiêu chuẩn</th>';
            $result['message'] .= '<th style="text-align: center" width="15%">Bắt buộc</th>';
            $result['message'] .= '<th style="text-align: center" width="15%">Đạt điều kiên</th>';
            $result['message'] .= '<th style="text-align: center" width="10%">Thao tác</th>';
            $result['message'] .= '</tr>';
            $result['message'] .= '</thead>';

            $result['message'] .= '<tbody>';
            $key = 1;
            foreach ($model_tieuchuan as $ct) {
                $ct->dieukien = $model->where('matieuchuandhtd', $ct->matieuchuandhtd)->first()->dieukien ?? 0;
                $result['message'] .= '<tr>';
                $result['message'] .= '<td style="text-align: center">' . $key++ . '</td>';
                $result['message'] .= '<td>' . $ct->tentieuchuandhtd . '</td>';
                $result['message'] .= '<td style="text-align: center">' . $ct->batbuoc . '</td>';
                $result['message'] .= '<td style="text-align: center">' . $ct->dieukien . '</td>';
                $result['message'] .= '<td></td>';
                $result['message'] .= '</tr>';
            }
            $result['message'] .= '</tbody>';
            $result['message'] .= '</table>';
            $result['message'] .= '</div>';
            $result['message'] .= '</div>';
            $result['status'] = 'success';
        }
        die(json_encode($result));
    }
}
