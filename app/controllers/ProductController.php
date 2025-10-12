<?php

class ProductController
{
    public function __construct(private ProductGateway $gateway)
    {

    }

    public function processRequest(string $method, ?string $id):void
    {
        if ($id)
            $this->processResourceRequest($method, $id);
        else
            $this->processCollectionRequest($method);
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this -> gateway->get($id);

        if(!$product) {
            http_response_code(404); //not found
            echo json_encode(["message" => "Product not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($product);
                break;

            case "PATCH":
                $data = (array)json_decode(file_get_contents("php://input"), true); //if it's null, convert to empty array

                $errors = $this->getValidationErrors($data, false);

                if (!empty($errors)) {
                    http_response_code(422); //422 unprocessable entity
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($product, $data);
                
                echo json_encode([
                    "message" => "Product $id updated",
                    "rows" => $rows
                ]);
                break;

            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
            
            default:
                http_response_code(405); //405 method not allowed
                header("Allow: GET"); //indicate the allowed methods on this endpoint
                break;
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
            
            case "POST":
                //$data = $_POST; //data from the request body
                //data from the request body json format associative array
                $data = (array)json_decode(file_get_contents("php://input"), true); //if it's null, convert to empty array

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422); //422 unprocessable entity
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->gateway->create($data);
                
                http_response_code(201); //201 created
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
                break;
            
            default:
                http_response_code(405); //405 method not allowed
                header("Allow: GET, POST"); //indicate the allowed methods on this endpoint
                break;
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["name"])) {
            $errors[] = "Name is required";
        }

        if (isset($data["size"]) && !filter_var($data["size"], FILTER_VALIDATE_INT)) {
            $errors[] = "Size must be an integer";
        }

        if (isset($data["is_available"]) && !is_bool($data["is_available"])) {
            $errors[] = "Is Available must be a boolean";
        }

        return $errors;
    }
}