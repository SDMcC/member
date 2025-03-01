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
    require_once ('config.inc.php');
    require_once (DOCSPATH . 'includes/public.inc.php');

    if(isset($_GET['do'])) {
        if($_GET['do'] == 'language' && $_POST['language']) {
            $egLanguage->change_language($_POST['language']);
            do_redirect(egTpl::setUrl('index.php?pa=home'));
        }
    }
    
    $_Url = new egURL();
    
    
    if($customPage = $_Url->getCustomPage()) {
        egHtml::display_front_page("db:{$customPage}", $show_array);
    }
    else {
        if($pa = $_Url->getPage()) { 
           
            if(file_exists($phpfile = _EG_FRONT_MODULES_DIR. $pa.'.php')) {
                include($phpfile);
                exit();   
            }
        }

        include(_EG_FRONT_MODULES_DIR . 'home.php');
        exit();  
    }

?>