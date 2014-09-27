<?php
/**
 * Created by PhpStorm.
 * User: pablochiappetti
 * Date: 9/26/14
 * Time: 18:49
 */

namespace Mapstorming;

class DB {

    public function __construct()
    {
        $this->MONGO_SERVER = getenv('MONGO_SERVER');
        $this->MONGO_DB = getenv('MONGO_DB');
        $this->MONGO_USER = getenv('MONGO_USER');
        $this->MONGO_PWD = getenv('MONGO_PWD');
    }

    public function getAll($collection)
    {
        exec("mongo --quiet {$this->MONGO_SERVER}/{$this->MONGO_DB} -u {$this->MONGO_USER} -p{$this->MONGO_PWD} --eval='printjsononeline(db.$collection.find({}).toArray())'", $result);
        return $this->parse($result);
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function parse($result)
    {
        // Delete ObjectId()
        $delete = ['ObjectId(', '")'];
        $replace = ['', '"'];
        $result = str_replace($delete, $replace, $result[0]);

        return json_decode($result);
    }

    public function insert($collection, $document)
    {
        exec("mongo --quiet {$this->MONGO_SERVER}/{$this->MONGO_DB} -u {$this->MONGO_USER} -p{$this->MONGO_PWD} --eval='db.$collection.insert($document)'", $result);
        if (preg_match('|1|', $result[1])) return true;

        return false;
    }

    public function addItem($data, $match, $collection, $parent)
    {
        // Adds only $data to $parent if it is not already there.
        $query = "mongo --quiet {$this->MONGO_SERVER}/{$this->MONGO_DB} -u {$this->MONGO_USER} -p{$this->MONGO_PWD} --eval='db.$collection.update( { $match[0]: \"$match[1]\" }, {\$addToSet: { $parent: $data } } )'";
        exec($query, $result);

        return true;
    }
} 