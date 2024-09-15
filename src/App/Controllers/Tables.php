<?php
/**
 * This file contains the src/App/Controllers/Tables.php class for project WS-0003
 *
 * File Information:
 * Project Name: WS-0003
 * Section Name: Source
 * Module Name: App
 * Category Name: Controllers
 * File Name: Tables.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 06/22/2024
 */
namespace App\Controllers;

use App\Database;
use App\Models\TableModel;
use App\Models\SectionModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * Class Tables
 *
 * This class is responsible for managing tables in the application.
 * It extends the base Controller class.
 *
 * @package App\Controllers
 *
 * @noinspection PhpUnused
 */
class Tables extends Controller {

    private SectionModel $sectionModel;
    private TableModel $tableModel;
    protected ValidateModel $validateModel;

    /**
     * Initializes the object by creating a new Database instance,
     * a TableModel instance, and a ValidateModel instance.
     *
     * The Database instance is created using the provided environment variables: DB_HOST,
     * DB_NAME, DB_USER, and DB_PASSWORD.
     *
     * @return void
     *
     * @noinspection PhpUnused
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->tableModel = new TableModel($database);
        $this->sectionModel = new SectionModel($database);
        $this->validateModel = new ValidateModel($this->tableModel);
    }

    /**
     * Retrieves all tables and sections from the models and returns a Response object
     * with the template "Tables/index.tlmt" and the tables and sections data.
     *
     * @return Response The response object with the template and data.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $tables = $this->tableModel->findAll();
        $sections = $this->sectionModel->findAll();
        $scripts =  ['sortTable'];
        return $this->view(template: "Tables/index.tlmt", data: [
            'tables' => $tables,
            'sections' => $sections,
            'scripts' => $scripts
        ]);
    }

    /**
     * Creates a new instance of the Request class and returns a Response object.
     *
     * The template parameter specifies the path to the template file for rendering the view.
     *
     * The data parameter is an associative array that contains the data to be passed to the view.
     * In this case, it includes the 'sections' key with the value obtained by calling the findAll()
     * method of the sectionModel object.
     *
     * @return Response The response object containing the rendered view.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        return $this->view(template: "Tables/create.tlmt", data: [
            'sections' => $this->sectionModel->findAll()
        ]);
    }

    /**
     * Creates a table based on the POST data, if the data is valid.
     *
     * If the POST data is not valid, an error response is returned.
     * If the table insertion fails, an error response is returned.
     * If the table is successfully created, the index page is returned.
     *
     * @return Response The response object that represents the result of creating the table.
     *
     * @noinspection PhpUnused
     */
    public function createTable(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method not allowed",
                "message" => "Method not allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if (!$this->tableModel->insert($_POST)) {
                $this->view(template: "error.tlmt", data: [
                    "title" => "Table Entry Error",
                    "message" => "Unable to save table",
                    "code" => "500"
                ]);
            }
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Table Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }

    /**
     * Updates a table entry identified by the given ID.
     *
     * Retrieves the old table entry from the TableModel based on the provided ID.
     * If the table entry is not found, returns a Response object rendered with the error.tlmt template,
     * including the title, message, and error code.
     * Otherwise, returns a Response object rendered with the update.tlmt template,
     * including the column ID, column name, and old section of the old table entry.
     *
     * @param string $id The ID of the table entry to be updated.
     * @return Response The Response object rendered with the appropriate template and data.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $oldTable = $this->tableModel->find(id: $id);
        $sections = $this->sectionModel->findAll();
        if(!$oldTable) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Table Entry Error",
                "message" => "Table not found",
                "errorcode" => "404"
            ]);
        } else {
            return $this->view(template: "Tables/update.tlmt", data: [
                "colId" => $oldTable['colId'],
                "colName" => $oldTable['colName'],
                "oldSection" => $oldTable['colSection'],
                "colSeats" => $oldTable['colSeats'],
                "sections" => $sections
            ]);
        }
    }

    /**
     * Updates the table with the new data.
     *
     * The method first validates the POST request using the ValidateModel's validatePost() method.
     * If the validation fails, it returns a Response object with an error template and appropriate data.
     *
     * If the validation is successful, it further validates the data using the ValidateModel's validateData() method
     * with the "update" operation. If the data validation fails, it returns a Response object with an error template and
     * appropriate data.
     *
     * If the data validation is successful, it calls the TableModel's updateRecord() method to update the table using
     * the data from the POST request. If the update is successful, it returns a Response object with an error template and
     * appropriate data.
     *
     * If all the above conditions fail, it calls the index() method to return the default view.
     *
     * @return Response The updated table view or an error view.
     *
     * @noinspection PhpUnused
     */
    public function updateTable(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "update")) {
            if($this->tableModel->updateRecord($_POST)) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Save Error",
                    "message" => "Unable to update department",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }

    /**
     * Deletes a record from the table.
     *
     * Retrieves the record with the given ID from the table using the TableModel's find
     * method. If the record is not found, a response is returned with the template "blank.tlmt",
     * along with the data containing the title, message, and error code. Otherwise, the "colId"
     * and "colName" data are passed to the "Tables/delete.tlmt" template for further processing.
     *
     * @param string $id The ID of the record to be deleted.
     *
     * @return Response The response object containing the view template and data.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $oldDep = $this->tableModel->find(id: $id);
        if(!$oldDep) {
            return $this->view(template: "blank.tlmt", data: [
                "title" => "Missing record",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $colId = $oldDep['colId'];
        $colName = $oldDep['colName'];
        return $this->view(template: 'Tables/delete.tlmt', data: [
            "colId" => $colId,
            "colName" => $colName
        ]);
    }

    /**
     * Deletes a table based on the given POST data.
     *
     * This method first validates the POST data using the validatePost() method
     * from the ValidateModel. If the validation fails, it returns a Response object
     * with an error template and the appropriate error message.
     *
     * If the validation succeeds, it then calls the validateData() method from the
     * ValidateModel, passing the $_POST data and the "delete" operation as arguments.
     * If the validation fails, it returns a Response object with an error template and
     * the appropriate error message.
     *
     * If the validation passes, it calls the delete() method from the TableModel,
     * passing the "colId" value from the $_POST data as an argument. If the deletion
     * fails, it returns a Response object with an error template and the appropriate
     * error message.
     *
     * If the deletion is successful or no deletion is required, it calls the index()
     * method and returns the result.
     *
     * @return Response The response object representing the result of the delete operation.
     *
     * @noinspection PhpUnused
     */
    public function deleteTable(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "delete")) {
            if(!$this->tableModel->delete($_POST["colId"])) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Deletion Error",
                    "message" => "Unable to delete table",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }
}