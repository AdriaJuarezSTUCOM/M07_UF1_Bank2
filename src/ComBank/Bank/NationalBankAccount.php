<?php namespace ComBank\Bank;

use ComBank\Bank\BankAccount;

class NationalBankAccount extends BankAccount{
    public function __construct($balance = 100, Person $personHolder = null){
        parent::__construct($balance, $personHolder);
        $this->currency = "€ (Euros)";
    }
}