<?php
/**
 * @desc Withdraw.php
 * @auhtor Wayne
 * @time 2023/10/30 14:33
 */
namespace dasher\withdraw;

use dasher\withdraw\lib\Brush;
use dasher\withdraw\lib\Level;
use dasher\withdraw\lib\PostData;
use dasher\withdraw\lib\Rule;

class Withdraw{

    protected $userData;
    protected $withdrawData;
    protected $walletData;

    protected $ruleConfig;
    protected $levelConfig;
    protected $brushConfig;

    protected $checkFormat = ['ifsc','mobile','accountVal','email'];

    public function setCheckFormat($checkFormat){
        $this->checkFormat = $checkFormat;
        return $this;
    }

    public function setUserData($userData){
        $this->userData = [
            'id'                => $userData['id'],
            'balance'           => $userData['balance'],
            'create_time'       => $userData['create_time'],
            'identification'    => $userData['identification'],
        ];
        return $this;
    }

    public function setWithdrawData($withdrawData){
        $this->withdrawData = [
            'account_name'  => $withdrawData['account_name'],
            'mobile'        => $withdrawData['mobile'],
            'email'         => $withdrawData['email'],
            'account_val'   => $withdrawData['account_val'],
            'amount'        => $withdrawData['amount'],
            'ifsc'          => $withdrawData['ifsc'],
        ];
        return $this;
    }

    public function setWalletData($wallet){
        $this->walletData = [
            'user_id'           => $wallet['user_id'],
            'order_total'       => $wallet['order_total'],
            'recharge_total'    => $wallet['recharge_total'],
            'withdraw_times'    => $wallet['withdraw_times'],
            'recharge_times'    => $wallet['recharge_times'],
            'free_amount'       => $wallet['free_amount']
        ];
        return $this;
    }


    public function setLevelConfig($levelConfig){
        $this->levelConfig = [
            'status'    => $levelConfig['status'],
            'min'       => $levelConfig['min'],
            'max'       => $levelConfig['max'],
            'num'       => $levelConfig['num'],
            'ratio'     => $levelConfig['ratio'],
        ];
        return $this;
    }

    public function setRuleConfig($ruleConfig){
        $this->ruleConfig = [
            'status'    => $ruleConfig['status'],
            'min'       => $ruleConfig['min'],
            'max'       => $ruleConfig['max'],
            'ratio'     => $ruleConfig['ratio'],
            'fee'       => $ruleConfig['fee'],
            'num'       => $ruleConfig['num']
        ];
        return $this;
    }

    public function setBrushConfig($brushConfig){
        $this->brushConfig = [
            'status'    => $brushConfig['status'],
            'multiple'  => $brushConfig['multiple'],
            'ratio'     => $brushConfig['ratio'],
        ];
        return $this;
    }


