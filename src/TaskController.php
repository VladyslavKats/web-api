<?php
 
    class TaskController{

        public function __construct(private TaskGateway $gateway){

        }

        public function processRequest(string $method , ?string $id) : void {
            if($id === null){
                switch($method){
                    case "GET":
                        echo json_encode($this->gateway->GetAll());
                        break;
                    case "POST":
                        $data = (array)json_decode(file_get_contents("php://input"));
                        $errors = $this->getValidationErrors($data);
                        if(!empty($errors)){
                            $this->respondUnprocessableEntity($errors);
                            return;
                        }
                        $id = $this->gateway->create($data);
                        $this->respondCreated($id);
                        break;
                    
                        
                }
            }else{
                $data = $this->gateway->get($id);
                if($data === false){
                    $this->respondNotFound($id);
                    return;
                }else{
                    echo json_encode($data);
                }

                if($method == "PATCH"){
                    $data = (array)json_decode(file_get_contents("php://input"));
                    $errors = $this->getValidationErrors($data , false);
                    if(!empty($errors)){
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }
                    $rows = $this->gateway->update($id , $data);
                    echo json_encode(["message" => "Task updated" , "rows" => $rows]);
                }
                if($method == "DELETE"){
                    $rows = $this->gateway->delete($id);
                    echo json_encode(["message" => "Task deleted" , "rows" => $rows]);
                }
            }
        }

        private function respondMethodNotAllowed(string $alowwed_methods) : void{
            http_response_code(405);
            header("Allow: GET, POST");
        }

        private function respondNotFound(string $id){
            http_response_code(404);
            echo json_encode(["message" => "Task with ID $id not found"]);
        }

        private function respondCreated(string $id) : void {
            http_response_code(201);
            echo json_encode(["message" => "Task created" , "id" => $id]);
        }

        private function getValidationErrors(array $data , bool $is_new = true) : array{
            $errors = [];
            if($is_new && empty($data["name"])){
                $errors[] = "name is required";
            }
            if(!empty($data["priority"])){
                if(filter_var($data["priority"] , FILTER_VALIDATE_INT) === false){
                    $errors[] = "priority must be an integer";
                }
            }
            return $errors;
        }

        private function respondUnprocessableEntity(array $errors) : void {
            http_response_code(422);
            echo json_encode(["errors" => $errors]);
        }
    }


?>