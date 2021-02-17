<?php


namespace App\Messenger;


class ArrayMessage
{
    public function __construct(private string $id, private array $data){}

    public function getId() {
        return $this->id;
    }

    public function getData() {
        return $this->data;
    }
}