    public function run($withdrawNum){
        $result = [
            'amount'                => $this->withdrawData['amount'],
            'fee'                   => 0,
            'min_withdraw_fee'      => 0,
            'withdraw_fee_ratio'    => 0,
            'min_withdraw_amount'   => 0,
            'can_get_amount'        => 0,
            'recharge_total'        => 0,
            'order_amount'          => $this->walletData['order_total'],
            'error'                 => "",
            'status'                => true,
            'launder_status'        => false,
            'free_fee_amount'       => min($this->walletData['free_amount'],$this->withdrawData['amount'])
        ];
        try {
            $brushObj = new Brush($this->brushConfig, $this->walletData);
            $brushBool = $brushObj->run();
            if($brushBool){
                $result['launder_status'] = true;
                $result['fee'] = $this->withdrawData['amount'] * $this->brushConfig['ratio'];
                $result['can_get_amount'] = $this->withdrawData['amount'] - $result['fee'];
                $result['withdraw_fee_ratio'] = $this->brushConfig['ratio'];
                return $result;
            }

            $postObj = new PostData($this->withdrawData);
            $postObj->checkEmpty()->checkFormat($this->checkFormat);

            $levelObj = new Level($this->levelConfig,$this->withdrawData['amount'], $withdrawNum);
            $levelBool = $levelObj->run();
            $levelResult = $levelObj->getData();

            $ruleObj = new Rule($this->ruleConfig);
            $ruleObj->setUserAccount($this->userData)->setWithdrawData($this->withdrawData['amount'])->setWithdrawNum($withdrawNum);
            $ruleObj->run();
            $ruleResult = $ruleObj->getData();

            $levelFee = 0;
            $fee = 0;
            $feeRatio = 0;
            if($levelBool && $levelResult['ratio'] > 0){
                $levelFee = ($this->withdrawData['amount']-$result['free_fee_amount']) * $levelResult['ratio'];
                $feeRatio = $levelResult['ratio'];
            }
            $ruleFee = max(($this->withdrawData['amount']-$result['free_fee_amount']) * $ruleResult['ratio'], $ruleResult['fee']);
            if($ruleFee > $levelFee){
                $fee = $ruleFee;
                $feeRatio = $ruleResult['ratio'];
            }
            $result['withdraw_fee_ratio'] = $feeRatio;
            $result['fee'] = $fee;
            $result['can_get_amount'] = $this->withdrawData['amount'] - $result['fee'];
            return $result;
        }catch (WithdrawException $e){
            $result['error'] = $e->getMessage();
            $result['status'] = false;
            return $result;
        }catch (\Exception $e){
            $result['status'] = false;
            $result['error'] = $e->getMessage();
            return $result;
        }

    }

    public function amount($withdrawNum,$amount): array
    {
        $result = [
            'amount'                => $amount,
            'fee'                   => 0,
            'min_withdraw_fee'      => 0,
            'withdraw_fee_ratio'    => 0,
            'min_withdraw_amount'   => 0,
            'can_get_amount'        => 0,
            'recharge_total'        => 0,
            'order_amount'          => $this->walletData['order_total'],
            'error'                 => "",
            'status'                => true,
            'launder_status'        => false,
            'free_fee_amount'       => min($this->walletData['free_amount'],$amount)
        ];
        try {
            $brushObj = new Brush($this->brushConfig, $this->walletData);
            $brushBool = $brushObj->run();
            if($brushBool){
                $result['launder_status'] = true;
                $result['fee'] = $amount * $this->brushConfig['ratio'];
                $result['can_get_amount'] = $amount - $result['fee'];
                $result['withdraw_fee_ratio'] = $this->brushConfig['ratio'];
                return $result;
            }

            $levelObj = new Level($this->levelConfig,$amount, $withdrawNum);
            $levelBool = $levelObj->run();
            $levelResult = $levelObj->getData();

            $ruleObj = new Rule($this->ruleConfig);
            $ruleObj->setUserAccount($this->userData)->setWithdrawData($amount)->setWithdrawNum($withdrawNum);
            $ruleObj->run();
            $ruleResult = $ruleObj->getData();

            $levelFee = 0;
            $fee = 0;
            $feeRatio = 0;
            if($levelBool && $levelResult['ratio'] > 0){
                $levelFee = ($amount-$result['free_fee_amount']) * $levelResult['ratio'];
                $feeRatio = $levelResult['ratio'];
            }
            $ruleFee = max(($amount-$result['free_fee_amount']) * $ruleResult['ratio'], $ruleResult['fee']);
            if($ruleFee > $levelFee){
                $fee = $ruleFee;
                $feeRatio = $ruleResult['ratio'];
            }
            $result['withdraw_fee_ratio'] = $feeRatio;
            $result['fee'] = $fee;
            $result['can_get_amount'] = $amount - $result['fee'];
            return $result;
        }catch (WithdrawException $e){
            $result['error'] = $e->getMessage();
            $result['status'] = false;
            return $result;
        }
    }

}