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
    $egMember = new egMember($member_data);


    $_page_sub_menus = array();
    $_page_sub_menus[] = array('fa' => 'fas fa-user', 'bg' => 'bg-info', 'url' => "user.php?id=$userid", 'title' => $member_data['username']);
    $_page_sub_menus[] = array('fa' => 'fas fa-users', 'bg' => 'bg-success', 'url' => "user_network.php?do=referrals&id=$userid", 'title' => _network_referral);
    $_page_sub_menus[] = array('fa' => 'fas fa-users-slash', 'bg' => 'bg-warning', 'url' => "user_network.php?do=prospectus&id=$userid", 'title' => _network_prospect);
    $_page_sub_menus[] = array('fa' => 'fas fa-network-wired', 'bg' => 'bg-red', 'url' => "user_network.php?do=genealogy_unilevel&id=$userid", 'title' => _unilevel_tree);
    
    $_page_sub_menus[] = array('fa' => 'fas fa-project-diagram', 'bg' => 'bg-info', 'url' => "user_network.php?do=total_downlines&id=$userid", 'title' => _downlines_in_matrix);

    $adminMenus->set_main_id('members.php');

    if(isset($_REQUEST['do'])) {
        $do = safe_string($_REQUEST['do']);

        if(file_exists($phpfile = _EG_ADMIN_MODULES_DIR.'user_network/'.$do.'.php')) {
            require_once ($phpfile);
            exit();   
        }
    }

    require_once (_EG_ADMIN_MODULES_DIR . 'user_network/'.'referrals.php');
    exit(); 

?>