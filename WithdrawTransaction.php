<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction  extends BaseTransaction implements BankTransactionInterface 
{
    private $amountWithdraw;

    public function __construct($amount){
        parent::validateAmount($amount);
        $this->amountWithdraw = $amount;
    }
    public function applyTransaction(BackAccountInterface $account) : float{
        $newBalance = $account->getBalance() - $this->amountWithdraw;

        if($newBalance < 0){
            if($account->getOverdraft()->getOverdraftFundsAmmount() == 0){
                throw new InvalidOverdraftFundsException("Insufficient balance to complete the withdrawal");
            }else{
                if(!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)){
                    throw new FailedTransactionException("Withdrawal exceeds overdraft limit");
                }
            }
        }
        $account->setBalance($newBalance);
        return $this->detectFraud($this)
            ? $account->getBalance()
            : throw new InvalidArgsException("ERROR: possible fraud was detected");
    }
    public function getTransactionInfo() : string{
        return "WITHDRAW_TRANSACTION";
    }
    public function getAmount() : float{
        return $this->amountWithdraw;
    }
}