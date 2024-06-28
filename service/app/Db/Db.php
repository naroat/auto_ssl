<?php

class SQLiteDB extends SQLite3
{
    public $dbFile = '';

    public function __construct($dbFile)
    {
        if (empty($dbFile)) {
            throw new \Exception('db文件不能为空');
        }
        $this->dbFile = $dbFile;
        //连接
        $this->open($dbFile);
    }

    public function getOne($sql)
    {
        $data = $this->handleQuery($sql);
        return $data[0] ?? [];
    }

    public function getList($sql)
    {
        $data = $this->handleQuery($sql);
        return $data;
    }

    public function handleQuery($sql)
    {
        $sql = $this->formatSql($sql);
        $result = $this->query($sql);
        return $this->fetch($result);
    }

    //fetch
    public function fetch($data)
    {
        $newData = [];
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $newData[] = $row;
        }
        return $newData;
    }

    public function formatSql($sql)
    {
        return <<<EOF
        {$sql}
EOF;
    }
}

try {
    //连接
    $dbFile = 'tmp.db';
    $db = new SQLiteDB($dbFile);
    //创建表
/*    $sql =<<<EOF
      CREATE TABLE COMPANY
      (ID INT PRIMARY KEY     NOT NULL,
      NAME           TEXT    NOT NULL,
      AGE            INT     NOT NULL,
      ADDRESS        CHAR(50),
      SALARY         REAL);
EOF;

    $db->exec($sql);*/

    //查询
    $sql = 'select * from `COMPANY`';
//    $data = $db->getOne($sql);
    $data = $db->getList($sql);
    $data = array_rand($data);
    var_dump($data);
    exit;
    $db->close();
} catch (Exception $exception) {
    var_dump($exception->getMessage());exit;
}



