<?php
/**
 * @desc withdrawData.php
 * @auhtor Wayne
 * @time 2023/10/30 15:23
 */
namespace dasher\withdraw\lib;



use dasher\withdraw\WithdrawException;

class PostData{

    protected $accountName;
    protected $mobile;
    protected $email;
    protected $accountVal;
    protected $amount;
    protected $ifsc;

    public function __construct($postData){
        $this->accountName  = $postData['account_name'];
        $this->mobile       = $postData['mobile'];
        $this->email        = $postData['email'];
        $this->accountVal   = $postData['account_val'];
        $this->amount       = $postData['amount'];
        $this->ifsc         = $postData['ifsc'];
    }

    /**
     * @throws WithdrawException
     */
    public function checkEmpty(){
        if(!$this->mobile ) throw new WithdrawException('The parameter "mobile" cannot be empty');
        if(!$this->email)  throw new WithdrawException('The parameter "email" cannot be empty');
        if(!$this->ifsc) throw new WithdrawException('The parameter "ifsc" cannot be empty');
        if(!$this->accountName) throw new WithdrawException('The parameter "name" cannot be empty');
        if(!$this->accountVal) throw new WithdrawException('The parameter "account" cannot be empty');
        return $this;
    }

    /**
     * @throws WithdrawException
     */
    public function checkFormat($checkField){
        if(in_array('ifsc', $checkField)){
            if(!preg_match('/^[a-zA-Z0-9]{11}$/', $this->ifsc)) throw new WithdrawException('The parameter "ifsc" error');
        }
        if(in_array('mobile', $checkField)){
            if(!preg_match('/^[6789]\d{9}$/', $this->mobile)) throw new WithdrawException('The parameter "mobile" error');
        }
        if(in_array('accountVal', $checkField)){
            if(!preg_match('/^\d+$/', $this->accountVal)) throw new WithdrawException('The parameter "account" error');
        }
        if(in_array('email', $checkField)){
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) throw new WithdrawException('The parameter "email" error');
        }
        return $this;
    }
}