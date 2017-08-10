<?php

/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'include/DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    public $domain = 'http://192.168.1.5';

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $previllage, $username, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $stmt = $this->conn->prepare("INSERT INTO login(id_user, username, encrypted_password, salt, status) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $uuid, $username, $encrypted_password, $salt, $previllage);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
           if ($previllage == '1') {
                //create data to pengguna
                $stmt2 = $this->conn->prepare("INSERT INTO pengguna(id_user, nama) VALUES(?, ?)");
                $stmt2->bind_param("ss", $uuid, $name);
                $result2 = $stmt2->execute();
           }else{
                //create data to guru
                $stmt2 = $this->conn->prepare("INSERT INTO guru(id_guru, nama) VALUES(?, ?)");
                $stmt2->bind_param("ss", $uuid, $name);
                $result2 = $stmt2->execute();

                //create default rating to guru
                $stmt3 = $this->conn->prepare("INSERT INTO rating_guru(id_guru, rating) VALUES(?, 5)");
                $stmt3->bind_param("s", $uuid);
                $result3 = $stmt3->execute();
           }
           return true;
        } else {
            return false;
        }
    }

    /**
     * Get user by username and password
     */
    public function getUserByUsernameAndPassword($username, $password, $previllage) {

        $stmt = $this->conn->prepare("SELECT * FROM login WHERE username = ? AND status = ?");

        $stmt->bind_param("ss", $username,$previllage);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($username) {
        $stmt = $this->conn->prepare("SELECT username from login WHERE username = ?");

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    public function isUserAlreadyRate($id_user,$id_guru) {
        $stmt = $this->conn->prepare("SELECT * from rating WHERE id_user = ? AND id_guru = ?");

        $stmt->bind_param("ss", $id_user, $id_guru);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

     public function isUserAlreadyBook($id_user,$id_guru) {
        $stmt = $this->conn->prepare("SELECT * from pesan WHERE id_user = ? AND id_guru = ?");

        $stmt->bind_param("ss", $id_user, $id_guru);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    public function isUserAlreadyTransaction($id_guru,$id_lowongan) {
        $stmt = $this->conn->prepare("SELECT * from transaksi_lowongan WHERE id_guru = ? AND id_lowongan = ?");

        $stmt->bind_param("si", $id_guru, $id_lowongan);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }


    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

    function GetDrivingDistance($lat1, $lat2, $long1, $long2){
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";

        $data = file_get_contents($url);
        $data = json_decode($data);

        $distance = 0;

        foreach($data->rows[0]->elements as $road) {
            $distance += $road->distance->value;
        }

        return $distance/1000;
    }

    public static function vincentyGreatCircleDistance($latitudeFrom, $latitudeTo, $longitudeFrom, $longitudeTo){
      // convert from degrees to radians
      $earthRadius = 6371000;

     // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);

      $lonDelta = $lonTo - $lonFrom;
      $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
      $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

      $angle = atan2(sqrt($a), $b);
      return number_format((($angle * $earthRadius)/1000), 2, '.', '');
    }

    /**
     * updating existed user
     * returns user details
     */
    public function pengguna_update($id_user,$nama,$alamat,$no_telp,$email,$lat,$lng,$foto) {
        $photo = $id_user . ".png";  
        $path = "upload/$id_user.png";

        $stmt = $this->conn->prepare("UPDATE pengguna SET nama=?, alamat=?, no_telp=?, email=?, lat=?, lng=?, foto=? WHERE id_user = ?");
        $stmt->bind_param("ssssssss", $nama, $alamat, $no_telp, $email, $lat, $lng, $photo, $id_user);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            if (unlink($path) || !unlink($path)) {
                file_put_contents($path,base64_decode($foto));
            }
            return true;
        } else {
            return false;
        }
    }

    public function pengguna_select($id_user) {

        $user = array();

        $domain = $this->domain;

        $sql = "SELECT * FROM pengguna WHERE id_user = '$id_user'";
        
        $result = $this->conn->query($sql);

        //check row based on uid_guru and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = array(
                    "nama" => $row["nama"],
                    "alamat" => $row["alamat"],
                    "no_telp" => $row["no_telp"],
                    "email" => $row["email"],
                    "foto" => $domain.'/guruku_api/upload/'.$row["foto"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"],);
                array_push($user, $temp);
            }
            return $user;
        }else{
            return NULL;
        }
    }


    public function guru_update($id_guru,$nama,$alamat,$no_telp,$email,$pendidikan,$pengalaman,$deskripsi,$lat,$lng,$foto,$ipk,$kampus,$jurusan) {
        $photo = $id_guru . ".png";  
        $path = "upload/$id_guru.png";

        $stmt = $this->conn->prepare("UPDATE guru SET nama=?, alamat=?, no_telp=?, email=?, pendidikan=?, pengalaman=?, deskripsi=?, lat=?, lng=?, foto=?, ipk=?, kampus=?, jurusan=? WHERE id_guru = ?");
        $stmt->bind_param("sssssissssdsss", $nama, $alamat, $no_telp, $email, $pendidikan, $pengalaman, $deskripsi, $lat, $lng, $photo, $ipk,$kampus, $jurusan, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            if (unlink($path) || !unlink($path)) {
                file_put_contents($path,base64_decode($foto));
            }
            return true;
        } else {
            return false;
        }
    }

    public function guru_select($id_guru) {

        $user = array();

        $domain = $this->domain;

        $sql = "SELECT * FROM guru WHERE id_guru = '$id_guru'";
        
        $result = $this->conn->query($sql);

        //check row based on uid_guru and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = array(
                    "nama" => $row["nama"],
                    "alamat" => $row["alamat"],
                    "no_telp" => $row["no_telp"],
                    "email" => $row["email"],
                    "kelamin" => $row["kelamin"],
                    "pendidikan" => $row["pendidikan"],
                    "pengalaman" => $row["pengalaman"],
                    "deskripsi" => $row["deskripsi"],
                    "foto" => $domain.'/guruku_api/upload/'.$row["foto"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"],
                    "usia" => $row["usia"],
                    "ipk" => $row["ipk"],
                    "kampus" => $row["kampus"],
                    "jurusan" => $row["jurusan"],
                    "skill" => $this->skill_get_by($id_guru),
                    "jadwal" => $this->jadwal_get_by($id_guru),
                    "rating" => $this->rating_get_by($id_guru),
                    "rating1" => $this->rating_get($id_guru),
                    "rating2" => $this->rating_get_review($id_guru));
                array_push($user, $temp);
            }
            // array_push($user, $skill);
            return $user[0];
        }else{
            return NULL;
        }
    }

    public function guru_get_all($lat,$lng) {

        $user = array();

        $sql = "SELECT nama,foto,id_guru,pengalaman,lat,lng,no_telp,kampus,jurusan,alamat FROM guru ORDER BY pengalaman DESC";
        // var_dump($this->conn)
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                //mendapatkan jarak antara 2 titik koordinat
                $dist = $this->vincentyGreatCircleDistance($lat, $row['lat'], $lng, $row['lng']);

                if ($dist < 20) {
                    $temp = array(
                        "dist" => $dist,
                        "id_guru" => $row['id_guru'],
                        "nama" => $row['nama'],
                        "foto" => $row['foto'],
                        "no_telp" => $row['no_telp'],
                        "kampus" => $row['kampus'],
                        "jurusan" => $row['jurusan'],
                        "alamat" => $row['alamat'],
                        "pengalaman" => $row['pengalaman']);
                     array_push($user, $temp);
                }
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function lowongan_create($id_user, $subjek, $description) {
        $stmt = $this->conn->prepare("INSERT INTO lowongan(id_user, subjek, description, tanggal_buat) VALUES(?, ?, ?, NOW())");
        $stmt->bind_param("sss", $id_user, $subjek, $description);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
           return true;
        } else {
            return false;
        }
    }

     public function lowongan_update($id, $id_user, $subjek, $description) {

        $stmt = $this->conn->prepare("UPDATE lowongan SET subjek=?, description=? WHERE id_user = ? AND id = ?");
        $stmt->bind_param("ssss", $subjek, $description, $id_user, $id);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function lowongan_delete($id, $id_user) {

        $stmt = $this->conn->prepare("DELETE FROM lowongan WHERE id_user = ? AND id = ?");
        $stmt->bind_param("si", $id_user, $id);
        $result = $stmt->execute();
        $stmt->close();

        $stmt2 = $this->conn->prepare("DELETE FROM transaksi_lowongan WHERE id_lowongan = ?");
        $stmt2->bind_param("i",$id);
        $result2 = $stmt2->execute();
        $stmt2->close();

        // check for successful store
        if ($result && $result2) {
            return true;
        } else {
            return false;
        }
    }

    public function lowongan_get_all($lat,$lng) {

        $user = array();

        $sql = "SELECT 
        lowongan.id, 
        lowongan.id_user, 
        lowongan.subjek, 
        lowongan.description, 
        pengguna.nama,
        pengguna.foto,
        pengguna.alamat,
        pengguna.no_telp,
        pengguna.email,
        pengguna.lat,
        pengguna.lng 
        FROM lowongan 
        INNER JOIN pengguna ON lowongan.id_user = pengguna.id_user 
        ORDER BY id DESC";
        // var_dump($this->conn)
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {

                //mendapatkan jarak antara 2 titik koordinat
                $dist = $this->vincentyGreatCircleDistance($lat, $row['lat'], $lng, $row['lng']);

                if ($dist < 20) {
                    $temp = array(
                        "id" => $row["id"],
                        "id_user" => $row["id_user"],
                        "subjek" => $row["subjek"],
                        "description" => $row["description"],
                        "nama" => $row["nama"],
                        "foto" => $row["foto"],
                        "alamat" => $row["alamat"],
                        "no_telp" => $row["no_telp"],
                        "email" => $row["email"],
                        "lat" => $row["lat"],
                        "lng" => $row["lng"],
                        "dist" => $dist);
                    array_push($user, $temp);
                }
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function lowongan_get_by($id_user) {

        $user = array();

        $sql = "SELECT * FROM lowongan WHERE id_user = '$id_user' ORDER BY id DESC";
        
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = array(
                    "id" => $row["id"],
                    "id_user" => $row["id_user"],
                    "subjek" => $row["subjek"],
                    "description" => $row["description"]);
                array_push($user, $temp);
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function rating_create($id_user, $id_guru, $rating, $review) {
        $stmt = $this->conn->prepare("INSERT INTO rating(id_user, id_guru, rating, review) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id_user, $id_guru, $rating, $review);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function rating_update($id_user, $id_guru, $rating, $review) {

        $stmt = $this->conn->prepare("UPDATE rating SET rating=?, review=? WHERE id_user = ? AND id_guru = ?");
        $stmt->bind_param("ssss", $rating, $review, $id_user, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function rating_get_by($id_guru) {
        $user = array();

        $sql = "SELECT rating FROM rating WHERE id_guru = '$id_guru' ORDER BY id";
        
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $user[] = $row['rating'];
                 // Inside while loop
            }
            return $user;
        }else{
            return array();
        }
    }

    public function rating_guru_update($id_guru, $rating) {

        $stmt = $this->conn->prepare("UPDATE rating_guru SET rating=? WHERE id_guru = ?");
        $stmt->bind_param("ss", $rating, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function rating_get_review($id_guru) {
       $user = array();

        $sql = "SELECT 
        rating.id_user,
        rating.rating,
        rating.review,
        pengguna.nama,
        pengguna.foto 
        FROM rating 
        INNER JOIN pengguna ON rating.id_user = pengguna.id_user 
        WHERE id_guru = '$id_guru'";

        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $user[] = $row ;
            }
            return $user;
        }else{
            return array();
        }
    }

    public function rating_get($id_guru) {

        $user = array();

        $sql = "SELECT rating FROM rating_guru WHERE id_guru = '$id_guru'";
        
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = array(
                    "rating" => $row["rating"]);
                array_push($user, $temp);
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function skill_create($id_guru, $jenjang, $mapel, $biaya) {
        $stmt = $this->conn->prepare("INSERT INTO skill_guru(id_guru, jenjang, mapel, biaya) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id_guru, $jenjang, $mapel, $biaya);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

     public function skill_update($id, $id_guru, $jenjang, $mapel, $biaya) {

        $stmt = $this->conn->prepare("UPDATE skill_guru SET jenjang=?, mapel=?, biaya=? WHERE id_guru = ? AND id = ?");
        $stmt->bind_param("ssssi", $jenjang, $mapel, $biaya, $id_guru, $id);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function skill_delete($id, $id_guru) {

        $stmt = $this->conn->prepare("DELETE FROM skill_guru WHERE id = $id AND id_guru = '$id_guru'");
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function skill_get_by($id_guru) {

        $user = array();
        
        $sql = "SELECT * FROM skill_guru WHERE id_guru = '$id_guru'";
        
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = array(
                    "id" => $row["id"],
                    "id_guru" => $row["id_guru"],
                    "jenjang" => $row["jenjang"],
                    "mapel" => $row["mapel"],
                    "biaya" => $row["biaya"]);
                array_push($user, $temp);
            }
            return $user;
        }else{
            return array();
        }
    }

    public function jadwal_create($id_guru,$hari,$jam_mulai,$jam_selesai) {
        $stmt = $this->conn->prepare("INSERT INTO jadwal( id_guru, hari, jam_mulai, jam_selesai) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id_guru, $hari, $jam_mulai, $jam_selesai);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function jadwal_get_by($id_guru) {

        $user = array();
        
        $sql = "SELECT * FROM jadwal WHERE id_guru = '$id_guru'";
        
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $user[] = $row;
            }
            return $user;
        }else{
            return array();
        }
    }

    public function jadwal_update($id, $id_guru, $hari, $jam_mulai, $jam_selesai) {

        $stmt = $this->conn->prepare("UPDATE jadwal SET hari=?, jam_mulai=?, jam_selesai=? WHERE id = ? AND id_guru = ?");
        $stmt->bind_param("sssis", $hari, $jam_mulai, $jam_selesai, $id, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function jadwal_delete($id, $id_guru) {

        $stmt = $this->conn->prepare("DELETE FROM jadwal WHERE id = ? AND id_guru = ?");
        $stmt->bind_param("is", $id, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function booking_create($id_user, $id_guru) {
        $stmt = $this->conn->prepare("INSERT INTO pesan(id_user, id_guru, status) VALUES(?, ?, 0)");
        $stmt->bind_param("ss", $id_user, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function booking_update($id_user, $id_guru, $status) {
        $stmt = $this->conn->prepare("UPDATE pesan SET status='$status' WHERE id_user = ? AND id_guru = ?");
        $stmt->bind_param("ss", $id_user, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function booking_cancel($id_user, $id_guru) {

        $stmt = $this->conn->prepare("DELETE FROM pesan WHERE id_user = ? AND id_guru = ?");
        $stmt->bind_param("ss", $id_user, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function booking_get($id_user, $previllage) {

        $user = array();

        if ($previllage == '1') {
            $sql = "SELECT 
            pesan.id, 
            pesan.id_user, 
            pesan.id_guru, 
            pesan.status, 
            -- pesan.keterangan, 
            pengguna.nama,
            pengguna.foto,
            pengguna.alamat,
            pengguna.no_telp,
            pengguna.email,
            pengguna.lat,
            pengguna.lng  
            FROM pesan 
            INNER JOIN pengguna ON pesan.id_user = pengguna.id_user
            WHERE id_guru = '$id_user'";
        }else{
            $sql = "SELECT * FROM pesan WHERE id_user = '$id_user'";
        }
        
    
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $user[] = $row;
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function transaksi_lowongan_create($id_lowongan, $id_guru) {
        $stmt = $this->conn->prepare("INSERT INTO transaksi_lowongan(id_lowongan, id_guru, status) VALUES(?,?,0)");
        $stmt->bind_param("is", $id_lowongan,$id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function transaksi_lowongan_update($id_guru, $id_lowongan) {
        $stmt = $this->conn->prepare("UPDATE transaksi_lowongan SET status=1 WHERE id_lowongan = ? AND id_guru = ?");
        $stmt->bind_param("is", $id_lowongan, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function transaksi_lowongan_cancel($id_lowongan, $id_guru) {

        $stmt = $this->conn->prepare("DELETE FROM transaksi_lowongan WHERE id_lowongan = ? AND id_guru = ?");
        $stmt->bind_param("is", $id_lowongan, $id_guru);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function transaksi_lowongan_get($id_guru) {

        $sql = "SELECT 
        transaksi_lowongan.id, 
        transaksi_lowongan.id_lowongan,
        transaksi_lowongan.status,
        transaksi_lowongan.id_guru,
        lowongan.id_user,
        lowongan.subjek,
        lowongan.description,
        pengguna.nama,
        pengguna.foto,
        pengguna.alamat,
        pengguna.no_telp,
        pengguna.email,
        pengguna.lat,
        pengguna.lng 

        FROM transaksi_lowongan 
        INNER JOIN lowongan ON transaksi_lowongan.id_lowongan = lowongan.id 
        INNER JOIN pengguna ON lowongan.id_user = pengguna.id_user
        WHERE id_guru = '$id_guru'";
        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $user[] = $row;
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function fuzzy_search($harga_min,$harga_mid,$harga_max,$pengalaman_min,$pengalaman_mid,$pengalaman_max,$jarak_min,$jarak_mid,$jarak_max,$param_harga,$param_pengalaman,$param_jarak,$jenjang,$mapel,$hari,$kelamin,$latitude,$longitude){
        $user = array();


        $sql = "SELECT 
                guru.id_guru, guru.nama, guru.foto, guru.pengalaman, guru.lat, guru.pendidikan, guru.lng, guru.alamat, guru.kampus, guru.jurusan,guru.no_telp
                skill_guru.biaya, skill_guru.id, 
                rating_guru.rating
                FROM guru
                INNER JOIN jadwal ON jadwal.id_guru = guru.id_guru
                INNER JOIN skill_guru ON skill_guru.id_guru = guru.id_guru
                INNER JOIN rating_guru ON rating_guru.id_guru = guru.id_guru
                WHERE guru.kelamin = '$kelamin' AND jadwal.hari = '$hari' AND skill_guru.jenjang = '$jenjang' AND skill_guru.mapel = '$mapel'";

        $result = $this->conn->query($sql);

        //check row based on uid_user and month
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                 $dist = $this->vincentyGreatCircleDistance($latitude, $row['lat'], $longitude, $row['lng']);
                $fuzzy = $this->fuzzyFunction($harga_min,$harga_mid,$harga_max,$pengalaman_min,$pengalaman_mid,$pengalaman_max,$jarak_min,$jarak_mid,$jarak_max,$param_harga,$param_pengalaman,$param_jarak,$dist,$row['biaya'],$row['pengalaman']);
                 if ($fuzzy > 0) {
                     $temp = array(
                        "id_guru" => $row['id_guru'],
                        "nama" => $row['nama'],
                        "foto" => $row['foto'],
                        "alamat" => $row['alamat'],
                        "kampus" => $row['kampus'],
                        "jurusan" => $row['jurusan'],
                        "no_telp" => $row['no_telp'],
                        "pengalaman" => $row['pengalaman'],
                        "pendidikan" => $row['pendidikan'],
                        "lat" => $row['lat'],
                        "lng" => $row['lng'],
                        "biaya" => $row['biaya'],
                        "id_skill" => $row['id'],
                        "rating" => $row['rating'],
                        "jarak" => $jarak);
                    array_push($user, $temp);
                 }
            }
            return $user;
        }else{
            return NULL;
        }
    }

    public function fuzzyFunction($harga_min,$harga_mid,$harga_max,$pengalaman_min,$pengalaman_mid,$pengalaman_max,$jarak_min,$jarak_mid,$jarak_max,$param_harga,$param_pengalaman,$param_jarak,$jarak,$biaya,$pengalaman){

        //menempatkan syarat di array multidimensional
        $param = array(
            array($harga_min,$harga_mid,$harga_max), 
            array($pengalaman_min,$pengalaman_mid,$pengalaman_max),
            array($jarak_min,$jarak_mid,$jarak_max));

        $harga_array = array();
        $pengalaman_array = array();
        $jarak_array = array();
        $result = array();

        //looping fungsi fuzzy
        for ($i=0; $i <3 ; $i++) { 
            $harga_array[$i] = $this->fuzzyAlgorithm($i,$biaya,$param[$i][0],$param[$i][1],$param[$i][2]);
            $pengalaman_array[$i] = $this->fuzzyAlgorithm($i,$pengalaman,$param[$i][0],$param[$i][1],$param[$i][2]);
            $jarak_array[$i] = $this->fuzzyAlgorithm($i,$jarak,$param[$i][0],$param[$i][1],$param[$i][2]);
        }

        $result[1] = $harga_array[$param_harga];
        $result[2] = $harga_array[$param_pengalaman];
        $result[3] = $harga_array[$param_jarak];

        for ($j=0; $j < count($result); $j++) { 
             if ($j == 0) {
                 $min = $result[$i];
             }else{
                if ($min < $result[$i]) {
                    $min = $result[$i];
                }
             }
        }

        return $min;
    }

    public function fuzzyAlgorithm($counter,$value,$a,$b,$c){
        $return_value = 0;

        if ($counter == 0) {
            if ($value <= $a) {
                $return_value = 1;
            }elseif ($a < $value && $value < $b) {
                $return_value = ($b - $value)/($b - $a);
            }elseif ($value >= $b) {
                $return_value = 0;
            }
        }

        elseif ($counter == 1) {
            if ($value <= $a || $value >= $b) {
                $return_value = ($value-$a)/($b-$a);
            }elseif ($a < $value && $value < $b) {
                $return_value = ($value - $a)/($b - $a);
            }elseif ($b < $value && $value < $c) {
                $return_value = ($c - $value)/($c - $b);
            }
        }

        elseif ($counter == 2) {
            if ($value <= $b) {
                $return_value = 0;
            }elseif ($b < $value && $value < $c) {
                $return_value = ($value - $b)/($c - $b);
            }elseif ($value >= $c) {
                $return_value = 1;
            }
        }
        return $return_value;
    }
}

?>
