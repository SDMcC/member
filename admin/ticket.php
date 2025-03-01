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
    require_once (DOCSPATH . 'includes/eg_ticket.php'); 
    $egLanguage->include_admin_translation(__FILE__);
    
    if(isset($_REQUEST['do'])) {
        if(file_exists($phpfile = _EG_ADMIN_MODULES_DIR. 'ticket/' . safe_string($_REQUEST['do']) . '.php')) {
            require_once ($phpfile);
            exit();   
        }
        else{
            egAdmin::admin_error(_error_page_not_available);
        }
    }

    require_once (_EG_ADMIN_MODULES_DIR . 'ticket/'.'open.php');
    exit(); 

?>