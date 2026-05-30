<?php
class LocationModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ Tỉnh/Thành phố
    public function getAllProvinces() {
        $query = "SELECT id, name FROM provinces ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy Quận/Huyện dựa vào ID Tỉnh
    public function getDistrictsByProvince($province_id) {
        $query = "SELECT id, name FROM districts WHERE province_id = :pid ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':pid' => $province_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy Phường/Xã dựa vào ID Huyện
    public function getWardsByDistrict($district_id) {
        $query = "SELECT id, name FROM wards WHERE district_id = :did ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':did' => $district_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>