<?php include_once("header.php"); ?>
<?php
    $htaccessFile = dirname(__FILE__)."/../.htaccess";
    $webAppConfigFile = dirname(__FILE__)."/../WebApp/application/config/config.php";
    /*
     * So ...
     */

     $checks = array();

     $check->check = "First off, I need to check to see if you have a .htaccess file in place.";
     $check->result = (file_exists($htaccessFile));
     $check->message = $check->result
                         ? "Excelent, its there."
                         : "Oops, you don't seem to have a .htaccess file in the root ".
                           "folder of my install. This can happen sometimes when ".
                           "you take the code from my GitHub repo but shouldn't if you ".
                           "got me a packaged download. Its fine though, you just need to ".
                           "goto my GitHub repo, download the file and place it in my root ".
                           "folder.";
     $checks[] = $check;
     unset($check);
     
     $check->check = "Now I'm going to check that I can write to the .htaccess file.";
     $check->result = is_writable($htaccessFile);
     $check->message = $check->result
                       ? "No problems here, I can open it and write to it."
                       : "Oops, thats a shame, I dont seem to be able to open it ".
                         "for writing (I tried is_writable()). Can you check ".
                         "the file permissions please to ensure I can write to it?";
                         
     $checks[] = $check;
     
     unset($check);

     $check->check = "Now onto the next file: I need to check to see if you have the web application config file in place.";
     $check->result = (file_exists($webAppConfigFile));
     $check->message = $check->result
                         ? "Excelent, its in the right place."
                         : "Oops, you don't seem to have a config.php file in the ".
                           "directory [myroot]/WebApp/application/config . . . this ".
                           "has been known to happen, sorry! . . . All you need to do ".
                           "is create a blank file for me in the right place called config.php can ".
                           "you do that for me?";
     $checks[] = $check;
     unset($check);

     $check->check = "Now I'm going to check that I can write to the web application config file.";
     $check->result = is_writable($webAppConfigFile);
     $check->message = $check->result
                       ? "No problems here, I can open it and write to it."
                       : "Oops, thats a shame, I dont seem to be able to open it ".
                         "for writing (I tried is_writable()). Can you check ".
                         "the file permissions please to ensure I can write to it?";

     $checks[] = $check;

     unset($check);

     //check directory acces to key directories
     $check->check = "So now I need to check that the directories I need to write to are ".
                     "available to me. I'll check them all in one go to save time ... they are: ".
                     "[myroot]/Core/Configuration/ConfigurationFiles ... [myroot]/Core/Modules ".
                     "... and ... [myroot]/WebApp/application/config.";
     $check->result = is_writable(dirname(__FILE__)."/../Core/Configuration/ConfigurationFiles") &&
                      is_writable(dirname(__FILE__)."/../Core/Modules") &&
                      is_writable(dirname(__FILE__)."/../WebApp/application/config");
     $check->message = $check->result
                        ? "Great news ... they are all willing to accept my changes!"
                        : "Oops, at least one of the above directories wouldn't let me ".
                          "write to it (I tried is_writable()). Can you check the permissions ".
                          "of all of them for me please?";
     $checks[] = $check;

     unset($check);

?>
<div id="php-checks">
<script language="javascript" type="text/javascript">
    var data = {"checks":<?php echo(json_encode($checks)); ?>};
    var checks = data.checks;
    var counter = 0;
    $(document).ready(function(){
        DoMessage("check");
    });

    function DoMessage(messageType) {
        if(messageType == "check") {
            ClearMessages()
            DoWriteMessage(
                "div#messages div.check",
                checks[counter].check,
                GetTime(checks[counter].check)
            );
            setTimeout(
                "DoMessage('wait')",
                (GetTime(checks[counter].check)* 1000) + 500
            );
        } else if (messageType == "wait") {
            DoWriteMessage(
                "div#messages div.wait",
                " . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .",
                GetTime(" . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .")
            );
            setTimeout(
                "DoMessage('message')",
                (GetTime(" . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .")*1000) + 500
            );
        } else if (messageType == "message") {
            var time = GetTime(checks[counter].message);
            DoWriteMessage("div#messages div.message", checks[counter].message, time);
            if(checks[counter].result != 1) {
                setTimeout('DoMessage("fail")', (time * 1000) + 500);
            }else if(counter < checks.length - 1) {
                counter++;
                setTimeout('DoMessage("check")', (time * 1000) + 500);
            } else {
                setTimeout('DoMessage("sucess")', (time * 1000) + 500);
            }
        } else if (messageType == "sucess") {
            ClearMessages()
            DoWriteMessage(
                "div#messages div.message",
                "Ok, happy days, thats all the checks in this step passed.",
                5);
            setTimeout('$("div#messages div.action").show();', 5500);
        } else if (messageType == "fail") {
            setTimeout('$("div#messages div.fail").show();', 1000);
        }
    }

    function ClearMessages() {
        $("div#messages div:not(.action, .fail)").each(function() {
            $(this).html("");
        });
    }
</script>
    <img id="logo-callout" src="Assets/Images/logo-callout.png" />
    <div id="baloon">
        <div class="top">&nbsp;</div>
        <div class="mid">
            <div id="messages">
                <div class="check"></div>
                <div class="wait"></div>
                <div class="message"></div>
                <div class="action" style="display:none;">
                    <p>Lets move on to setting up the database that my Core will work with.</p>
                    <form action="step-db-intro.php" method="GET">
                        <input type="submit" value="lets go ..." class="button" />
                    </form>
                </div>
                <div class="fail" style="display:none;">
                    <p>Sorry about that! One of the tets I carried out didn't pass.</p>
                    <p>Can you go and try to fix it and then start the installation again?</p>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>
</div>
<?php include_once("footer.php"); ?>