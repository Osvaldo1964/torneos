<?php
class Mysql extends Conexion
{
    private $conexion;
    private $strquery;
    private $arrValues;

    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->conect();
    }

    public function insert(string $query, array $arrValues)
    {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $insert = $this->conexion->prepare($this->strquery);
        $resInsert = $insert->execute($this->arrValues);
        if ($resInsert) {
            $lastInsert = $this->conexion->lastInsertId();
        } else {
            $lastInsert = 0;
        }
        return $lastInsert;
    }

    public function select(string $query)
    {
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    public function select_all(string $query)
    {
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function update(string $query, array $arrValues)
    {
        $this->strquery = $query;
        $this->arrValues = $arrValues;
        $update = $this->conexion->prepare($this->strquery);
        $resExecute = $update->execute($this->arrValues);
        return $resExecute;
    }

    public function delete(string $query)
    {
        $this->strquery = $query;
        $result = $this->conexion->prepare($this->strquery);
        $resExecute = $result->execute();
        return $resExecute;
    }

    public function strClean($strChain)
    {
        $string = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $strChain);
        $string = trim($string);
        $string = stripslashes($string);
        $string = str_ireplace("<script>", "", $string);
        $string = str_ireplace("</script>", "", $string);
        $string = str_ireplace("<script src", "", $string);
        $string = str_ireplace("<script type=", "", $string);
        $string = str_ireplace("SELECT * FROM", "", $string);
        $string = str_ireplace("DELETE FROM", "", $string);
        $string = str_ireplace("INSERT INTO", "", $string);
        $string = str_ireplace("SELECT COUNT(*) FROM", "", $string);
        $string = str_ireplace("DROP TABLE", "", $string);
        $string = str_ireplace("OR '1'='1", "", $string);
        $string = str_ireplace('OR "1"="1"', "", $string);
        $string = str_ireplace('OR ´1´=´1´', "", $string);
        $string = str_ireplace("is NULL; --", "", $string);
        $string = str_ireplace("LIKE '", "", $string);
        $string = str_ireplace('LIKE "', "", $string);
        $string = str_ireplace("LIKE ´", "", $string);
        $string = str_ireplace("OR 'a'='a", "", $string);
        $string = str_ireplace('OR "a"="a"', "", $string);
        $string = str_ireplace("OR ´a´=´a´", "", $string);
        $string = str_ireplace("--", "", $string);
        $string = str_ireplace("^", "", $string);
        $string = str_ireplace("[", "", $string);
        $string = str_ireplace("]", "", $string);
        $string = str_ireplace("==", "", $string);
        return $string;
    }
}
