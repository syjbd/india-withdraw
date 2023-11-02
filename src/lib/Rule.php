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
    protected $minFee = 0;

    protected $withdrawAmount = 0;
    protected $withdrawNum = 0;

    protected $registerTime = 0;
    protected $balance = 0;
    protected $identification = 1;

    public function __construct($ruleConfig){
        $this->status   = $ruleConfig['status'];
        $this->min      = $ruleConfig['min'];
        $this->max      = $ruleConfig['max'];
        $this->num      = $ruleConfig['num'];
        $this->ratio    = $ruleConfig['ratio'];
        $this->minFee   = $ruleConfig['fee'];
    }

    public function setWithdrawData($withdrawAmount){
        $this->withdrawAmount = $withdrawAmount;
        return $this;
    }

    public function setUserAccount($userAccount){
        $this->balance          = $userAccount['balance'];
        $this->registerTime     = $userAccount['create_time'];
        $this->identification   = $userAccount['identification'];
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

        if($this->balance < $this->withdrawAmount)  throw new WithdrawException('Withdrawal balance is insufficient');
        if(time() - $this->registerTime < 3600)  throw new WithdrawException('Account less than one hour old');
        if($this->min > 0 && $this->withdrawAmount < $this->min)   throw new WithdrawException("Amount cannot be less than {$this->min}");
        if($this->max > 0 && $this->withdrawAmount > $this->max)   throw new WithdrawException("Amount cannot be more than {$this->max}");
        if($this->num > 0 && $this->withdrawNum >= $this->num)  throw new WithdrawException('You currently do not have the number of withdrawal');
        return true;
    }

    public function getData(){
        return [
            'status'    => $this->status,
            'min'       => $this->min,
            'max'       => $this->max,
            'num'       => $this->num,
            'ratio'     => $this->ratio,
            'fee'       => 0
        ];
    }
}