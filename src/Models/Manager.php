<?php

namespace Models;

/**
 * Class Manager
 *
 * Модель для работы с таблицей "manager".
 * Предоставляет методы для поиска или создания записей.
 */
class Manager extends BaseModel
{
    /**
     * Название таблицы в базе данных.
     *
     * @var string
     */
    protected string $table = 'manager';

    /**
     * Находит запись по уникальному полю или создает новую, если она отсутствует.
     *
     * @param string $name Имя менеджера.
     * @return int Идентификатор найденной или созданной записи.
     */
    public function findOrCreate(string $name, int $agencyId): int
    {
        return $this->insertIfNotExists(['name' => $name, 'agency_id' => $agencyId], 'name');
    }
}