<?php

namespace app\common\model;

use think\Model;

class User extends Model
{
    //设置了模型的数据集返回类型
    protected $resultSetType = 'collection';
}
