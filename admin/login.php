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
    require_once ('../config.inc.php');
    define('_VALID_MOS_', 1);   
    require_once (DOCSPATH . 'includes/general.inc.php');
    require_once (DOCSPATH . 'includes/eg_ssl.php');
    require_once (DOCSPATH . 'includes/eg_admin.php');

    $egLanguage = new egLanguage();
    $egLanguage->load_public_language();

    $egAdmin = new egAdmin();

    if ($_POST) {
        if ($config['captcha_enable']) {
            $touring = new touring();
            if (! $touring->check($_POST['touring_code']))  $egAdmin->show_login_form(_error_captcha);
        } 
        $post = eg_safe_db($_POST);
        if ($post['uname'] && $_POST['pass']) {
            $egAdmin->login_admin($post['uname'], $_POST['pass']);  
            do_redirect("index.php");  
        }
        $egAdmin->show_login_form(); 
    }
    $egAdmin->show_login_form();
?>