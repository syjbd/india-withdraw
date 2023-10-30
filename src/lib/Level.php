<?php
/**
 * @desc Level.php
 * @auhtor Wayne
 * @time 2023/10/30 14:42
 */
namespace dasher\withdraw\lib;

use dasher\withdraw\WithdrawException;

class Level{

    protected $status = 0;
    protected $min = 0;
    protected $max = 0;
    protected $num = 0;
    protected $ratio = 0;
    protected $withdrawAmount = 0;
    protected $withdrawNum = 0;

    public function __construct($levelConfig, $withdrawAmount,$withdrawNum){
        $this->status   = $levelConfig['status'];
        $this->min      = $levelConfig['min'];
        $this->max      = $levelConfig['max'];
        $this->num      = $levelConfig['num'];
        $this->ratio    = $levelConfig['ratio'];
        $this->withdrawNum = $withdrawNum;
        $this->withdrawAmount = $withdrawAmount;
    }

    /**
     * @throws WithdrawException
     */
    public function run(){
        //等级检测是否开启
        if($this->status == 0) return false;
        //等级最小金额
        if($this->min > 0 && $this->withdrawAmount < $this->min){
            throw new WithdrawException('withdraw.level.amount.min');
        }
        //等级最大金额
        if($this->max > 0 && $this->withdrawAmount > $this->max){
            throw new WithdrawException('withdraw.level.amount.max');
        }
        //等级每日提现次数
        if($this->num > 0 && $this->withdrawNum >= $this->num){
            throw new WithdrawException('withdraw.level.num');
        }
        return true;
    }

    public function getData(){
        return [
            'status'    => $this->status,
            'min'       => $this->min,
            'max'       => $this->max,
            'num'       => $this->num,
            'ratio'     => $this->ratio,
            'fee'       => $this->withdrawAmount * $this->ratio
        ];
    }
}