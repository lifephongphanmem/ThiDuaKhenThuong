<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDshosotdktcumkhoiTaptheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dshosotdktcumkhoi_tapthe', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mahosotdkt')->nullable();
            $table->string('maphanloaitapthe')->nullable(); //Tập thể nhà nước; Doanh nghiệp; Hộ gia đình
            //Thông tin tập thể            
            $table->string('tentapthe')->nullable();
            $table->string('ghichu')->nullable(); //
            //Kết quả đánh giá
            $table->boolean('ketqua')->default(0); //
            $table->string('madanhhieutd')->nullable();
            $table->string('mahinhthuckt')->nullable();
            $table->string('lydo')->nullable();
            $table->string('noidungkhenthuong')->nullable(); //in trên phôi bằng khen
            $table->string('madonvi')->nullable(); //phục vụ lấy dữ liệu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dshosotdktcumkhoi_tapthe');
    }
}