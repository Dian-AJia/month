<?php
function checkcaptcha($code,$id=''){
    $captcha = new \think\captcha\Captcha();
    return $captcha->check($code,$id);
}