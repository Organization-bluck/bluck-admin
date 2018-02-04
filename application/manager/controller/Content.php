<?php
/**
 * Created by PhpStorm.
 * User: xuheng
 * Date: 2018/1/23
 * Time: 上午11:50
 */

namespace app\manager\controller;


use controller\BasicAdmin;
use service\DataService;
use think\Db;

class Content extends BasicAdmin
{
    /**
     * 默认数据表
     * @var string
     */
    public $table = 'centent';

    /**
     * 列表
     */
    public function index()
    {
        $this->title = '内容信息列表';
        $get = $this->request->get();
        $db = Db::name($this->table)->where(['is_del' => 0])->order('id desc');
        // 搜索条件
        if (isset($get['title']) && $get['title'] !== '') {
            $db->where('title', 'like', "%{$get['title']}%");
        }
        if(!empty($get['c_id'])) {
            $db->where('c_id', 'eq', $get['c_id']);
        }

        $this->assign('content_category', Db::table('content_category')->where(['status'=>1,'is_del'=>0])->select());
        return parent::_list($db);
    }

    /**
     * 添加商品
     */
    public function add()
    {
        $this->title = '添加商品';
        return $this->_form($this->table, 'form');
    }

    /**
     * 编辑商品
     * @return string
     */
    public function edit()
    {
        $this->title = '编辑商品';
        return $this->_form($this->table, 'form');
    }

    /**
     * 删除
     */
    public function del() {
        if (DataService::update($this->table)) {
            $this->success("删除成功！", '');
        }
        $this->error("删除失败，请稍候再试！");
    }

    /**
     * 商品类型禁用
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            $this->success("信息禁用成功！", '');
        }
        $this->error("信息禁用失败，请稍候再试！");
    }

    /**
     * 商品类型启用
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("信息启用成功！", '');
        }
        $this->error("信息启用失败，请稍候再试！");
    }

    /**
     * 表单处理
     * @param $data
     */
    protected function _form_filter($data)
    {
        if ($this->request->isPost() && isset($data['title'])) {
            $db = Db::table($this->table)->where('title', $data['title']);
            !empty($data['id']) && $db->where('id', 'neq', $data['id']);
            $db->count() > 0 && $this->error('此商品名已存在！');
        } else {
            $this->assign('goods_type', Db::table('goods_type')->where(['record_status'=>0,'is_del'=>0])->select());
        }
    }
}