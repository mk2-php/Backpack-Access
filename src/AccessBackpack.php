<?php

namespace mk2\backpack_access;

use Mk2\Libraries\Backpack;

class AccessBackpack extends Backpack{

    private const METHOD_GET = "GET";
    private const METHOD_POST = "POST";
    private const METHOD_PUT = "PUT";
    private const METHOD_DELETE = "DELETE";

    public $baseUrl = "";
    public $url = "";
    public $method = self::METHOD_GET;
    public $headers = [];
    public $requestData = [];
    public $sslVerify = false;
    public $cainfo = null;

    /**
     * baseUrl
     * @param $url
     */
    public function baseUrl($url){
        $this->baseUrl=$url;
        return $this;
    }
	
    /**
     * url
     * @param $url
     */
    public function url($url){
        $this->url=$url;
        return $this;
    }

    /**
     * method
     * @param $method
     */
    public function method($method){
        $this->method=$method;
        return $this;
    }

    /**
     * setHeader
     * @param $params
     */
    public function setHeader($params){

        foreach($params as $field=>$val){
            $this->headers[$field]=$val;
        }

        return $this;
    }

    /**
     * delHeader
     * @param $field
     */
    public function delHeader($field){
        unset($this->headers[$field]);
        return $this;
    }

    /**
     * setData
     * @param $params
     */
    public function setData($params){
        $this->requestData=$params;
        return $this;
    }

    /**
     * sslVerify
     * @param $juge = true
     */
    public function sslVerify($juge=true){
        $this->sslVerify=$juge;
        return $this;
    }

    /**
     * cainfo
     * @param $path
     */
    public function cainfo($path){
        $this->cainfo=$path;
        return $this;
    }

    /**
     * send
     * @param $params = null
     */
    public function send($params=null){
        $fullUrl=$this->baseUrl.$this->url;
        $method=strtoupper($this->method);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        if($method != self::METHOD_GET){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        if($method==self::METHOD_POST){
            curl_setopt($ch,CURLOPT_POSTFIELDS,$this->requestData);
        }
        else{
            curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($this->requestData));
        }

        if($this->headers){
            $headers=[];
            foreach($this->headers as $field=>$value){
                $headers[]=$field.": ".$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,$this->sslVerify);

        if($this->cainfo){
            curl_setopt($ch,CURLOPT_CAINFO,$this->cainfo);
        }

        $res = curl_exec($ch);        
        $info = curl_getInfo($ch);

        curl_close($ch);
        
        return new AccessResponse($res,$info,$this->method);
    }
}

class AccessResponse{

    private $result;
    private $info;
    private $status;
    private $contentType;
    private $method;

    /**
     * __construct
     * @param $result
     * @param $info
     * @param $method
     */
    public function __construct($result,$info,$method){

        $this->method=$method;
        $this->result=$result;
        $this->info=$info;
        $this->status=$info["http_code"];
        $this->contentType=$info["content_type"];

    }

    /**
     * toResult
     */
    public function toResult(){
        return $this->result;
    }

    /**
     * toInfo
     */
    public function toInfo(){
        return $this->info;
    }

    /**
     * toStatus
     */
    public function toStatus(){
        return $this->status;
    }

    /**
     * toContentType
     */
    public function toContentType(){
        return $this->contentType;
    }

    /**
     * toMethod
     */
    public function toMethod(){
        return $this->method;
    }
}