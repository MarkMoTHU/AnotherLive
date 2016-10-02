<?php
/**
 * Created by PhpStorm.
 * User: Volio
 * Date: 2016/9/18
 * Time: 22:42
 */

namespace App\Services;

use Qiniu\Pili;

class PiliService
{
    private $accessKey;
    private $secretKey;
    private $hubName;
    private $RTMPPublishUrl;
    private $RTMPPlayUrl;
    private $HLSPlayUrl;
    private $HDLPlayUrl;
    private $snapshotPlayUrl;

    public function __construct($accessKey, $secretKey, $hubName
        , $RTMPPublishUrl, $RTMPPlayUrl, $HLSPlayUrl, $HDLPlayUrl, $snapshotPlayUrl)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->hubName = $hubName;
        $this->RTMPPublishUrl = $RTMPPublishUrl;
        $this->RTMPPlayUrl = $RTMPPlayUrl;
        $this->HLSPlayUrl = $HLSPlayUrl;
        $this->HDLPlayUrl = $HDLPlayUrl;
        $this->snapshotPlayUrl = $snapshotPlayUrl;
    }

    /**
     * @param $streamKey
     * @return mixed
     */
    public function getPlayUrl($streamKey)
    {
        $url['rtmp'] = $this->RTMPPlayURL($this->RTMPPlayUrl, $this->hubName, $streamKey);
        $url['hls'] = $this->HLSPlayURL($this->HLSPlayUrl, $this->hubName, $streamKey);
        $url['hdl'] = $this->HDLPlayURL($this->HDLPlayUrl, $this->hubName, $streamKey);
        $url['snapshot'] = $this->SnapshotPlayURL($this->snapshotPlayUrl, $this->hubName, $streamKey);
        return $url;
    }

    /**
     * @param $streamKey
     * @return string
     */
    public function getPublishUrl($streamKey)
    {
        return $this->RTMPPublishURL($this->RTMPPublishUrl,
            $this->hubName, $streamKey, 3600, $this->accessKey, $this->secretKey);
    }

    /**
     * @param $streamKey
     * @return \Exception|Pili\Stream
     */
    public function createStream($streamKey)
    {
        try {
            return $this->getHub()->create($streamKey);
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * @param $streamKey
     * @return array
     */
    public function getStreamInfo($streamKey)
    {
        return $this->getStream($streamKey)->info();
    }

    /**
     * @param $streamKey
     * @return mixed
     */
    public function getLiveStatus($streamKey)
    {
        return $this->getStream($streamKey)->liveStatus();
    }

    /**
     * @param $streamKey
     * @return mixed
     */
    public function disableStream($streamKey)
    {
        $stream = $this->getStream($streamKey);
        return $stream->disable();
    }

    /**
     * @param $streamKey
     * @return mixed
     */
    public function enableStream($streamKey)
    {
        $stream = $this->getStream($streamKey);
        return $stream->enable();
    }

    /**
     * @return Pili\Hub
     */
    private function getHub()
    {
        $mac = new Pili\Mac($this->accessKey, $this->secretKey);
        $client = new Pili\Client($mac);
        return $client->hub($this->hubName);
    }

    /**
     * @param $streamKey
     * @return Pili\Stream
     */
    private function getStream($streamKey)
    {
        return $this->getHub()->stream($streamKey);
    }

    private function RTMPPublishURL($domain, $hub, $streamKey, $expireAfterSeconds, $accessKey, $secretKey)
    {
        $expire = time() + $expireAfterSeconds;
        $path = sprintf("/%s/%s?e=%d", $hub, $streamKey, $expire);
        $token = $accessKey . ":" . Pili\Utils::sign($secretKey, $path);
        return sprintf("rtmp://%s%s&token=%s", $domain, $path, $token);
    }

    private function RTMPPlayURL($domain, $hub, $streamKey)
    {
        return sprintf("rtmp://%s/%s/%s", $domain, $hub, $streamKey);
    }

    private function HLSPlayURL($domain, $hub, $streamKey)
    {
        return sprintf("http://%s/%s/%s.m3u8", $domain, $hub, $streamKey);
    }

    private function HDLPlayURL($domain, $hub, $streamKey)
    {
        return sprintf("http://%s/%s/%s.flv", $domain, $hub, $streamKey);
    }

    private function SnapshotPlayURL($domain, $hub, $streamKey)
    {
        return sprintf("http://%s/%s/%s.jpg", $domain, $hub, $streamKey);
    }
}