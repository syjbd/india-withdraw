<?php
/**
 * @desc Withdraw.php
 * @auhtor Wayne
 * @time 2023/10/30 14:33
 */
namespace dasher\withdraw;

class Withdraw{

    protected $userData;
    protected $withdrawData;
    protected $walletData;

    protected $ruleConfig;
    protected $levelConfig;
    protected $brushConfig;


    public function setUserData($userData){
        $this->userData = [
            'id'                => $userData['id'],
            'balance'           => $userData['balance'],
            'create_time'       => $userData['create_time'],
            'identification'    => $userData['identification'],
        ];
    }

    public function setWithdrawData($withdrawData){
        $this->withdrawData = [

        ];
    }

    public function setWalletData($type, $val, $name, $mobile, $ifsc, $email){

    }


    public function setLevelConfig($levelConfig){

    }

    public function setRuleConfig($ruleConfig){

    }

    public function setBrushConfig($brushConfig){

    }

    public function run(){

    }



}