<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public static function addRoom($uid)
    {
        $user = User::select('name')->where('id', $uid)->first();
        $room = new Room();
        $room->uid = $uid;
        $room->streamKey = sha1($uid . time());
        $room->title = $user['name'] . '的直播间';
        $room->description = '暂无简介';
        $room->category_id = 1;
        if ($room->save()) {
            User::where('id', $uid)->update([
                'authority' => 'waiting'
            ]);
            return true;
        } else
            return false;
    }

    public static function getStreamKey($uid)
    {
        $room = self::select('streamKey')->where('uid', $uid)->first();
        return $room['streamKey'];
    }

    public static function getStreamKeyByRid($rid)
    {
        $room = self::select('streamKey')->where('id', $rid)->first();
        return $room['streamKey'];
    }
}
