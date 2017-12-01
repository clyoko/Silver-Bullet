<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户账户表
 * @package App
 */
class Account extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'blade_account';

    protected $primaryKey = 'user_id';
}
