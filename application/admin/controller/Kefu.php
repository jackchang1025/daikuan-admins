<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Kefu extends Backend
{

    /**
     * Khxz模型对象
     * @var \app\common\model\Khxz
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }


    public function index()
    {
        // 从系统配置读取客服号（general/config 保存后会写入 fa_config & site.php）
        // 这里以数据库为准，避免配置文件未刷新导致读取不到
        $kefuConfig = Db::name('config')->where('name', 'kefunum')->find();

        $kefunum = trim((string)($kefuConfig['value'] ?? ''));

        if ($kefunum === '') {
            throw new \think\Exception('系统配置项 kefunum 未填写，请在 系统配置 中设置后再访问');
        }

        // 方案：跨站嵌入需要 SameSite=None;Secure，必须使用 HTTPS
        // 从配置读取客服系统基址（例如：https://kefu.example.com 或 https://kefu.example.com:8443）
        $baseUrlConfig = Db::name('config')->where('name', 'kefu_base_url')->find();
        $baseUrl = trim((string)($baseUrlConfig['value'] ?? ''));
        if ($baseUrl === '') {
            throw new \think\Exception('系统配置项 kefu_base_url 未填写，请在 系统配置 中设置客服系统 HTTPS 基址');
        }
        $baseUrl = rtrim($baseUrl, '/');

        $url = $baseUrl . '/service/index/index/u/' . $kefunum . '.html';
        $this->view->assign('url', $url);
        return $this->view->fetch();
    }
}
