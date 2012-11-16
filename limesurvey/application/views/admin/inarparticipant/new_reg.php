<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
//$qls->Security->check_auth_page('alter_reg.php'); 
?>
<html>
<head>
<link rel="stylesheet" type="text/css" HREF="html/form.css"/>
</head>
<body>
<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') 
{
require_once('includes/banner2.php');
require_once('html/custom_reg.php');
}
?>
</body>
</html>