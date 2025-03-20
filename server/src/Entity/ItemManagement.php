<?php

namespace App\Entity;

interface ItemManagement 
{

    public function addItem(string $title, string $description, string $url) : Item;

    public function editItem(Item $item, string $title, string $description, string $url) : Item;

    public function removeItem(Item $item) : void;

}

?>