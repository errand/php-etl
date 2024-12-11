<?php
namespace Models;

class Manager extends BaseModel
{
    public function findOrCreate(string $name, int $agencyId): int
    {
        return $this->insertIfNotExists('manager', ['name' => $name, 'agency_id' => $agencyId], 'name');
    }
}