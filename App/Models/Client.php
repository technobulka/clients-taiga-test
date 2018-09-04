<?php

namespace App\Models;

use App\Db;

class Client
{
    private $db;

    public function __construct()
    {
        $this->db = new Db();

        if ($this->db->connect_error) {
            throw new \Exception($this->db->connect_error);
        }
    }

    /**
     * @param string $table - table name
     * @return array - list of table columns
     */
    public function showColumns($table = '')
    {
        $result = [];

        if (!$table) {
            return $result;
        }

        $table = $this->db->real_escape_string($table);
        $query = $this->db->query("DESCRIBE $table");

        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * @param string $q - search query
     * @return array - list of clients
     */
    public function getList($q = '')
    {
        $result = [];
        $words = [];

        // search conditions
        $where = $order = '';

        if (!empty($q)) {
            $search = self::search($q);

            $words = $search['words'];
            $ids = implode(', ', $search['ids']);

            $where = "WHERE `c`.`id` in ($ids)";
            $order = "ORDER BY FIELD(`c`.`id`, $ids)";
        }

        $query = $this->db->query("
            SELECT `c`.*, GROUP_CONCAT(`p`.`phone`) AS `phones`
            FROM `clients` as `c`
            LEFT JOIN `clients_phones` as `p`
            ON `c`.`id` = `p`.`client_id`
            $where
            GROUP BY `c`.`id`
            $order");

        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {

                // highlight finded words
                foreach ($words as $word) {
                    $row['patronymic'] = preg_replace("#$word#ui", '<b>$0</b>', $row['patronymic']);
                    $row['phones'] = preg_replace("#$word#ui", '<b>$0</b>', $row['phones']);
                }

                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * @param int $id - client id
     * @return array - client data
     */
    public function getOne($id = 0)
    {
        $id = intval($id);
        $result = [];

        if (!$id) {
            return $result;
        }

        $query = $this->db->query("
            SELECT `c`.*, GROUP_CONCAT(`p`.`phone`) AS `phones`
            FROM `clients` as `c`
            LEFT JOIN `clients_phones` as `p`
            ON `c`.`id` = `p`.`client_id`
            WHERE `c`.`id` = $id
            GROUP BY `c`.`id`");

        if ($query->num_rows > 0) {
            $result = $query->fetch_assoc();
            $result['phones'] = explode(',', $result['phones']);
        }

        return $result;
    }

    /**
     * @param string $q - search query
     * @return array - separated words list and founded client ids list
     */
    private function search($q = '')
    {
        $q = $this->db->real_escape_string($q);

        // remove one-symbol words
        $search = preg_replace('/\b.\b/ui', ' ', $q);
        // remove double spaces
        $search = preg_replace('/\s{2,}/ui', ' ', $search);
        // trim spaces
        $search = trim(mb_strtolower($search, 'UTF-8'));

        $words = array_unique(explode(' ', $search));
        $count = count($words);
        $where = $order = '';

        foreach ($words as $i => $word) {
            $where .= ($i ? ' OR ' : ' ') . '`field` LIKE "%' . $word . '%"';
            $order .= ' WHEN `field` LIKE "' . $word . '%" THEN ' . $i;
        }

        // get clients ids
        $ids = [];
        $query = "SELECT `id` FROM `clients` WHERE $where ORDER BY CASE $order ELSE $count END, `field`";
        $query = str_replace('`field`', '`patronymic`', $query);

        $result = $this->db->query($query);

        while ($row = $result->fetch_row()) {
            $ids[] = $row[0];
        }

        // get clients ids by phones
        $query = "SELECT `client_id` FROM `clients_phones` WHERE $where ORDER BY CASE $order ELSE $count END, `field`";
        $query = str_replace('`field`', '`phone`', $query);
        $result = $this->db->query($query);

        while ($row = $result->fetch_row()) {
            $ids[] = $row[0];
        }

        $ids = array_unique($ids);

        return [
            'words' => $words,
            'ids' => $ids,
        ];
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function create($data = [])
    {
        if (empty($data)) {
            throw new \Exception('Client create invalid data.');
        } else {
            $keys = [];
            $values = [];
            $phones = [];

            foreach ($data as $key => $el) {
                if (is_array($el)) {
                    $phones = array_filter($el);
                    continue;
                }

                // escape data
                $key = $this->db->real_escape_string($key);
                $el = $this->db->real_escape_string($el);

                // convert birthday to date format
                if ($key === 'birthday') {
                    $el = date("Y-m-d", strtotime($el));
                }

                $keys[] = "`$key`";
                $values[] = "'$el'";
            }

            $keys = implode(', ', $keys);
            $values = implode(', ', $values);
            $this->db->query("INSERT INTO `clients` ($keys) VALUES ($values)");

            if (!empty($phones)) {
                self::addPhones($this->db->insert_id, $phones);
            }

            header('Location: /');
        }
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function edit($data = [])
    {
        if (empty($data)) {
            throw new \Exception('Client edit invalid data.');
        } else {
            $id = 0;
            $set = [];
            $phones = [];

            foreach ($data as $key => $el) {
                if ($key === 'id') {
                    $id = intval($el);
                    continue;
                }

                if (is_array($el)) {
                    $phones = array_filter($el);
                    continue;
                }

                // escape data
                $key = $this->db->real_escape_string($key);
                $el = $this->db->real_escape_string($el);

                // convert birthday to date format
                if ($key === 'birthday') {
                    $el = date("Y-m-d", strtotime($el));
                }

                $set[] = "`$key` = '$el'";
            }

            // update phones list
            $client = self::getOne($id);

            if (empty($phones)) {
                self::deletePhones($id);
            } elseif (array_values($client['phones']) !== array_values($phones)) {
                self::deletePhones($id);
                self::addPhones($id, $phones);
            }

            // save client
            $set = implode(', ', $set);
            $this->db->query("UPDATE `clients` SET $set WHERE `id` = $id");

            header('Location: /');
        }
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function delete($id = 0)
    {
        if (!$id) {
            throw new \Exception('Client not found.');
        } else {
            $this->db->query("DELETE FROM `clients` WHERE `id` = $id");
            self::deletePhones($id);

            header('Location: /');
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @throws \Exception
     */
    private function addPhones($id = 0, $data = [])
    {
        if (!$id || empty($data)) {
            throw new \Exception('Clients phones invalid data.');
        } else {
            foreach ($data as $el) {
                $el = $this->db->real_escape_string($el);
                $this->db->query("INSERT INTO `clients_phones` (`client_id`, `phone`) VALUES ('$id', '$el')");
            }
        }
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    private function deletePhones($id = 0)
    {
        if (!$id) {
            throw new \Exception('Clients phones invalid data.');
        } else {
            $this->db->query("DELETE FROM `clients_phones` WHERE `client_id` = $id");
        }
    }
}