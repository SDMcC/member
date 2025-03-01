<?php
    /**
    * -------------------------------.*~ E G N  ~*.------------------------------
    * EGN Software: egMember
    * Copyright (c) 2012-2024 Elwin HG - http://egnsoftware.com (elwin@cvegn.com)
    * ---------------------------------------------------------------------------
    * This software is  furnished  under a  license and may  be used and   copied
    * only  in accordance with the terms of such  license and with  the inclusion
    * of  the above copyright notice.  This software or any other  copies thereof
    * may not be  provided or otherwise made available  to any other person.   No
    * title to and  ownership of the software is hereby transferred.
    * 
    * You  may  not  reverse   engineer,  decompile,  defeat  license  encryption
    * mechanisms, or  disassemble  this  software  product  or software   product
    * license. EGN  may terminate  this license if you  don't comply with any  of
    * the terms and   conditions set forth   in our  End  User  License Agreement
    * (EULA). In  such event, licensee  agrees to return licensor or  destroy all
    * copies of software upon termination  of the license. 
    * Please see the EULA file for the full End User License Agreement.
    * ---------------------------------------------------------------------------
    */
    if (defined('DOCSPATH')) require_once (DOCSPATH . 'config.inc.php');
    else  require_once ('../config.inc.php');
    define('_VALID_MOS_', 1);
    define('BOPATH', dirname(__FILE__) . '/');
    require_once (DOCSPATH . 'includes/general.inc.php');
    require_once (DOCSPATH . 'includes/eg_ssl.php');
    require_once (DOCSPATH . 'includes/eg_admin.php');
    require_once (DOCSPATH . 'includes/eg_reminder.php');
    
    
    $egAdmin = new egAdmin();
    $egAdmin->initiate_admin();


    switch ($_REQUEST['action']) {
        case 'notification':
            $text = array();
            $withdrawal = db_value("SELECT COUNT(id) FROM _table:withdrawals WHERE paid_status = '1'");
            if ($withdrawal) {
                $text[] = sprintf(_notify_withdrawal_request,$withdrawal);
            }

            $withdrawal_account_review = db_value("SELECT COUNT(account_id) FROM _table:withdrawal_account WHERE review_status = '1'");
            if ($withdrawal_account_review) {
                $text[] = sprintf(_notify_withdrawal_account_request, $withdrawal_account_review);
            }



            $expired = db_value("SELECT COUNT(userid) FROM _table:members WHERE member_state='2'"); 
            if($expired) {
                $text[] = sprintf(_notify_expired_member,$expired);
            }


            if(egFeatures::feature_enabled('epin')) { 
                if ($config['epin_enable']) {
                    $epin = db_value("SELECT COUNT(id) FROM _table:epins WHERE pin_status = '0'"); 
                    if($epin <= 10) $text[] = sprintf(_notify_epin_stock,$epin);

                }
            }

            $ticket = db_value("SELECT COUNT(id) FROM _table:tickets WHERE status = 'open' AND isanswered = '0'");
            if ($ticket) {
                $text[] = sprintf(_notify_new_ticket,$ticket);
            }    


            $testimonials = db_value("SELECT COUNT(id) FROM _table:testimonials WHERE active='0'");
            if ($testimonials) {
                $text[] = sprintf(_notify_new_testimonial,$testimonials);
            }

            $payment = db_value("SELECT COUNT(id) FROM _table:payments WHERE completed='0'");
            if ($payment) {
                $text[] = sprintf(_notify_incompleted_payment,$payment);
            }

            $_events = egReminder::get_ajax_events($admin_data);
            if ($_events) {
                $upcoming_events  = $_events;
                $events_total = count($_events);
            }


            $stats = egStats::get_stats();
            $response = array(
                'ok' => false, 
                'msg' => $text,
                'total' => count($text),
                'events_total' => $events_total,
                'events' => $upcoming_events,
                'stats' => $stats
            );

            //          echo '<pre>';
            //            print_r($response);
            //           exit;

            echo json_encode($response); 
            break;


        case 'check_username':
            $post = eg_safe_input($_POST);
            $username = safe_string($post['username']);
            $response = array(); 

            if (!$username || strlen($username) < 4 || strlen($username) > 12 || !preg_match("^[_\.0-9A-z-]+$^", $username)) {
                $response = array(
                    'ok' => false, 
                    'msg' => _error_username_empty);
            } 

            else if (read_loginid($username)) {
                $response = array(
                    'ok' => false, 
                    'msg' => sprintf(_error_username_registered, $username));
            } else {
                $response = array(
                    'ok' => true, 
                    'msg' => _username_available);
            }
            echo json_encode($response); 
            break; 

        case 'check_parent':
            $response = array(); 
            $userid = safe_string($_POST['userid']);
            $parent = safe_string($_POST['parent']);
            $response = array('ok' => true, 'msg' => "Member '{$parent}' is not found");
            if ($parent_data = load_user_data($parent, 0)) {
                $member_data = load_user_data($userid);
                $egMatrix = new egMatrix();
                if (!$err = $egMatrix->validate_matrix_parent($member_data, $parent_data)) {
                    $parent_data = get_public_info($parent_data);    
                    $response = array('ok' => true, 'msg' => "Upline is {$parent_data['name']} : {$parent_data['loginid']} ({$parent_data['userid']})"); 
                }
                else {

                    $response = array('ok' => true, 'msg' => $err);   
                }
            }
            echo json_encode($response); 
            break; 
        case 'check_info':
            $username = safe_string($_POST['username']);
            $response = array(); 
            if (!$username) {
                $response = array(
                    'ok' => false, 
                    'msg' => "Please specify a username");
            } 
            else {
                $member_data = load_user_data($username, 0);
                if (!is_array($member_data)) {
                    $response = array('ok' => true, 'msg' => sprintf(_error_no_member, $username), 'balance' => '');
                }
                else {
                    $member_data = get_public_info($member_data);    
                    $response = array(
                        'ok' => true, 
                        'msg' => "{$member_data['name']} : {$member_data['loginid']} ({$member_data['userid']})",
                        'name' => $member_data['name'], 
                        'email' => $member_data['email'], 
                        'phone' => $member_data['phone'],
                        'username' => $member_data['username']

                    );    
                }
            }
            echo json_encode($response); 
            break;

        case 'order_ship':
            $username = safe_string($_POST['username']);
            $response = array(); 
            if (!$username) {
                $response = array(
                    'ok' => false, 
                    'msg' => "Please specify a username");
            } 
            else {
                $member_data = load_user_data($username, 0);
                if (!is_array($member_data)) {
                    $response = array('ok' => true, 'msg' => sprintf(_error_no_member, $username), 'balance' => '');
                }
                else {
                    $m_data = get_public_info($member_data);    
                    $address = "{$member_data['address']}\n{$member_data['city']}, {$member_data['state']}, {$member_data['zip']}\n{$member_data['country']}";

                    $response = array(
                        'ok' => true, 
                        'msg' => "{$m_data['name']} : {$m_data['loginid']} ({$m_data['userid']})",
                        'ship_name' => $m_data['name'], 
                        'ship_phone' => $member_data['mphone'], 
                        'ship_address' => $address

                    );    
                }
            }
            echo json_encode($response); 
            break;


        case 'check_mphone':
            $username = safe_string($_POST['username']);
            $response = array(); 
            if (!$username) {
                $response = array(
                    'ok' => false, 
                    'msg' => "Please specify a username");
            } 
            else {
                $member_data = load_user_data($username, 0);
                if (!is_array($member_data)) {
                    $response = array('ok' => true, 'msg' => sprintf(_error_no_member, $username));
                }
                else {
                    $member_data = get_public_info($member_data);    

                    $mphone = phone_format_international($member_data['mphone']);

                    $response = array(
                        'ok' => true, 
                        'msg' => "<a href=\"#\" onclick=\"showPage('user.php?do=view&id={$member_data['userid']}')\">{$member_data['name']}: {$mphone} </a>"
                    );    
                }
            }
            echo json_encode($response); 
            break;
        case 'check_member':
            $username = safe_string($_POST['username']);
            $response = array(); 
            if (!$username) {
                $response = array(
                    'ok' => false, 
                    'msg' => "Please specify a username");
            } 
            else {
                $member_data = load_user_data($username, 0);
                if (!is_array($member_data)) {
                    $response = array('ok' => true, 'msg' => sprintf(_error_no_member, $username));
                }
                else {
                    $member_data = get_public_info($member_data);    
                    $response = array(
                        'ok' => true, 
                        'msg' => "<a href=\"#\" onclick=\"showPage('user.php?do=view&id={$member_data['userid']}')\">{$member_data['name']} : {$member_data['loginid']} ({$member_data['userid']})</a>"
                    );    
                }
            }
            echo json_encode($response); 
            break;  
        case 'chart_funds':

            $dataTable['cols'] = array(
                array('type' => 'string', 'label' => _year),
                array('type' => 'number', 'label' => _addfund_payment),
                array('type' => 'number', 'label' => _membership_payment),
                array('type' => 'number', 'label' => _epin_payment),
                array('type' => 'number', 'label' => _withdrawal)
            );

            $maxd = 15;
            for ($i = $maxd; $i > 0; $i--) {
                $data = array();
                $dp = date('dM', $timenow - ($i-1) * 3600 * 24);
                $start_date = date('Y-m-d 00:00:00', $timenow - ($i-1) * 3600 * 24);
                $end_date = date('Y-m-d 23:59:59',  $timenow - ($i-1) * 3600 * 24); 


                $payment_type = _payment_type_fund;
                $query1 = "SELECT count(id), SUM(amount)  FROM _table:payments WHERE (pay_date BETWEEN '$start_date' AND '$end_date') AND payment_type = '$payment_type' AND completed = '1'";
                $q1 = db_result($query1); 

                $payment_type = _payment_type_upgrade;
                $query2 = "SELECT count(id), SUM(amount)  FROM _table:payments WHERE (pay_date BETWEEN '$start_date' AND '$end_date') AND payment_type = '$payment_type' AND completed = '1'";
                $q2 = db_result($query2); 

                $payment_type = _payment_type_epin;
                $query3 = "SELECT count(id), SUM(amount)  FROM _table:payments WHERE (pay_date BETWEEN '$start_date' AND '$end_date') AND payment_type = '$payment_type' AND completed = '1'";
                $q3 = db_result($query3); 

                $query4 = "SELECT count(id), SUM(amount)  FROM _table:withdrawals WHERE (pay_date BETWEEN '$start_date' AND '$end_date') AND paid_status = '2'";
                $q4 = db_result($query4); 

                $data[] = array('v' => "$dp");
                $data[] = array('v' => $q1[0], 'f' => $config['currency'].format_money($q1[1]));
                $data[] = array('v' => $q2[0], 'f' => $config['currency'].format_money($q2[1]));
                $data[] = array('v' => $q3[0], 'f' => $config['currency'].format_money($q3[1]));
                $data[] = array('v' => $q4[0], 'f' => $config['currency'].format_money($q4[1]));
                $dataTable['rows'][] = array('c' => $data);
            }
            echo json_encode($dataTable);
            break; 

        case 'chart_stats':
            $dataTable['cols'] = array(
                array('type' => 'string', 'label' => _year),
                array('type' => 'number', 'label' => _log_stats),
                array('type' => 'number', 'label' => _members)
            );

            $maxd = 15;
            for ($i = $maxd;$i > 0;$i--) {
                $data = array();
                $dp = date('dM', $timenow - ($i-1) * 3600 * 24);
                $start_date = date('Y-m-d 00:00:00', $timenow - ($i-1) * 3600 * 24);
                $start_date = strtotime($start_date);
                $end_date = date('Y-m-d 23:59:59',  $timenow - ($i-1) * 3600 * 24);
                $end_date = strtotime($end_date);

                $query1 = "SELECT count(time) FROM _table:stats WHERE (time BETWEEN '$start_date' AND '$end_date') AND type = '0'";
                $q1 = db_value($query1);

                $query2 = "SELECT count(userid) FROM _table:members WHERE (joindate BETWEEN '$start_date' AND '$end_date')";
                $q2 = db_value($query2);

                $data[] = array('v' => "$dp");
                $data[] = array('v' => $q1);
                $data[] = array('v' => $q2);
                $dataTable['rows'][] = array('c' => $data);
            }
            echo json_encode($dataTable);
            break;


        case 'chart_earning':
            $dataTable['cols'] = array(
                array('type' => 'string', 'label' => _year),
                array('type' => 'number', 'label' => _log_earning),
                array('type' => 'number', 'label' => _log_points)
            );

            $maxd = 15;
            for ($i = $maxd;$i > 0;$i--) {
                $data = array();
                $dp = date('dM', $timenow - ($i-1) * 3600 * 24);
                $start_date = date('Y-m-d 00:00:00', $timenow - ($i-1) * 3600 * 24);
                $start_date = strtotime($start_date);
                $end_date = date('Y-m-d 23:59:59',  $timenow - ($i-1) * 3600 * 24);
                $end_date = strtotime($end_date);

                $query1 = "SELECT SUM(amount) FROM _table:earnings WHERE (pay_date BETWEEN '$start_date' AND '$end_date')";
                $q1 = db_value($query1);

                $query2 = "SELECT SUM(amount) FROM _table:points WHERE (pay_date BETWEEN '$start_date' AND '$end_date')";
                $q2 = db_value($query2);

                $data[] = array('v' => "$dp");
                $data[] = array('v' => $q1);
                $data[] = array('v' => $q2);
                $dataTable['rows'][] = array('c' => $data);
            }
            echo json_encode($dataTable);
            break;



        case 'bookmark':
            if ($_REQUEST['url'] && $_REQUEST['text']) {
                egAdmin::add_bookmark($_REQUEST);
            }
            break;

        case 'upload_img':
            require_once (_EG_ADMIN_MODULES_DIR .'upload_image.php');
            break;


    }


?>