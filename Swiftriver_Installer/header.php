<?php
    error_reporting(E_ERROR);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" language="javascript" src="Assets/Scripts/jQuery.js"></script>
        <script type="text/javascript" language="javascript" src="Assets/Scripts/jTypeWriter.js"></script>
        <link rel="stylesheet" media="screen" href="Assets/Styles/master.css" />
        <script language="javascript" type="text/javascript">
            function GetTime(message) {
                return Math.floor(message.length / 15);
            }
            function DoWriteMessage(target, message, time) {
                $(target).jTypeWriter({duration:time,text:message});
            }
        </script>
    </head>
    <body>
        <div id="page">
