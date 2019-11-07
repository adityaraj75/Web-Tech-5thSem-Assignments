<?php
require_once('DataBase.php');

class User
{
    private $exists;
    private $id;
    private $name;
    private $email;
    private $profilePicPath;

    public function __construct($id)
    {
        $result = DataBase::query('SELECT name,id,email,dob,profilepic'.
                                  ' FROM '.DataBase::$user_table_name.
                                  ' WHERE id=:id',
                                    array(':id'=>$id))['data'];
        if(count($result)===0){
            $this->exists=False;
            $this->id=NULL;
            $this->name=NULL;
            $this->email=NULL;
            $this->dob=NULL;
            $this->$profilePicPath=NULL;
            return;
        }
        $this->exists=True;
        $this->id=$result[0]['id'];
        $this->name=$result[0]['name'];
        $this->email=$result[0]['email'];
        $this->dob=new DateTime($result[0]['dob']);
        $this->profilePicPath=$result[0]['profilepic'];
    }

    public function isReal() {
        return $this->exists;
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getDOB() {
        return $this->dob;
    }

    public function getProfilePicPath() {
        return $this->profilePicPath;
    }

    public function follows($following_id){
        $following_user = new User($following_id);
        if(! $following_user->isReal()){
            return false;
        }
        if(! $this->exists){
            return false;
        }
        $result = DataBase::query('SELECT * FROM '.DataBase::$follow_table_name.
                                  ' WHERE userid = :userid'.
                                    ' AND followerid = :followerid',
                                  array(':userid'=>$following_id,
                                        ':followerid'=>$this->id)
                                );

        if(!$result['executed']){
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        if(count($result['data'])===1){
            return true;
        }
        return false;
    }

    public function noOfFollowers(){
        if(! $this->exists){
            return 0;
        }
        $result = DataBase::query('SELECT COUNT(*) AS count FROM '.DataBase::$follow_table_name.
                                  ' WHERE userid = :userid'.
                                    ' AND NOT followerid = :userid',
                                  array(':userid'=>$this->id)
                                );

        if(!$result['executed']){
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        return $result['data'][0]['count'];
    }

    public function noOfFollowing(){
        if(! $this->exists){
            return 0;
        }
        $result = DataBase::query('SELECT COUNT(*) AS count FROM '.DataBase::$follow_table_name.
                                  ' WHERE followerid = :userid'.
                                    ' AND NOT userid = :userid',
                                  array(':userid'=>$this->id)
                                );

        if(!$result['executed']){
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        return $result['data'][0]['count'];
    }

}

?>
