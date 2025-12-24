       <?php
        define('BASEPATH', true);
        require('../init.php');
        $secret = "YOUR SECRET KEY"; //Enter Your BitcoTasks SECRET KEY
        
        $userId = isset($_REQUEST['subId']) ? $db->EscapeString($_REQUEST['subId']) : null;
        $survey = isset($_REQUEST['transId']) ? $db->EscapeString($_REQUEST['transId']) : null;
        $reward = isset($_REQUEST['reward']) ? $db->EscapeString($_REQUEST['reward']) : null;
        $payout = isset($_REQUEST['payout']) ? $db->EscapeString($_REQUEST['payout']) : null;
        $action = isset($_REQUEST['status']) ? $db->EscapeString($_REQUEST['status']) : null;
        $userIP = isset($_REQUEST['userIp']) ? $db->EscapeString($_REQUEST['userIp']) : '0.0.0.0';
        $country = isset($_REQUEST['country']) ? $db->EscapeString($_REQUEST['country']) : null;
        
        if (md5($userId.$survey.$reward.$secret) != $_REQUEST['signature']){
        echo "ERROR: Signature doesn't match";
        return;
        }
        if(!empty($userId) && $db->QueryGetNumRows("SELECT * FROM `completed_offers` WHERE `survey_id`='".$survey."' LIMIT 1") == 0)
        {
        $user = $db->QueryFetchArray("SELECT `id` FROM `users` WHERE `id`='".$userId."'");
        if(!empty($user['id'])) {
        $tc_points = (0.10*($payout*100));
        $tc_points = ($tc_points < 1 ? 1 : number_format($tc_points, 0));
        $db->Query("UPDATE `users` SET `ow_credits`=`ow_credits`+'".$reward."', `tasks_contest`=`tasks_contest`+'".$tc_points."' WHERE `id`='".$user['id']."'"); 
        $db->Query("INSERT INTO `users_offers` (`uid`,`total_offers`,`total_revenue`,`last_offer`) VALUES ('".$user['id']."','1','".$reward."','".time()."') ON DUPLICATE KEY UPDATE `total_offers`=`total_offers`+'1', `total_revenue`=`total_revenue`+'".$reward."', `last_offer`='".time()."'");
        $db->Query("INSERT INTO `completed_offers` (`user_id`,`survey_id`,`user_country`,`user_ip`,`revenue`,`reward`,`method`,`timestamp`) VALUES ('".$user['id']."','".$survey."','".$country."','".ip2long($userIP)."','".$payout."','".$reward."','BitcoTasks','".time()."')");
        }
     }  
        echo 'ok';
       
       ?>
