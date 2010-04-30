<?php
$uri = (file_exists(dirname(__FILE__)."/WebApp/application/config/database.php"))
    ? "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."/WebApp"
    : "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."/Installer";
?>
<html>
    <head>
        <script language="javascript" type="text/javascript">
            window.location ="<?php echo($uri); ?>";
        </script>
    </head>
</html>