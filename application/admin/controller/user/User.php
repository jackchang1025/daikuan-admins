<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use app\common\library\Auth;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id,username,nickname';

    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\User;
        
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            
            $show = true;
            $ww = [];
            $adminid = $this->request->request('adminid');
            if($adminid && $adminid>0)
            {
                $ww['user.admin_id'] = ['=',$adminid];
            }
            else
            {
                if($this->auth->id!=1)
                {
                    $ww['user.admin_id'] = ['=',$this->auth->id];
                    
                }
            }
            $dkstatus = $this->request->request('dkstatus');
            if($dkstatus && $dkstatus>0)
            {
                $ll = Db::name('user_dai')->where('status',$dkstatus)->field('user_id')->select();
                $ids = [];
                foreach ($ll as $k => $v)
                {
                    $ids[] = $v['user_id'];
                }
                $ww['user.id'] = ['in',$ids];
            }
            $show = Db::name('config')->where('id',25)->value('value');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->with('group')
                ->where($where)
                ->where($ww)
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $k => $v) {
                $v->avatar = $v->avatar ? cdnurl($v->avatar, true) : letter_avatar($v->nickname);
                $v->hidden(['password', 'salt']);
                $dai = Db::name('user_dai')->where('user_id',$v->id)->find();
                $v->admin_id = Db::name('admin')->where('id',$v->admin_id)->value('nickname');
                if($dai)
                {
                    $v->dk_status = $dai['status'];
                }
                else 
                {
                    $v->dk_status = 0;
                }
                $v->jq_image = Db::name('user_dai')->where('user_id',$v->id)->value('image');
                $v->is_status = $show;
            }
            $result = array("total" => $list->total(), "rows" => $list->items(),'is_status'=>$show);

            return json($result);
        }
        $show = true;
        if($this->auth->id!=1)
        {
            $show = Db::name('config')->where('id',25)->value('value');
            if($show==0)
            {
                $show = false;
            }
            else
            {
                $show = true;
            }
        }
        
        $adminlst = Db::name('admin')->select();
        $this->view->assign('show', $show);
        $this->view->assign('adminlst', $adminlst);
        return $this->view->fetch();
    }
    
    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $info = Db::name('user')->where('mobile',$params['mobile'])->find();
            if($info)
            {
                $this->error(__('当前客户已存在'));
            }
            $info = Db::name('user')->where('sfz',$params['sfz'])->find();
            if($info)
            {
                $this->error(__('当前客户已存在'));
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $id = Db::name('user')->where('mobile',$params['mobile'])->value('id');
        $dd = [];
        $dd['user_id'] = $id;
        $dai = Db::name('user_dai')->insertGetId($dd);
        $shou = Db::name('user_shou')->insertGetId($dd);
        Db::name('user')->where('id',$id)->update(['dai_id'=>$dai,'shou_id'=>$shou,'admin_id'=>$this->auth->id]);
        $this->success();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        Auth::instance()->delete($row['id']);
        
        $dai = Db::name('user_dai')->where('user_id',$row['id'])->delete();
        $shou = Db::name('user_shou')->where('user_id',$row['id'])->delete();
        $this->success();
    }
    
    public function import()
    {
        $file = $this->request->param("file");
        $lines = file_get_contents('.'.$file);
        $line=explode("\n",$lines);
        foreach ($line as $k => $v)
        {
            $dd = [];
            $info=explode("-",$v);
            if(isset($info[0]) && $info[0])
            {
                $dd['username'] = $info[0];
            }
            if(isset($info[1]) && $info[1])
            {
                $dd['mobile'] = $info[1];
            }
            if(isset($info[2]) && $info[2])
            {
                $dd['sfz'] = $info[2];
            }
            if(isset($info[3]) && $info[3])
            {
                $dd['khyh'] = $info[3];
            }
            if(isset($info[4]) && $info[4])
            {
                $dd['yhk_num'] = $info[4];
            }
            if(isset($info[5]) && $info[5])
            {
                $dd['khdz'] = $info[5];
            }
            $dd['admin_id'] = $this->auth->id;
            $dd['status'] = 'normal';
            $dd['jointime'] = time();
            $dd['createtime'] = time();
            $id = Db::name('user')->insertGetId($dd);
            $ddd = [];
            $ddd['user_id'] = $id;
            $dai = Db::name('user_dai')->insertGetId($ddd);
            $shou = Db::name('user_shou')->insertGetId($ddd);
        }
        $this->success();
    }
    
    public function export()
    {
        $param = $this->request->param();
        if(isset($param['ids']) && $param['ids']=='all')
        {
            $data = Db::name('user')->select();
        }
        elseif(isset($param['ids']) && $param['ids'])
        {
            $data = Db::name('user')->where('id','in',$param['ids'])->select();
        }
        else
        {
            $this->error(__("参数错误"));
        }
        // 创建一个新的电子表格对象
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('数据列表');

        // 设置默认列宽和行高
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getDefaultRowDimension()->setRowHeight(15);

        // 设置表头样式和内容
        // $styleArray = [
        //     'font' => [
        //         'bold' => true,
        //     ],
        //     'alignment' => [
        //         'horizontal' => Alignment::HORIZONTAL_CENTER,
        //     ],
        //     'borders' => [
        //         'outline' => [
        //             'borderStyle' => Border::BORDER_THIN,
        //             'color' => ['argb' => 'FFFF0000'],
        //         ],
        //     ],
        // ];
        // $sheet->getStyle('1')->applyFromArray($styleArray);
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', '姓名');
        $sheet->setCellValue('C1', '手机号');
        $sheet->setCellValue('D1', '身份证号');
        $sheet->setCellValue('E1', '开户银行');
        $sheet->setCellValue('F1', '银行卡号');
        // 添加更多列标题...
        
        // 填充数据行
        $row = 2; // 从第二行开始，第一行是标题行
        foreach ($data as $item) {
            $sheet->setCellValueExplicit("A$row", $item['id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("B$row", $item['username'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C$row", $item['mobile'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("D$row", $item['sfz'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("E$row", $item['khyh'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("F$row", $item['yhk_num'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $row++;
        }

        // 设置自动宽度以适应内容
        foreach (range('A', 'F') as $columnID) { // 根据实际列数调整范围，例如 'A' 到 'Z' 或 'A' 到 'C' 等。
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 创建 Excel 文件并发送到浏览器下载或保存到服务器文件系统。
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user_' . date('Ymd H:i:s') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output'); // 直接输出到浏览器下载。如果你希望保存到服务器，可以使用文件路径替代 'php://output'。
    }
    
    public function qingkong()
    {
        Db::name('user')->where('id','>',0)->delete();
        Db::name('user_dai')->where('id','>',0)->delete();
        Db::name('user_shou')->where('id','>',0)->delete();
        $this->success();
    }
}
