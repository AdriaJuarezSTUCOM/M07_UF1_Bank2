<?php namespace ComBank\OverdraftStrategy;
      use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
      use ComBank\Exceptions\InvalidOverdraftFundsException;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

class NoOverdraft implements OverdraftInterface
{
    public function isGrantOverdraftFunds(float $amount): bool{
        return false;
    }
    public function getOverdraftFundsAmmount(): float{
        return 0.0;
    }
}
