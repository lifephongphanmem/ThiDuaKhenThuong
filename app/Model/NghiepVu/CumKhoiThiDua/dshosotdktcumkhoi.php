<?php

namespace App\Model\NghiepVu\CumKhoiThiDua;

use Illuminate\Database\Eloquent\Model;

class dshosotdktcumkhoi extends Model
{
    protected $table = 'dshosotdktcumkhoi';
    protected $fillable = [
        'id',
        'mahosotdkt',
        'ngayhoso',
        'noidung',
        'phanloai', //hồ sơ thi đua; hồ sơ khen thưởng (để sau thống kê)
        'maloaihinhkt', //lấy từ phong trào nếu là hồ sơ thi đua
        'maphongtraotd', //tùy theo phân loại
        'macumkhoi',
        'ghichu',
        //File đính kèm
        'baocao', //báo cáo thành tích
        'bienban', //biên bản cuộc họp
        'tailieukhac', //tài liệu khác
        //Kết quả khen thưởng
        'mahosokt',
        //Trạng thái đơn vị
        'madonvi',
        'madonvi_nhan',
        'lydo',
        'thongtin', //chưa dùng
        'trangthai',
        'thoigian',
        //Trạng thái huyện
        'madonvi_h',
        'madonvi_nhan_h',
        'lydo_h',
        'thongtin_h', //chưa dùng
        'trangthai_h',
        'thoigian_h',
        //Trạng thái tỉnh
        'madonvi_t',
        'madonvi_nhan_t',
        'lydo_t',
        'thongtin_t', //chưa dùng
        'trangthai_t',
        'thoigian_t',
        //Trạng thái trung ương
        'madonvi_tw',
        'madonvi_nhan_tw',
        'lydo_tw',
        'thongtin_tw', //chưa dùng
        'trangthai_tw',
        'thoigian_tw',
    ];
}
