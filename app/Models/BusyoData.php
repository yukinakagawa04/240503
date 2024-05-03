<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusyoData extends Model
{
    //use HasFactory;
    public $timestamps = false;//マスタデータとして使用するので不要とのことだったので、timestampsをfalseに指定。
    protected $guarded = ['id'];//データ追加時に思わぬエラーが起きないようidを指定
}
