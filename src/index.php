<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\Person;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    $bankAccount1 = new BankAccount(400.0);
    // show balance account
    pl("My balance : " . $bankAccount1->getBalance());
    // close account
    $bankAccount1->closeAccount();
    pl("My account is now closed");
    // reopen account
    $bankAccount1->reopenAccount();
    pl("My account is now reopened");

    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction deposit (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());

} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());

$bankAccount1->closeAccount();
pl("My account is now closed");


//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    $bankAccount2 = new BankAccount(200.0);
    $bankAccount2->applyOverdraft(new SilverOverdraft());
    // show balance account
    pl("My balance : " . $bankAccount2->getBalance());
    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100.0));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300.0));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50.0));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120.0));
    
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
} catch (ZeroAmountException $e){
    pl(''. $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20.0));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

try {
    $bankAccount2->closeAccount();
    pl("My account is now closed");
    $bankAccount2->closeAccount();
} catch (BankAccountException $e) {
    pl($e->getMessage());
}


//---[Start testing national account (No conversion)]---/
pl('--------- [Start testing national account (No conversion)] --------');
$nationalBankAccount = new NationalBankAccount(500.0);
pl("My balance: " . $nationalBankAccount->getCurrency());

//---[Start testing international account (Dollar conversion)]---/
pl('--------- [Start testing international account (Dollar conversion)] --------');
$internationalBankAccount = new InternationalBankAccount(300.0);
pl("My balance: " . $internationalBankAccount->getCurrency());
pl("Converting balance to Dollars (Rate: 1 USD = 1.10 €)");
pl("Converted balance: " . $internationalBankAccount->getConvertedCurrency());

//---[Start testing good mail]---/
pl('--------- [Start testing good mail] --------');
try{
$personaCorreoBueno = new Person("ejemplo@correo.com");
pl("Validating email: ". $personaCorreoBueno->getEmail());
pl("Email is valid");

//---[Start testing bad mail]---/
pl('--------- [Start testing bad mail] --------');
$personaCorreoMalo = new Person("dihfkdsnfdsfn.es");
pl("Validating email: ". $personaCorreoMalo->getEmail());
}catch(InvalidArgumentException $e){
    pl($e->getMessage());
}

//---------------[Start testing not fraud]---------------------/
pl('--------- [Start testing not fraud] --------');
try{
pl('Doing transaction deposit (+3000)');
$cuentaNoFraudulenta = new BankAccount(500);
$cuentaNoFraudulenta->transaction(new DepositTransaction(3000));
pl('Transaction successful');

//---------------[Start testing fraud]---------------------/
pl('--------- [Start testing fraud] --------');
pl('Doing transaction deposit (+59000)');
$cuentaFraudulenta = new BankAccount(500);
$cuentaFraudulenta->transaction(new DepositTransaction(59000));
}catch(InvalidArgsException $e){
    pl($e->getMessage());
}

//---------------[Start testing ubication in Barcelona]------------/
try{
pl('--------- [Start testing ubication in Barcelona] --------');
$cuentaFueraBarcelona = new BankAccount(500);
pl('Opening account with ip: 84.88.0.19 (in Barcelona)');
$cuentaFueraBarcelona->openAccount("84.88.0.19");//HAY QUE AÑADIR UNA IP AQUI PARA PODER HACER EL TEST, EN LOCALHOST DARA ERROR

//---------------[Start testing ubication out of Barcelona]------------/
pl('--------- [Start testing ubication out of Barcelona] --------');
$cuentaBarcelona = new BankAccount(500);
pl('Opening account with ip: 103.109.244.212 (not in Barcelona)');
$cuentaBarcelona->openAccount("103.109.244.212");//HAY QUE AÑADIR UNA IP AQUI PARA PODER HACER EL TEST, EN LOCALHOST DARA ERROR
}catch(InvalidArgsException $e){
    pl($e->getMessage());
}
