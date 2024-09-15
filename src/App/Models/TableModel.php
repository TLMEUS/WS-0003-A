<?php
/**
 * This file contains the src/AppModels/TableModel.php file for project WS-0003
 *
 * File Information:
 * Project Name: WS-0003
 * Module Name: Source
 * Group Name: App
 * Section Name: Models
 * File Name: TableModel.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 06/2024
 */
namespace App\Models;

use Framework\Model;
use Framework\Response;
use PDO;

class TableModel extends Model {

    protected string $table = 'tbl_tables';

    public function updateRecord(array $data): bool|array {
        $conn = $this->database->getConnection();
        $sql = "UPDATE $this->table SET colName = :colName, colSection = :colSection, colSeats = :colSeats WHERE colId = :colId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(param: ":colName", value: $data["colName"]);
        $stmt->bindValue(param: ":colSection", value: $data["colSection"]);
        $stmt->bindValue(param: ":colSeats", value: $data["colSeats"]);
        $stmt->bindValue(param: ":colId", value: $data["colId"], type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }
}