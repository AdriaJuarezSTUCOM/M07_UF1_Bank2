<?php namespace ComBank\Bank;

      use ComBank\Support\Traits\APITrait;

class Person{
    use APITrait;
    private $name;
    private $idCard;
    private $email;

    public function __construct($email, $name = null, $idCard = null){
        $this->name = $name;
        $this->idCard = $idCard;
        $this->validateEmail($email)
        ? $this->email = $email
        : throw new \InvalidArgumentException("Error: invalid email address: $email");
    }

    public function getEmail(): string{
        return $this->email;
    }
}