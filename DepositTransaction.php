<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface 
{
    private $amountDeposit;

    public function __construct($amount){
        parent::validateAmount($amount);
        $this->amountDeposit = $amount;
    }
    public function applyTransaction(BackAccountInterface $account) : float{
            $newBalance = $account->getBalance() + $this->amountDeposit;
            $account->setBalance($newBalance);
            return $this->detectFraud($this)
            ? $newBalance
            : throw new InvalidArgsException("ERROR: possible fraud was detected");
    }
    public function getTransactionInfo() : string{
        return "DEPOSIT_TRANSACTION";
    }
    public function getAmount() : float{
        return $this->amountDeposit;
    }
}
