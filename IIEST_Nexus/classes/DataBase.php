<?php

class DataBase
{

    ////////////////////////////////IIESTS DB///////////////////////////////////
    // private static $host_name='localhost';
    // private static $database_name='btech2017';
    // private static $database_username='btech2017';
    // private static $database_password='btech2017';
    ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////Arijit's Home DB/////////////////////////////////
    private static $host_name='localhost';
    private static $database_name='IIESTNexus';
    private static $database_username='PPLab';
    private static $database_password='PPRox';
    ////////////////////////////////////////////////////////////////////////////

    // Table Names:
    public static $user_table_name          = "ASARGUsers";
    public static $skill_table_name         = "ASARGSkills";
    public static $skill_reg_table_name     = "ASARGSkillReg";
    public static $token_table_name         = "asargtokens";
    public static $posts_table_name         = "ASARGPosts";
    public static $follow_table_name        = "ASARGFollowers";

    private static function connect()
    {
        $link=new PDO('mysql:host='.self::$host_name.';dbname='.self::$database_name.';charset=utf8',self::$database_username,self::$database_password);
        // $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $link;
    }



    public static function query($query, $params = array())
    {
        $statement=self::connect()->prepare($query);
        if(!($statement->execute($params)))
        {
            return array("executed"=>False,"errorInfo"=>$statement->errorInfo(),"data"=>NULL);
        }
        if(explode(' ', $query)[0]=='SELECT') //checks if the first word of the query is select
        {
            $data=$statement->fetchAll();
            return array("executed"=>True,"errorInfo"=>$statement->errorInfo(),"data"=>$data);
        }
        return array("executed"=>True,"errorInfo"=>NULL,"data"=>NULL);
    }

}
?>
