<?php
/**
 * This file contains the src/App/Controllers/Sections.php class for project WS-0003
 *
 * File Information:
 * Project Name: WS-0003
 * Module Name: Source
 * Group Name: App
 * Section Name: Controllers
 * File Name: Sections.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 06/2024
 */
namespace App\Controllers;

use App\Database;
use App\Models\SectionModel;
use App\Models\ValidateModel;
use Framework\Controller;
use Framework\Response;

/**
 * Represents the Sections controller class.
 *
 * This class extends the base Controller class and is responsible for handling section-related operations.
 *
 * @package App\Controllers
 *
 * @noinspection PhpUnused
 */
class Sections extends Controller {

    private SectionModel $sectionModel;
    protected ValidateModel $validateModel;

    /**
     * Class constructor.
     *
     * Initializes a new instance of the class and sets up the database connection and section model.
     */
    public function __construct() {
        $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->sectionModel = new SectionModel($database);
        $this->validateModel = new ValidateModel($this->sectionModel);
    }

    /**
     * Retrieves all sections from the database and returns the response
     * containing the rendered view.
     *
     * @return Response The response containing the rendered view.
     *
     * @noinspection PhpUnused
     */
    public function index(): Response {
        $sections = $this->sectionModel->findAll();
        return $this->view(template: "Sections/index.tlmt", data: [
            'sections' => $sections
        ]);
    }

    /**
     * Create method.
     *
     * Returns a Response object with a rendered view for creating a new section.
     *
     * @return Response A Response object containing the rendered view.
     *
     * @noinspection PhpUnused
     */
    public function create(): Response {
        return $this->view(template: "Sections/create.tlmt");
    }

    /**
     * Creates a new section and inserts it into the database.
     *
     * Validates the post data submitted, inserts the data into the section model,
     * and returns a response based on the success or failure of the insertion.
     *
     * @return Response The response indicating the success or failure of the section creation.
     *
     * @noinspection PhpUnused
     */
    public function createSection(): Response
    {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method not allowed",
                "message" => "Method not allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if (!$this->sectionModel->insert($_POST)) {
                $this->view(template: "error.tlmt", data: [
                    "title" => "Section Entry Error",
                    "message" => "Unable to save section",
                    "code" => "500"
                ]);
            }
        } else {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Section Entry Error",
                "message" => "Invalid Information Entered",
                "errorcode" => "406"
            ]);
        }
        return $this->index();
    }

    /**
     * Update method.
     *
     * Retrieves the section entry with the given ID and returns a response based on its existence.
     *
     * @param string $id The ID of the section to update.
     *
     * @return Response A response object based on whether the section entry was found or not.
     *
     * @noinspection PhpUnused
     */
    public function update(string $id): Response {
        $old_dep = $this->sectionModel->find($id);
        if(!$old_dep) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Section Entry Error",
                "message" => "Record not found",
                "errorcode" => "404"
            ]);
        } else {
            return $this->view(template: 'Sections/update.tlmt', data: [
                "colId" => $old_dep['colId'],
                "colName" => $old_dep['colName']
            ]);
        }
    }

    /**
     * Updates a section.
     *
     * This method updates a section based on the validated POST data. It performs the following steps:
     * 1. Validates the POST data using the validateModel's validatePost method.
     * 2. If the POST data is not valid, it returns an error view with a method not allowed error message.
     * 3. If the POST data is valid, it validates the data using the validateModel's validateData method.
     * 4. If the data is valid, it updates the section record using the sectionModel's updateRecord method.
     * 5. If the record is not updated successfully, it returns an error view with a save error message.
     * 6. Finally, it returns the index view.
     *
     * @return Response The index view or error view based on the condition.
     *
     * @noinspection PhpUnused
     */
    public function updateSection(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST)) {
            if($this->sectionModel->updateRecord($_POST)) {
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
     * Deletes a record based on the given ID.
     *
     * Retrieves the old record based on the provided ID from the section model.
     * If the record does not exist, a blank template with an error message is returned.
     * Otherwise, the ID and name of the record are passed to the delete template.
     *
     * @param string $id The ID of the record to delete.
     *
     * @return Response The response containing the appropriate template and data.
     *
     * @noinspection PhpUnused
     */
    public function delete(string $id): Response {
        $oldDep = $this->sectionModel->find(id: $id);
        if(!$oldDep) {
            return $this->view(template: "blank.tlmt", data: [
                "title" => "Missing record",
                "message" => "Unable to locate record",
                "errorcode" => "404"
            ]);
        }
        $colId = $oldDep['colId'];
        $colName = $oldDep['colName'];
        return $this->view(template: 'Sections/delete.tlmt', data: [
            "colId" => $colId,
            "colName" => $colName
        ]);
    }

    /**
     * Deletes a section.
     *
     * This method validates the request method using the validatePost() method of the validateModel.
     * If the request method is not valid, it returns an error view.
     *
     * It then validates the data using the validateData() method of the validateModel with the "delete" operation.
     * If the data is valid, it calls the delete() method of the sectionModel with the "colId" value from the $_POST array.
     * If the deletion is successful, it returns the result of the index() method.
     *
     * @return Response The response of the index() method if the section is deleted successfully,
     *         an error view response if the request method is not valid or the deletion fails.
     *
     * @noinspection PhpUnused
     */
    public function deleteSection(): Response {
        if (!$this->validateModel->validatePost()) {
            return $this->view(template: "error.tlmt", data: [
                "title" => "Method Not Allowed",
                "message" => "Method Not Allowed",
                "errorcode" => "405"
            ]);
        }
        if ($this->validateModel->validateData($_POST, op: "delete")) {
            if(!$this->sectionModel->delete($_POST["colId"])) {
                return $this->view(template: "error.tlmt", data: [
                    "title" => "Deletion Error",
                    "message" => "Unable to delete section",
                    "errorcode" => "500"
                ]);
            }
        }
        return $this->index();
    }
}