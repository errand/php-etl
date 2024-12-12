<?php

namespace Models;

/**
 * Class Agency
 *
 * Модель для работы с таблицей "agency".
 * Предоставляет методы для поиска или создания записей.
 */
class Agency extends BaseModel
{
    /**
     * Название таблицы в базе данных.
     *
     * @var string
     */
    protected string $table = 'agency';

    /**
     * Находит запись по уникальному полю или создает новую, если она отсутствует.
     *
     * @param string $name Название агентства.
     * @return int Идентификатор найденной или созданной записи.
     */
    public function findOrCreate(string $name): int
    {
        return $this->insertIfNotExists(['name' => $name], 'name');
    }
}
