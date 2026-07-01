<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Headers: Content-Type');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    exit(0);
}

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/CategoryModel.php';

$controller = new ApiCategoryController();
$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            $data = $_POST;
        }

class ApiCategoryController{
    private $CategoryModel;

    public function __construct(){
        $this ->CategoryModel = new CategoryModel();
    }

    //Lấy tất cả danh mục
    public function getList() {
        $data = $this ->CategoryModel->findAll('id DESC');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }    

    //Lấy 1 danh mục
    public function getOne($id){
        $data = $this ->CategoryModel->getCategory($id);

        if($data){
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'data' => "Không tìm thấy danh mục"
            ]);
        }
    }

    //Thêm danh mục
    public function add($data){
        $name = $data['name'] ?? '';

        if (empty($name)){
            echo json_encode([
                'success' => false,
                'message' => "Vui lòng nhập tên danh mục",
            ]);
            return;
        }

         $result = $this->CategoryModel->addCategory($name);

        echo json_encode([
            'success' => $result ? true : false
        ]);
    }

    // Sửa Danh mục
    public function update($data){
        $id = $data['id'] ?? 0;
        $name = $data['name'] ?? '';

        if (empty($id) || empty($name)){
            echo json_encode([
                'success' => false,
                'message' => "Vui lòng nhập đầy đủ thông tin"
            ]);
            return;
        } 

        $result = $this->CategoryModel->updateCategory(
            $data['id'],
            $data['name'] 
        );

        echo json_encode([
            'success' => $result ? true : false
        ]);
    }
    // Xóa Danh Mục
    public function delete($data){
        $id = $data['id'] ?? 0;

        if (empty($id)){
            echo json_encode([
                'success' => false,
                'message' => "Vui lòng nhập id danh mục cần xóa"
            ]);
            return;
        }

        $result = $this->CategoryModel->deleteCategory(
            $data['id']
        );

        echo json_encode([
            'success' => $result ? true : false
        ]);
    }}


switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $controller->getOne($_GET['id']);
        } else {
            $controller->getList();
        }
        break;

    case 'POST':
        $controller->add($data);
        break;

    case 'PUT':
        $controller->update($data);
        break;

    case 'DELETE':
        $controller->delete($data);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Method không hợp lệ'
        ]);
        break;
}
?>