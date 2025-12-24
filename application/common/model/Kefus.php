<?php

namespace app\common\model;

use think\Model;


class Kefus extends Model
{

    

    

    // 表名
    protected $name = 'kefu';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'kefu_status_text'
    ];
    

    
    public function getKefuStatusList()
    {
        return ['0' => __('Kefu_status 0'), '1' => __('Kefu_status 1')];
    }


    public function getKefuStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['kefu_status'] ?? '');
        $list = $this->getKefuStatusList();
        return $list[$value] ?? '';
    }




}
