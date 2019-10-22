<?php
    // PHP File for asynchronous requests of skills, if logged in.

    require_once('../classes/DataBase.php');
    require_once('../classes/LoginClass.php');
    require_once('../classes/User.php');
    require_once('../classes/variables.php');

    $logged_in_id = LoginClass::isLoggedIn();

    if (! $logged_in_id)
    {
        // Someone is trying to access this php file and inspect its content
        // through direct URL.
        header("Location: index.php");
        exit();
    }

    $current_user = new User($logged_in_id);

    // Function to get parent of skill
    function parent($skillid){
        $result = DataBase::query('SELECT * FROM '.DataBase::$skill_table_name.' '.
                                    'WHERE skillid=:skillid',
                                  array(':skillid'=>$skillid));

        if ($result['executed']===false)
        {
            echo "ERROR: Not able to execute SQL<br>";
            // print_r($result['errorInfo']);
            exit();
        }
        else if(count($result['data'])===0){
            return false;
        }
        else{
            return $result['data'][0]['parent'];
        }
    }

    //Delete all children of skill:
    function deleteSkillForUser($skillid,$userid){
        $result = DataBase::query('SELECT skillid FROM '.DataBase::$skill_table_name.' '.
                                  'WHERE parent=:parentid',
                            array(':parentid'=>$skillid));

        if ($result['executed']===false)
        {
            echo "ERROR: Not able to execute SQL<br>";
            print_r($result['errorInfo']);
            exit();
        }
        foreach($result['data'] as $child)
        {
            deleteSkillForUser($child['skillid'],$userid);
        }
        DataBase::query('DELETE FROM '.DataBase::$skill_reg_table_name.' '.
                        'WHERE skillid=:skillid '.
                        'AND UserID=:userid',
                        array(':skillid'=>$skillid,
                                ':userid'=>$userid)
                        );
    }


    // To get JSON of My Skills:
    if(isset($_GET['showMy']))
    {
        $result = DataBase::query('SELECT a.skillid,skill,parent '.
                                  'FROM '.DATABASE::$skill_reg_table_name.' a,'.DATABASE::$skill_table_name.' b '.
                                  'WHERE a.skillid=b.skillid '.
                                      'AND a.Userid=:userid',
                                  array(':userid'=>$current_user->getId())
                              );
        if ($result['executed']===false)
        {
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result['data']);
            echo $result_json;
        }
    }



    // To get JSON of all skills:
    if(isset($_GET['showAll']))
    {
        $result = DataBase::query('SELECT * FROM '.DataBase::$skill_table_name);

        if ($result['executed']===false)
        {
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result['data']);
            echo $result_json;
        }
    }



    // To get JSON of suggestions of skills:
    if( isset($_GET["skillSearch"]) )
    {
        $inputString="%".strtolower($_GET["skillSearch"])."%";
        $result = DataBase::query('SELECT * FROM '.DataBase::$skill_table_name.' '.
                                  'WHERE LOWER(skill) LIKE :inputString '.
                                  'ORDER BY skill LIMIT 10',
                            array(':inputString'=>$inputString));

        if ($result['executed']===false)
        {
            echo "ERROR: Could not able to execute SQL<br>";
            print_r($result['errorInfo']);
        }
        else
        {
            $result_json=json_encode($result['data']);
            echo $result_json;
        }
    }


    // To add a skill:
    if(isset($_GET['addSkill']))
    {
        $skill_recognise = DataBase::query('SELECT skillid FROM '.DataBase::$skill_table_name.' '.
                                           'WHERE skill=:skill',
                                        array(':skill'=>$_GET['addSkill'])
                                    );

        if($skill_recognise['executed']===false){
            $skill_recognise['validSkill']=NULL;
            $result_json=json_encode($skill_recognise);
            echo $result_json;
            exit();
        }

        if(count($skill_recognise['data'])===0){
            $skill_recognise['validSkill']=false;
            $result_json=json_encode($skill_recognise);
            echo $result_json;
            exit();
        }


        $skillid = $skill_recognise['data'][0]['skillid'];

        // Add all Ancestor Skills:
        $currid=parent($skillid);
        while( ($currid!==false) && ($currid!==NULL) ){
            DataBase::query('INSERT INTO '.DataBase::$skill_reg_table_name.
                            '(skillid,UserID) VALUES(:skillid,:UserID)',
                        array(':skillid'=>$currid,':UserID'=>$current_user->getId())
                    );
            $currid = parent($currid);
        }

        $result = DataBase::query('INSERT INTO '.DataBase::$skill_reg_table_name.
                                  '(skillid,UserID) VALUES(:skillid,:UserID)',
                            array(':skillid'=>$skillid,':UserID'=>$current_user->getId())
                        );

        $result['validSkill']=true;
        $result_json=json_encode($result);
        echo $result_json;
    }


    // To Delete a skill:
    if( isset($_GET['deleteSkill']))
    {
        $skill_recognise = DataBase::query('SELECT skillid FROM '.DataBase::$skill_table_name.' '.
                                           'WHERE skill=:skill',
                                        array(':skill'=>$_GET['deleteSkill'])
                                    );

        if($skill_recognise['executed']===false){
            $skill_recognise['validSkill']=NULL;
            $result_json=json_encode($skill_recognise);
            echo $result_json;
            exit();
        }

        if(count($skill_recognise['data'])===0){
            $skill_recognise['validSkill']=false;
            $result_json=json_encode($skill_recognise);
            echo $result_json;
            exit();
        }

        $skillid = $skill_recognise['data'][0]['skillid'];

        // Delete this and all Child Skills:
        deleteSkillForUser($skillid,$current_user->getId());

        $result['deleted']=true;
        $result_json=json_encode($result);
        echo $result_json;
        exit();
    }

?>
