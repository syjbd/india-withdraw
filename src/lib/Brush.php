<?php
/**
 * @desc Brush.php
 * @auhtor Wayne
 * @time 2023/10/30 14:33
 */
namespace dasher\withdraw\lib;

class Brush{

    protected $status;
    protected $multiple;
    protected $ratio;

    protected $userWallet;

    public function __construct($brushConfig,$userWallet)
    {
        $this->status       = $brushConfig['status'];
        $this->multiple     = $brushConfig['multiple'];
        $this->ratio        = $brushConfig['ratio'];
        $this->userWallet   = $userWallet;
    }

    public function run(){
        if($this->status == 0) return false;
        if($this->userWallet->getData('recharge_total') <= 0) return true;
        if($this->userWallet['order_total'] / $this->userWallet['recharge_total'] < $this->userWallet['multiple']) return true;
        return false;
    }

    public function getData(){
        return [
            'status'    => $this->status,
            'multiple'  => $this->multiple,
            'ratio'     => $this->ratio,
        ];
    }
}