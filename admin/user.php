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
    require_once (DOCSPATH . 'includes/eg_member.php');
    $egLanguage->include_admin_translation(__FILE__);

    if ($_REQUEST['id'] && ! $member_data = load_user_data($_REQUEST['id'])) egAdmin::admin_error(sprintf(_error_no_member, safe_string($_REQUEST['id'])));
    elseif ($uname = $_REQUEST['uname'] && ! $member_data = load_user_data($_REQUEST['uname'], 2)) egAdmin::admin_error(sprintf(_error_no_member, safe_string($_REQUEST['uname'])));
    $userid = $member_data['userid'];

    $_page_sub_menus = array();
    $_page_sub_menus[] = array('fa' => 'fas fa-user', 'bg' => 'bg-info', 'url' => "user.php?id=$userid", 'title' => $member_data['username']);
    $_page_sub_menus[] = array('fa' => 'fas fa-wallet', 'bg' => 'bg-warning', 'url' => "user.php?do=balance&id=$userid", 'title' => _member_balance);

    $_page_sub_menus[] = array('fa' => 'fas fa-hand-holding-usd', 'bg' => 'bg-red', 'url' => "user.php?do=max_earning&id=$userid", 'title' => _max_earning);

    if ($config['point_system']) { 
        $_page_sub_menus[] = array('fa' => 'fas fa-coins', 'bg' => 'bg-indigo','url' => "user.php?do=point&id=$userid", 'title' => _point);
    }

    $_page_sub_menus[] = array('fa' => 'fas fa-money-check', 'bg' => 'bg-gray','url' => "user.php?do=withdrawal_account&id=$userid", 'title' => _withdrawal_account);



    if ($config['rank_enabled']) { 
        $_page_sub_menus[] = array('fa' => 'fa fa-star', 'bg' => 'bg-orange','url' => "user.php?do=rank&id=$userid", 'title' => _rank);
    }

    if ($config['epin_enable']) { 
        $_page_sub_menus[] = array('fa' => 'fa fa-chalkboard-teacher', 'bg' => 'bg-primary','url' => "user.php?do=epin&id=$userid", 'title' => _epin);
    }



    $_page_sub_menus[] = array('fa' => 'fas fa-users', 'bg' => 'bg-success', 'url' => "user_network.php?do=referral&id=$userid", 'title' => _member_network);
    $_page_sub_menus[] = array('fa' => 'fa fa-chart-bar', 'bg' => 'bg-navy', 'url' => "user_logs.php?do=earning&id=$userid", 'title' => _member_log);
    $_page_sub_menus[] = array('fa' => 'fa fa-envelope-open-text', 'bg' => 'bg-olive', 'url' => "contact.php?do=email&id=$userid", 'title' => _member_contact);

    $adminMenus->set_main_id('members.php');

    $egMember = new egMember($member_data);
    $member_data = $egMember->member_data;

    if($egMember->check_member_expiration()) {
        $egMember->reload_member_data();
    }

    if(isset($_REQUEST['do'])) {
        if(file_exists($phpfile = _EG_ADMIN_MODULES_DIR. 'user/' . safe_string($_REQUEST['do']) . '.php')) {
            require_once ($phpfile);
            exit();   
        }
        else{
            egAdmin::admin_error(_error_page_not_available);
        }
    }

    require_once (_EG_ADMIN_MODULES_DIR . 'user/'.'profile.php');
    exit(); 


?>