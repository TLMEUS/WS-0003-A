<?php
/**
 * This file contains the src/AppModels/DepartmentModel.php file for project WS-0003
 *
 * File Information:
 * Project Name: WS-0003
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: SectionModel.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 06/2024
 */
namespace App\Models;

use Framework\Model;
use Framework\Response;
use PDO;

/**
 * Class SectionModel
 *
 * This class represents a section model that extends the base Model class.
 *
 * @package App\Models
 */
class SectionModel extends Model {

    protected string $table = 'tbl_sections';

    /**
     * Updates a record in the database.
     *
     * @param array $data The data for the record to be updated.
     *                    The array should contain the following keys:
     *                    - colName: The new value for colName column.
     *                    - colId: The identifier value for colId column.
     *
     * @return bool|array Returns the fetched row as an associative array if the update is successful,
     *                   otherwise returns false.
     *                   The associative array will have the following keys:
     *                   - colName: The updated value for colName column.
     *                   - colId: The identifier value for colId column.
     *                   If the update fails, false will be returned.
     */
    public function updateRecord(array $data): bool|array {
        $conn = $this->database->getConnection();
        $sql = "UPDATE $this->table SET colName = :colName WHERE colId = :colId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":colName", value: $data["colName"]);
        $stmt->bindValue(param: ":colId", value: $data["colId"], type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }
}