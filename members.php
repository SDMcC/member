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
    define('_VALID_MOS_', 1);
    require_once ('config.inc.php');
    require_once (DOCSPATH . 'includes/general.inc.php');
    require_once (DOCSPATH . 'includes/eg_epin.php');
    require_once (DOCSPATH . 'includes/eg_ssl.php');

    $egMember = new egMember();
    $egMember->initiate_member();


    $egLogs = new egLogs();
    egStats::write_member_stats();

    $_Url = new egURL();

    if($customPage = $_Url->getMemberCustomPage()) {
        require_once (_EG_MEMBER_MODULES_DIR . 'members_page.php');
        exit;
    }
    else {
        if($pa = $_Url->getMemberPage()) {
            $smarty = new egTemplater('member', true, $member_data);
            $smarty->check_restriction();

            if(file_exists($phpfile = _EG_MEMBER_MODULES_DIR. $pa.'.php')) {
                include($phpfile);
                exit();   
            }
            else{
                include (_EG_MEMBER_MODULES_DIR . 'page_invalid.php');   
            }
        }

        include (_EG_MEMBER_MODULES_DIR . 'home.php');
    }

    exit(); 
?>