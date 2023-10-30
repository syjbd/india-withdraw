<?php
/**
 * @desc Rule.php
 * @auhtor Wayne
 * @time 2023/10/30 14:51
 */
namespace dasher\withdraw\lib;

use dasher\withdraw\WithdrawException;

class Rule{

    protected $status = 0;
    protected $min = 0;
    protected $max = 0;
    protected $num = 0;
    protected $ratio = 0;

    protected $withdrawAmount = 0;
    protected $withdrawNum = 0;

    protected $registerTime = 0;
    protected $balance = 0;
    protected $identification = 1;

    public function __construct($status,$min,$max,$num,$ratio){
        $this->status   = $status;
        $this->min      = $min;
        $this->max      = $max;
        $this->num      = $num;
        $this->ratio    = $ratio;

    }

    public function setWithdrawData($withdrawAmount){
        $this->withdrawAmount = $withdrawAmount;
        return $this;
    }

    public function setUserAccount($balance,$registerTime,$identification){
        $this->balance          = $balance;
        $this->registerTime     = $registerTime;
        $this->identification   = $identification;
        return $this;
    }

    public function setWithdrawNum($withdrawNum){
        $this->withdrawNum = $withdrawNum;
        return $this;
    }

    /**
     * @throws WithdrawException
     */
    public function run(){
        //等级检测是否开启
        if($this->status == 0) return true;

        if($this->balance < $this->withdrawAmount)  throw new WithdrawException('withdraw.balance.enough');
        if(time() - $this->registerTime < $this->registerTime)  throw new WithdrawException('Account less than one hour old');
        if($this->min > 0 && $this->withdrawAmount < $this->min)   throw new WithdrawException('withdraw.amount.min');
        if($this->max > 0 && $this->withdrawAmount > $this->max)   throw new WithdrawException('withdraw.amount.max');
        if($this->num > 0 && $this->withdrawNum >= $this->num)  throw new WithdrawException('withdraw.num');
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