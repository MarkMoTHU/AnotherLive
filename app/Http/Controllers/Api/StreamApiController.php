<?php
/**
 * Created by PhpStorm.
 * User: Volio
 * Date: 2016/9/20
 * Time: 19:38
 * 用于前端播放器获取信息
 */

namespace App\Http\Controllers\Api;


use App\Models\Room;
use App\Services\PiliService;

class StreamApiController
{
    private $piliService;

    public function __construct(PiliService $piliService)
    {
        $this->piliService = $piliService;
    }

    //通过房间ID获取播放地址
    public function getPlayUrl($rid)
    {
        $streamKey = Room::getStreamKeyByRid($rid);
        return $this->piliService->getPlayUrl($streamKey);
    }
}