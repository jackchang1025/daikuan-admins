<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Validate;
use think\Db;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'userinfo', 'userinfos', 'changeemail', 'changemobile', 'third','huankuan','huankuans','hkzh','ht','htimage'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }
    
    public function huankuan()
    {
        $user_id = $this->request->param('user_id');
        $info = Db::name('user_dai')->where('user_id',$user_id)->find();
        $user = Db::name('user')->where('id',$user_id)->find();
        $infos = Db::name('user_shou')->where('user_id',$user_id)->find();
        $info['yq'] = 0;
        $info['yq_num'] = '0天';
        if($info['status']!=1 && $info['status']!=3)
        {
            if(strtotime($info['hkrq'])<time())
            {
                $info['yq'] = 1;
                $days = round(abs((time() - strtotime($info['hkrq'])) / (60 * 60 * 24)));
                $info['yq_num'] = $days.'天';
                if($info['yq_money']==0)
                {
                    $info['yq_money'] = $info['money'];
                }
            }
        }
        else
        {
            if($info['status']==3)
            {
                $info['yq'] = 2;
            }
        }
        // if($info['yh_money']==0)
        // {
        //     $info['yh_money'] = $info['money'];
        // }
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        if($info['image'])
        {
            $info['image'] = $domain.$info['image'];
        }
         $info['content'] = str_replace('username', $user['username'], $info['content']);
        $info['content'] = str_replace('sfz', $user['sfz'], $info['content']);
        $info['zfupload'] = $infos['zfupload'];
        $info['zfimage'] = $infos['zfimage'];
        $this->success('查询成功', $info);
    }
    
    public function huankuans()
    {
        $user_id = $this->request->param('user_id');
        $user = Db::name('user')->where('id',$user_id)->find();
        $info = Db::name('user_dai')->where('user_id',$user_id)->find();
        $info['yq'] = 0;
        $info['yq_num'] = '0天';
        if($info['status']!=1 && $info['status']!=3)
        {
            if(strtotime($info['hkrq'])<time())
            {
                $info['yq'] = 1;
                $days = round(abs((time() - strtotime($info['hkrq'])) / (60 * 60 * 24)));
                $info['yq_num'] = $days.'天';
                if($info['yq_money']==0)
                {
                    $info['yq_money'] = $info['money'];
                }
            }
        }
        else
        {
            if($info['status']==3)
            {
                $info['yq'] = 2;
            }
        }
        // if($info['yh_money']==0)
        // {
        //     $info['yh_money'] = $info['money'];
        // }
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        if($info['image'])
        {
            $info['image'] = $domain.$info['image'];
        }
        
        $info['content'] = str_replace('username', $user['username'], $info['content']);
        $info['content'] = str_replace('sfz', $user['sfz'], $info['content']);
        $user['hkinfo'] = $info;
        $this->success('查询成功', $user);
    }
    
    public function ht()
    {
        $user_id = $this->request->param('user_id');
        $user = Db::name('user')->where('id',$user_id)->find();
        $info = Db::name('user_dai')->where('user_id',$user_id)->find();
        $info['yq'] = 0;
        $info['yq_num'] = '0天';
        if($info['status']!=1 && $info['status']!=3)
        {
            if(strtotime($info['hkrq'])<time())
            {
                $info['yq'] = 1;
                $days = round(abs((time() - strtotime($info['hkrq'])) / (60 * 60 * 24)));
                $info['yq_num'] = $days.'天';
                if($info['yq_money']==0)
                {
                    $info['yq_money'] = $info['money'];
                }
            }
        }
        else
        {
            if($info['status']==3)
            {
                $info['yq'] = 2;
            }
        }
        // if($info['yh_money']==0)
        // {
        //     $info['yh_money'] = $info['money'];
        // }
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        if($info['image'])
        {
            $info['image'] =$domain.$info['image'];
        }
        $infos = Db::name('app')->where('id',1)->find();
        $ht = Db::name('hetong')->where('id',1)->value('content');
        $company = '平安贷金融有限公司';
        $infosss = Db::name('admin')->where('domain',$_SERVER['HTTP_HOST'])->find();
        if($infosss)
        {
            $company = $infosss['company_name'];
        }
        else
        {
            $infosss = Db::name('admin')->where('id',1)->find();
            if($infosss)
            {
                $company = $infosss['company_name'];
            }
        }
        
        $parsedUrl = parse_url("http://" . $_SERVER['HTTP_HOST']);
        $hostParts = explode('.', $parsedUrl['host']);
        $mainDomain = implode('.', array_slice($hostParts, -2)); // 取最后两部分
        
        $infosss = Db::name('admin')->where('fan_domain',$mainDomain)->find();
        if($infosss)
        {
            $company = $infosss['company_name'];
        }
        
        $ht = str_replace('company', $company, $ht);
        $ht = str_replace('username', $user['username'], $ht);
        $ht = str_replace('interest', $infos['rll'], $ht);
        
        $ht = str_replace('sfz', $user['sfz'], $ht);
        $ht = str_replace('phone', $user['mobile'], $ht);
        $ht = str_replace('loan_time', $info['dkrq'], $ht);
        $ht = str_replace('repayment_time', $info['hkrq'], $ht);
        $ht = str_replace('loan_R', $this->amountToChinese($info['money']), $ht);
        $ht = str_replace('loan', $info['money'], $ht);
        
        
        $this->success('查询成功', $ht);
    }
    
    public function htimage()
    {
        $infos = Db::name('hetong')->where('id',1)->find();
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        if($infos['images'])
        {
            $infos['images'] =$domain.$infos['images'];
        }
        $info = Db::name('admin')->where('domain',$_SERVER['HTTP_HOST'])->find();
        if($info)
        {
            $infos['images'] =$domain.$info['company_image'];
        }
        else
        {
            $info = Db::name('admin')->where('id',1)->find();
            if($info)
            {
                $infos['images'] =$domain.$info['company_image'];
            }
        }
        $parsedUrl = parse_url("http://" . $_SERVER['HTTP_HOST']);
        $hostParts = explode('.', $parsedUrl['host']);
        $mainDomain = implode('.', array_slice($hostParts, -2)); // 取最后两部分
        
        $info = Db::name('admin')->where('fan_domain',$mainDomain)->find();
        if($info)
        {
            $infos['images'] =$domain.$info['company_image'];
        }
        $this->success('查询成功', $infos);
    }
    
    
