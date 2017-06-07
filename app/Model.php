<?php
/*************************
 * Proje: Empatisoft @ 2017
 * Developer: Onur KAYA
 * Telefon: 0537 493 10 20
 * E-posta: empatisoft@gmail.com
 * Web: www.empatisoft.com
 * PHP Sürümü: 7.0.9
 * MySQL Sürümü: 5.0.12 (InnoDB, MariaDB)
 * Oluşturma Tarihi: 6.06.2017 13:16
 */

class Model
{
    public function IntKontrol($GelenSayi){
        if(is_numeric($GelenSayi)) {
            return $GelenSayi;
        } else {
            return 0;
        }
    }

    // Veritabanı bağlantısı
    public function db_connect()
    {
        $db = null;
        if ($db === null) {
            try
            {
                $dsn = 'mysql:host=localhost;dbname=dbname;charset=utf8';
                $db = new PDO($dsn, 'user', 'pwd');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //echo 'Bağlantı tamam';
            }
            catch (PDOException $e)
            {
                //return $e->getMessage();
                echo $e->getMessage();
            }
        }
        return $db;
    }

    // DB işlemlerinde hata oluşması durumunda hataları kayıt altına alıyor.
    public function system_errors($konu, $detay)
    {
        $HTTP_X_FORWARDED_FOR = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
        $REMOTE_ADDR = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        $query = $this->db_connect()->prepare('INSERT INTO system_errors (REMOTE_ADDR, HTTP_X_FORWARDED_FOR, subject, detail) VALUES (:remote_addr, :http_x_forwarded_for, :subject, :detail)');
        $query->bindParam(':remote_addr', $REMOTE_ADDR);
        $query->bindParam(':http_x_forwarded_for', $HTTP_X_FORWARDED_FOR);
        $query->bindParam(':subject', $konu);
        $query->bindParam(':detail', $detay);
        $query->execute();
    }

    // Toplam kayıt sayısını döndürür
    public function get_total_count($primary_id, $table, $where)
    {
        if(is_null($where))
        {
            try {
                $query_string = 'SELECT COUNT('.$primary_id.') FROM ' . $table . '';
                $r = $this->db_connect()->prepare($query_string);
                $r->execute();
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch(PDOException $e) {
                $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
        else
        {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = 'WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            try {
                $query_string = 'SELECT COUNT('.$primary_id.') FROM ' . $table . ' ' . $where_string . '';
                $r = $this->db_connect()->prepare($query_string);
                $r->execute($where);
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch(PDOException $e) {
                $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }

    // Array olarak tüm verileri döndürür.
    public function get_table($primary_id, $colums = "*", $table, $where, $order_by, $order_sort, $pageable = false, $adet = 0, $sayfa = 0 )
    {
        if(is_null($where))
        {
            $query_string = 'SELECT '.$colums.' FROM '.$table . ' ORDER BY ' . $order_by . ' ' . $order_sort.'';
            try {
                if($pageable == true)
                {
                    $get_total_count = $this->get_total_count($primary_id, $table, $where);

                    if ($get_total_count == 0){
                        return null;
                    } else {
                        $toplam_sayfa = ceil($get_total_count / $adet);
                        $sayfa = (int) $sayfa;
                        if($sayfa < 1) $sayfa = 1;
                        if($sayfa > $toplam_sayfa) $sayfa = $toplam_sayfa;

                        $limit = ($sayfa - 1) * $adet;
                        $query = $this->db_connect()->prepare($query_string . ' LIMIT '.$limit.', '.$adet.'');
                        $query->execute();
                        return $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                }
                else
                {
                    $query = $this->db_connect()->prepare($query_string);
                    $query->execute();
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch(PDOException $e) {
                $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
        else
        {

            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = 'WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            $query_string = 'SELECT '.$colums.' FROM '.$table . ' ' . $where_string . ' ORDER BY ' . $order_by . ' ' . $order_sort.'';

            try {
                if($pageable == true)
                {
                    $get_total_count = $this->get_total_count($primary_id, $table, $where);

                    if ($get_total_count == 0){
                        return null;
                    } else {
                        $toplam_sayfa = ceil($get_total_count / $adet);
                        $sayfa = (int)$sayfa;
                        if ($sayfa < 1) $sayfa = 1;
                        if ($sayfa > $toplam_sayfa) $sayfa = $toplam_sayfa;

                        $limit = ($sayfa - 1) * $adet;
                        $query = $this->db_connect()->prepare($query_string . ' LIMIT ' . $limit . ', ' . $adet . '');
                        $query->execute($where);
                        return $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                }
                else
                {
                    $query = $this->db_connect()->prepare($query_string);
                    $query->execute($where);
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch(PDOException $e) {
                $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }

    // Row olarak tek veri döndürür.
    public function get_row($colums = "*", $table, $where, $order_by = null, $order_sort = null)
    {
        if(is_null($where))
        {
            return null;
        }
        else
        {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = 'WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            if($order_by != null && $order_sort != null)
            {
                $query_string = 'SELECT '.$colums.' FROM '.$table . ' ' . $where_string . ' ORDER BY ' . $order_by . ' ' . $order_sort;
            }
            else
            {
                $query_string = 'SELECT '.$colums.' FROM '.$table . ' ' . $where_string;
            }
            try {
                $query = $this->db_connect()->prepare($query_string);
                $query->execute($where);
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }

    // Veri ekleme fonksiyonu
    public function insert($table, $values)
    {
        try {

            $values_string = "";
            $colums_string = "";
            $value_count = 0;
            foreach ($values as $key => $value)
            {
                if($value_count == 0)
                {
                    $colums_string = $key;
                    $values_string = ':' . $key;
                }
                else
                {
                    $colums_string = $colums_string . ', ' . $key;
                    $values_string = $values_string . ', :' . $key;
                }
                $value_count++;
            }

            $query_string = 'INSERT INTO ' . $table .' ('.$colums_string.') VALUES ('.$values_string.')';
            $query = $this->db_connect()->prepare($query_string);
            foreach ($values as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            return $query->execute();

        } catch(PDOException $e) {
            $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    // Veri düzenleme fonksiyonu
    public function update($table, $values, $where)
    {
        try {

            $values_string = "";
            $value_count = 0;
            foreach ($values as $key => $value)
            {
                if($value_count == 0)
                {
                    $values_string = $key . ' = :' . $key;
                }
                else
                {
                    $values_string = $values_string . ', ' . $key . ' = :' . $key;
                }
                $value_count++;
            }

            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = ' WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }

            $query_string = 'UPDATE ' . $table .' SET '.$values_string . $where_string. '';
            $query = $this->db_connect()->prepare($query_string);
            foreach ($values as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            foreach ($where as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            return $query->execute();

        } catch(PDOException $e) {
            $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    // Veri silme fonksiyonu
    public function delete($table, $where)
    {
        try {

            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = ' WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }

            $query_string = 'DELETE FROM ' . $table .$where_string;
            $query = $this->db_connect()->prepare($query_string);
            foreach ($where as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            return $query->execute();

        } catch(PDOException $e) {
            $this->system_errors('SQL', 'Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage());
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    /*protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }*/
}