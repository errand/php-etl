<?php

namespace Models;

/**
 * Class Contact
 * Работа с сущностью контактов.
 *
 * @package Models
 */
class Contact extends BaseModel
{
    /**
     * @var string
     * Название таблицы в базе данных.
     */
    protected string $table = 'contacts';

    /**
     * Найти контакт по имени или создать новый, если не найден.
     *
     * @param string $name Имя контакта.
     * @param string $phones Номера телефонов.
     *
     * @return int ID найденного или созданного контакта.
     */
    public function findOrCreate(string $name, string $phones): int
    {
        // Ищем контакт по имени.
        $contact = $this->findByField('name', $name);

        // Если контакт найден, возвращаем его ID.
        if ($contact) {
            return $contact['id'];
        }

        // Если контакт не найден, создаем новый и возвращаем его ID.
        return $this->create(['name' => $name, 'phones' => $phones]);
    }
}
