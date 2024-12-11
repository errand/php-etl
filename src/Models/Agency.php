<?php
namespace Models;

class Agency extends BaseModel
{
    public function findOrCreate(string $name): int
    {
        return $this->insertIfNotExists('agency', ['name' => $name], 'name');
    }
}