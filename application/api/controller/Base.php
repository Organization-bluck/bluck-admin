<?php
/**
 * Created by PhpStorm.
 * User: xuheng
 * Date: 2017/12/6
 * Time: 上午11:05
 */
namespace app\api\controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use think\Config;
use think\Controller;
use think\Exception;
use think\Request;
use think\Response;

class Base extends Controller
{
    protected $msg = '操作成功';
    protected $code = 200;
    protected $data = [];
    protected $error = '';
    protected $page_size = 10;

    protected $user_id='';
    protected $request_params = [];

    public function _initialize()
    {
        try{
            $this->setHeader();
            $this->request_params = Request::instance()->param();

        } catch (Exception $e) {
            $this->response(-3, $e->getMessage());
        }
    }

    protected function setHeader()
    {
        header('Access-Control-Allow-Origin: ' . Config::get('allow_origin'));
        header('Content-type:application/json;charset=utf-8');
    }

    protected function _request($url, $data = [], $requestType='GET', $is_asyn=false)
    {
        try{

            //是否异步
            if($is_asyn) {
                $rec = new Client(['timeout' => 1]);
                $rec->requestAsync($requestType, $url, $data)->wait();
                exit;
            } else {
                $rec = new Client();
                $result = $rec->request($requestType, $url, $data);
            }

            return json_decode($result->getBody(), true);
        } catch (RequestException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    //计算分页开始条数
    protected function _getStartCount()
    {
        return max(0, (input('request.page/d', 0)-1)*$this->page_size);
    }

    protected function response($code = 200, $msg = 'Success', $data = [])
    {
        $data = [
            'code'  => $code? :$this->code,
            'msg'   => $msg?  :$this->msg,
            'data'  => $data? :$this->data,
        ];
        $obj = Response::create($data, input('request.format', 'json'))->code(200);
        $obj->send();
        exit;
    }


    public function __destruct()
    {
        $this->response();
    }
}