function amountToChinese($num) {
    $cns = ['零','壹','贰','叁','肆','伍','陆','柒','捌','玖'];
    $units = ['','拾','佰','仟','万','拾','佰','仟','亿','拾','佰','仟','万亿'];
    $decUnits = ['角','分'];
    
    // 验证输入
    if (!is_numeric($num)) return '非法输入';
    $num = round($num, 2);
    if ($num >= 1e12) return '超出处理范围';

    // 分割整数小数
    list($integer, $decimal) = explode('.', sprintf('%.2f', $num));
    $decimal = substr($decimal, 0, 2);

    // 整数部分转换
    $intStr = '';
    $len = strlen($integer);
    for ($i = 0; $i < $len; $i++) {
        $n = $integer[$i];
        $pos = $len - $i - 1;
        if ($n != '0') {
            $intStr .= $cns[$n] . $units[$pos];
        } elseif ($i < $len - 1 && $integer[$i + 1] != '0' && $pos % 4 != 0) {
            $intStr .= $cns[0];
        }
    }
    $intStr = $intStr ? $intStr . '元' : '';

    // 小数部分转换
    $decStr = '';
    if ($decimal[0] != '0') $decStr .= $cns[$decimal[0]] . '角';
    if ($decimal[1] != '0') $decStr .= $cns[$decimal[1]] . '分';

    // 合并结果
    $result = $intStr . $decStr ?: '整';
    return $result ?: '零元整';
}

    
    public function hkzh()
    {
        $user_id = $this->request->param('user_id');
        $dd=  [];
        $info = Db::name('user_shou')->where('user_id',$user_id)->find();
        $infos = Db::name('user_dai')->where('user_id',$user_id)->find();
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        if($info['name'] && $info['yhkh'])
        {
            $dd['name'] = $info['name'];
            $dd['khyh'] = $info['khyh'];
            $dd['yhkh'] = $info['yhkh'];
            $dd['khdz'] = $info['khdz'];
            $dd['zfupload'] = $info['zfupload'];
            $dd['beizhu'] = $info['beizhu'];
            $dd['sanfang'] = $info['sanfang'];
            $dd['yh_money'] = $infos['yh_money'];
            $dd['image'] = '';
            $dd['images'] = '';
            $dd['wximage'] = '';
            $dd['zfbimage'] = '';
            $dd['zfimage'] = '';
            
            if($info['image'])
            {
                $dd['image'] = $domain.$info['image'];
            }
            if($info['image2'])
            {
                $dd['images'] = $domain.$info['image2'];
            }
            if($info['wximage'])
            {
                $dd['wximage'] = $domain.$info['wximage'];
            }
            if($info['zfbimage'])
            {
                $dd['zfbimage'] = $domain.$info['zfbimage'];
            }
            if($info['zfimage'])
            {
                $dd['zfimage'] = $domain.$info['zfimage'];
            }
        }
        else
        {
            $info = Db::name('app')->where('id',1)->find();
            $dd['name'] = $info['name'];
            $dd['khyh'] = $info['khyh'];
            $dd['yhkh'] = $info['yhkh'];
            $dd['khdz'] = $info['khyhdz'];
            $dd['beizhu'] = $info['beizhu'];
            $dd['yh_money'] = $infos['yh_money'];
            $dd['sanfang'] = 0;
            $dd['zfupload'] = 0;
            $dd['image'] = '';
            $dd['images'] = '';
            $dd['wximage'] = '';
            $dd['zfbimage'] = '';
            $dd['zfimage'] = '';
            if($info['image'])
            {
                $dd['image'] = $domain.$info['image'];
            }
            if($info['image2'])
            {
                $dd['images'] = $domain.$info['image2'];
            }
        }
        $this->success('查询成功', $dd);
    }
    
    public function jiekuan()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }
    
    public function userinfo()
    {
        $user_id = $this->request->param('user_id');
        $user = Db::name('user')->where('id',$user_id)->find();
        $infossss = Db::name('admin')->where('domain',$_SERVER['HTTP_HOST'])->find();
        if($infossss)
        {
             $user['kefunum'] =$infossss['kefuid'];
        }
        else
        {
            $user['kefunum'] = Db::name('config')->where('id',23)->value('value');
        }
        $parsedUrl = parse_url("http://" . $_SERVER['HTTP_HOST']);
        $hostParts = explode('.', $parsedUrl['host']);
        $mainDomain = implode('.', array_slice($hostParts, -2)); // 取最后两部分
        
        $infossss = Db::name('admin')->where('fan_domain',$mainDomain)->find();
        if($infossss)
        {
            $user['kefunum'] =$infossss['kefuid'];
        }
        $user['avatar'] = letter_avatar($user['username']);
        $base64 = $user['avatar']; // 替换为你的Base64字符串
        $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/20251020/';
        $name = time().$user_id .'.svg';
        $filePath   = $path . $name;
        // $base64就是前台传给你的base64图片代码
        $base_img = str_replace('data:image/svg+xml;base64,', '', $base64);
        //  设置文件路径和命名文件名称
        $path = $filePath;
        //  创建将数据流文件写入我们创建的文件内容中
        // print_r(explode(',',$base_img));die;
        file_put_contents($path, base64_decode(explode(',',$base_img)[0]));
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        $user['avatar'] = $domain.'/uploads/20251020/'.$name;
        // 获取当前时间
        $time = time();
        
        // 获取当前小时数
        $hour = date('G', $time); // 'G' 返回没有前导零的 24 小时格式的小时数
        
        if ($hour >= 6 && $hour < 12) {
            $user['tt'] = '上午好';
        } elseif ($hour >= 12 && $hour < 18) {
            $user['tt'] = '下午好';
        } elseif ($hour >= 18 && $hour <= 23) {
            $user['tt'] = '晚上好';
        } else {
            $user['tt'] = '凌晨好';
        }
        $this->success('查询成功',$user);
    }
    
    public function userinfos()
    {
        $user_id = $this->request->param('user_id');
        $user = Db::name('user')->where('id',$user_id)->find();
        $user['avatar'] = letter_avatar($user['username']);
        $base64 = $user['avatar']; // 替换为你的Base64字符串
        $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.date('Ymd',time())."/";
        $name = time().$user_id .'.svg';
        $filePath   = $path . $name;
        // $base64就是前台传给你的base64图片代码
        $base_img = str_replace('data:image/svg+xml;base64,', '', $base64);
        //  设置文件路径和命名文件名称
        $path = $filePath;
        //  创建将数据流文件写入我们创建的文件内容中
        // print_r(explode(',',$base_img));die;
        file_put_contents($path, base64_decode(explode(',',$base_img)[0]));
        $domain = $_SERVER["HTTP_HOST"];
        if($this->checkSSL($domain))
        {
            $domain = 'https://'.$domain;
        }
        else
        {
            $domain = 'http://'.$domain;
        }
        $user['avatar'] = $domain.'/uploads/20251020/'.$name;
        
        $this->success('查询成功',$user);
    }
    
    function base64_image_content($base64_image_content,$path){
    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        //var_dump($result);die;
        $type = $result[2];
        $new_file = $path."/".date('Ymd',time())."/";
        if(!file_exists($new_file)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0777);
        }
        $new_file = $new_file.time().".{$type}";
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            return '/'.$new_file;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @ApiParams (name="account", type="string", required=true, description="账号")
     * @ApiParams (name="password", type="string", required=true, description="密码")
     */
    public function login()
    {
        $account = $this->request->post('mobile');
        if (!$account ) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account);
        if ($ret) {
            $this->success(__('Logged in successful'),  $this->auth->getUserinfo());
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @ApiMethod (POST)
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function mobilelogin()
    {
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @ApiParams (name="username", type="string", required=true, description="用户名")
     * @ApiParams (name="password", type="string", required=true, description="密码")
     * @ApiParams (name="email", type="string", required=true, description="邮箱")
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="code", type="string", required=true, description="验证码")
     */
    public function register()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $email = $this->request->post('email');
        $mobile = $this->request->post('mobile');
        $code = $this->request->post('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @ApiMethod (POST)
     * @ApiParams (name="avatar", type="string", required=true, description="头像地址")
     * @ApiParams (name="username", type="string", required=true, description="用户名")
     * @ApiParams (name="nickname", type="string", required=true, description="昵称")
     * @ApiParams (name="bio", type="string", required=true, description="个人简介")
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        if ($nickname) {
            $exists = \app\common\model\User::where('nickname', $nickname)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Nickname already exists'));
            }
            $user->nickname = $nickname;
        }
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @ApiParams (name="email", type="string", required=true, description="邮箱")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @ApiMethod (POST)
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @ApiParams (name="platform", type="string", required=true, description="平台名称")
     * @ApiParams (name="code", type="string", required=true, description="Code码")
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->post("platform");
        $code = $this->request->post("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @ApiParams (name="mobile", type="string", required=true, description="手机号")
     * @ApiParams (name="newpassword", type="string", required=true, description="新密码")
     * @ApiParams (name="captcha", type="string", required=true, description="验证码")
     */
    public function resetpwd()
    {
        $type = $this->request->post("type", "mobile");
        $mobile = $this->request->post("mobile");
        $email = $this->request->post("email");
        $newpassword = $this->request->post("newpassword");
        $captcha = $this->request->post("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        //验证Token
        if (!Validate::make()->check(['newpassword' => $newpassword], ['newpassword' => 'require|regex:\S{6,30}'])) {
            $this->error(__('Password must be 6 to 30 characters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }
    
    function checkSSL($hostname) {
        $headers = get_headers("https://{$hostname}/", 1);
        return strpos($headers[0], '200') !== false;
    }
}
