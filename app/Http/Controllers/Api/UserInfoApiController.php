<?php

/**
 * Created by PhpStorm.
 * User: Volio
 * Date: 2016/9/20
 * Time: 15:39
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\PiliService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserInfoApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //申请直播间
    public function applyLiveRoom()
    {
        //只有user权限的用户可以申请
        if (Gate::denies('isUser'))
            return response()->json(['error' => 'Permission Denied'], 403);

        if(Room::addRoom(Auth::user()->id))
            return 'Success';
        else
            return 'Failed';
    }

    //获取推流地址
    public function getPublishUrl(PiliService $piliService)
    {
        //只有streamer权限的用户可以获取
        if (Gate::denies('isStreamer'))
            return response()->json(['error' => 'Permission Denied'], 403);

        $streamKey = Room::getStreamKey(Auth::user()->id);
        return $piliService->getPublishUrl($streamKey);
    }

    //更新直播间信息
    public function updateRoomInfo(Request $request)
    {

    }
}