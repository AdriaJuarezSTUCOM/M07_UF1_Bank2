<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Support\Traits\APITrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use PHPUnit\TextUI\XmlConfiguration\ValidationResult;
use ComBank\Bank\InternationalBankAccount;

use function PHPUnit\Framework\throwException;

class BankAccount implements BackAccountInterface
{
    protected $personHolder;
    protected $balance;
    protected $status;
    protected $overdraft;
    protected $currency;

    use APITrait;

    public function __construct($balance = 100, Person $personHolder = null) {

        
        $this->personHolder = $personHolder;
        $this->balance = $balance;
        $this->status = BackAccountInterface::STATUS_OPEN;
        $this->overdraft = new NoOverdraft;
        
    }

    public function transaction(BankTransactionInterface $transaction):void{
        $this->status==BackAccountInterface::STATUS_CLOSED
            ? throw new BankAccountException("Cuenta cerrada")
            : $transaction->applyTransaction($this);
    }

    public function openAccount($ip = null) : bool{
        return $this->validateLocation($ip)
        ? $this->status == BackAccountInterface::STATUS_OPEN
        : throw new InvalidArgsException("ERROR: you have to be in Barcelona to open an account");
    }
    public function reopenAccount() : void{
        if($this->status == BackAccountInterface::STATUS_OPEN){
            throw new BankAccountException("Account is already open");
        }else{
            $this->status = BackAccountInterface::STATUS_OPEN;
        }
    }
    public function closeAccount() : void{
        if($this->status == BackAccountInterface::STATUS_CLOSED){
            throw new BankAccountException("Error: Account is already closed");
        }
        else{
            $this->status = BackAccountInterface::STATUS_CLOSED;
        }
    }
    public function getBalance() : float{
        return $this->balance;
    }
    public function getOverdraft() : OverdraftInterface{
        return $this->overdraft;
    }
    public function getCurrency() : string{
        return ($this->balance . " " . $this->currency);
    }
    public function applyOverdraft(OverdraftInterface $overdraft) : void{
        $this->overdraft=$overdraft;
    }
    public function setBalance(float $balance) : void{
        $this->balance = $balance;
    }
} 