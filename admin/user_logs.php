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
    require_once ("admin.inc.php");
    $userid = number_only($_REQUEST['id']);
    if (!$member_data = load_user_data($userid)) egAdmin::admin_error(sprintf(_error_no_member, $userid));

    $_page_sub_menus = array();
    $_page_sub_menus[] = array('fa' => 'fas fa-user', 'bg' => 'bg-info', 'url' => "user.php?id=$userid", 'title' => $member_data['username']);
    $_page_sub_menus[] = array('fa' => 'fas fa-search-dollar ', 'bg' => 'bg-warning', 'url' => "user_logs.php?do=earning&id=$userid", 'title' => _log_earning);
    $_page_sub_menus[] = array('fa' => 'fas fa-hand-holding-usd', 'bg' => 'bg-success', 'url' => "user_logs.php?do=withdrawal&id=$userid", 'title' => _log_payout);
    $_page_sub_menus[] = array('fa' => 'fa fa-money-bill-wave', 'bg' => 'bg-indigo', 'url' => "user_logs.php?do=payment&id=$userid", 'title' => _log_payment);
    $_page_sub_menus[] = array('fa' => 'fas fa-comments-dollar', 'bg' => 'bg-olive', 'url' => "user_logs.php?do=transfer&id=$userid", 'title' => _log_transfer);
    $_page_sub_menus[] = array('fa' => 'fas fa-coins', 'bg' => 'bg-orange', 'url' => "user_logs.php?do=point&id=$userid", 'title' => _log_points);

    $_page_sub_menus[] = array('fa' => 'fas fa-exchange-alt', 'bg' => 'bg-red', 'url' => "user_logs.php?do=point_redeem&id=$userid", 'title' => _log_points_redeem);  
    

    if($config['log_account_activity']) {
        $_page_sub_menus[] = array('fa' => 'fas fa-calendar-week', 'bg' => 'bg-lightblue', 'url' => "user_logs.php?do=activity&id=$userid", 'title' => _log_activity);
    }

    $_page_sub_menus[] = array('fa' => 'fas fa-calendar-day', 'bg' => 'bg-navy', 'url' => "user_logs.php?do=access&id=$userid", 'title' => _log_access);
    $_page_sub_menus[] = array('fa' => 'fa fa-chart-bar', 'bg' => 'bg-primary', 'url' => "user_logs.php?do=stats&id=$userid", 'title' => _log_stats);

    $adminMenus->set_main_id('members.php');


    if(isset($_REQUEST['do'])) {
        $do = safe_string($_REQUEST['do']);

        if(file_exists($phpfile = _EG_ADMIN_MODULES_DIR.'logs/'.$do.'.php')) {
            require_once ($phpfile);
            exit();   
        }
    }

    require_once (_EG_ADMIN_MODULES_DIR . 'logs/'.'earning.php');
    exit(); 

?>