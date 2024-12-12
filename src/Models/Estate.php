<?php

namespace Models;

/**
 * Class Estate
 * Работа с сущностью недвижимости.
 *
 * @package Models
 */
class Estate extends BaseModel
{
    /**
     * @var string
     * Название таблицы в базе данных.
     */
    protected string $table = 'estate';

    /**
     * Обновить существующую запись или создать новую, если не существует.
     *
     * @param array $data Данные для обновления или создания.
     * @param string|null $id ID существующей записи, если она есть.
     *
     * @return int ID обновленной или созданной записи.
     */
    public function updateOrCreate(array $data, string $id = null): int
    {
        // Логирование начала операции.
        $this->logger->log("Updating or creating Estate");
        $this->logger->log("Data: " . json_encode($data));

        // Если передан ID, ищем существующую запись.
        if ($id) {
            $existing = $this->findByField('id', $id);

            // Если запись существует, проверяем изменения.
            if ($existing) {
                $changes = array_diff_assoc($data, $existing);

                // Если есть изменения, обновляем запись.
                if (!empty($changes)) {
                    $this->update($data, $id);
                    $this->logger->log("Updated record ID $id: " . json_encode($changes));
                } else {
                    $this->logger->log("No changes for record ID $id.");
                }

                return $id;
            }
        }

        // Если записи нет, создаем новую.
        $newId = $this->create($data);
        $this->logger->log("Created new record ID $newId: " . json_encode($data));
        return $newId;
    }
}
