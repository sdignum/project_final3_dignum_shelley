<link rel="stylesheet" href="css/main.css" type="text/css">
<?php


/**
 * PHP FormMail v 1.2.3
 *
 * Copyright (c) 2003 LAMP Host
 * http://www.lamphost.net
 *
 * This code is subject to version 1.0 of the LAMP Host license,
 * which is included with this package in the file license.txt,
 * and is available via the world-wide-web at
 * http://www.lampshost.net/source/license-1.0.html or by sending
 * a blank email to license@lamphost.net
 *
 * Credits: Matt Brown <mattbrown@lamphost.net>
 */

$fm = new Form_Mail();

class Form_Mail
{
    /**
     * Form_Mail
     */

    /* properties */
    var $error_message = "";
    var $print_array = array();
    var $config_vars_array = array("recipient"                 => "",
                                   "from"                      => "",
                                   "subject"                   => "Form Submission",
                                   "email"                     => "",
                                   "cc_email"                  => "",
                                   "redirect"                  => "",
                                   "required"                  => "",
                                   "env_report"                => "",
                                   "sort"                      => "",
                                   "print_config"              => "",
                                   "print_blank_fields"        => "0",
                                   "line_spacing"              => "0",
                                   "title"                     => "Form Submission",
                                   "return_link_url"           => "",
                                   "return_link_title"         => "Return",
                                   "missing_fields_redirect"   => "",
                                   "missing_fields_message"    => "<h2>Enter a valid email address</h2><p>Please enter a valid email address so we can respond to you.</p><p>\n<a onclick=\"history.go(-1)\">Click here</a> to return to the form.</p><p>&nbsp;</p>\n",
                                   "background"                => "",
                                   "bgcolor"                   => "#ffffff",
                                   "text_color"                => "#000000",
                                   "link_color"                => "blue",
                                   "vlink_color"               => "purple",
                                   "alink_color"               => "red",
                                   "thank_you_message"         => "We will respond to your message shortly<p>&nbsp;</p>",
                                  );
    var $referers_array = array();
    var $valid_env = array('REMOTE_ADDR','REMOTE_PORT','HTTP_REFERER','HTTP_USER_AGENT');

    /* methods */

    /**
     * Form_Mail();
     *
     * set_arrays()
     * check_referer()
     * check_recipient()
     * check_required_fields()
     * send_form()
     * display_thankyou()
     * display_missing_fields_error()
     * display_error()
     * display_header()
     * display_footer()
     */

    function Form_Mail()
    {
        /**
        * Form_Mail();
        */

        $this->referers_array = array($_SERVER["HTTP_HOST"]);
        /**
         * Leave AS IS to only allow posting from same host that script resides on.
         * List individual hosts to create list of hosts that can post to this script:
         * EXAMPLE: $referer_array = array ('example.com','www.example.com','192.168.0.1');
         */

        /* proccess form */
        $this->set_arrays();
        $this->check_referer();
        $this->check_recipient();
        $this->check_required_fields();
        $this->send_form();
        $this->display_thankyou();
    }

    function set_arrays()
    {
        /**
        * set_arrays();
        */

        foreach ($_POST as $key=>$value) {

            /* strip slashes if magic_quotes_gpc is on */
            $magic_quotes_gpc = (bool) ini_get('magic_quotes_gpc');
            if($magic_quotes_gpc == 1) {
                $value = stripslashes($value);
            }

            if (isset($this->config_vars_array[strtolower("$key")])) {
                $key = strtolower($key);
                $this->config_vars_array["$key"] = $value;
            } else {
                $this->print_array["$key"] = $value;
            }
        }

        /* create an array of config vars to print in message and add them to $print_array */
        if ($this->config_vars_array["print_config"] != "") {
            $print_config_array = explode(",", $this->config_vars_array["print_config"]);
            foreach ($print_config_array as $key=>$value) {
                $value = trim($value);
                $this->print_array["$value"] = $_POST["$value"];
            }
        }

        /* sort $print_array, if requested */
        if ($this->config_vars_array["sort"] == "alphabetic") {
            /* sort alphabetically */
            $print_array_keys_array = array_keys($this->print_array);
            sort($print_array_keys_array);
            foreach ($print_array_keys_array as $key=>$value) {
                $tmp_array["$value"] = $this->print_array["$value"];
            }
            unset($this->print_array);
            $this->print_array = $tmp_array;
        } elseif ($this->config_vars_array["sort"] != "") {
            /* sort according to $sort */
            $sort_array = explode(",", $this->config_vars_array["sort"]);
            foreach ($sort_array as $key=>$value) {
                $value = trim($value);
                $tmp_array["$value"] = $this->print_array["$value"];
            }
            foreach ($this->print_array as $key=>$value) {
                if (!isset($tmp_array["$key"])) {
                    $tmp_array["$key"] = $this->print_array["$key"];
                }
            }
            unset($this->print_array);
            $this->print_array = $tmp_array;
        }

        /* add environmental vars, if set */
        if ($_POST["env_report"] != "") {
            $env_vars_array = explode(",", $_POST["env_report"]);
            foreach ($env_vars_array as $key=>$value) {
                $value = trim($value);
                if (in_array("$value", $this->valid_env)) {
                    $this->print_array["$value"] = $_SERVER["$value"];
                }
            }
        }

        /* remove blank fields, if requested */
        if ($this->config_vars_array["print_blank_fields"] == "0") {
            foreach ($this->print_array as $key=>$value) {
                if ($value == "") {
                    unset($this->print_array["$key"]);
                }
            }
        }
    }

    function check_referer()
    {
        /**
        * check_referer();
        */

        $referer = $_SERVER["HTTP_REFERER"];

        if ($referer == "") {
            $this->error_message = "referer contains an empty value, this is not allowed.<br>\n";
            $this->display_error();
            return false;
        }

        foreach ($this->referers_array as $key=>$value) {
            if ((eregi("^http://$value", $referer)) || (eregi("^https://$value", $referer))) {
                return true;
            } elseif ($value == "") {
                $this->error_message = "referers_array contains an empty value, this is not allowed.<br>\n";
                $this->display_error();
                return false;
            }
        }

        $this->error_message = "<b>$referer</b> is not authorized to use this form.<br>\n";
        $this->display_error();
        return false;
    }

    function check_recipient()
    {
        /**
        * check_recipient();
        */

        $recipient_array = explode(",", $this->config_vars_array["recipient"]);
        foreach ($recipient_array as $key=>$email) {
            $email = trim($email);
            if(!eregi("^[0-9a-z]([-_.+]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,7}$", "$email")) {
                $this->error_message = "Recipient email is either not set, or is not a valid email address.";
                $this->display_error();
                return false;
            }
        }
        return true;
    }

    function check_required_fields()
    {
        /**
        * check_required_fields();
        */

        if ($this->config_vars_array["required"] != "") {
            $required_fields_array = explode(",", "$_POST[required]");
            foreach ($required_fields_array as $key=>$required_field) {
                $required_field = trim($required_field);
                if ($_POST["$required_field"] == "") {
                    $this->display_missing_fields_error();
                    return false;
                }
            }
        }
        return true;
    }

    function send_form()
    {
        /**
        * send_form();
        */

        $mailBody = "";

        foreach ($this->print_array as $key=>$value) {
            if (is_array($value)) {
                foreach ($value as $key2=>$value2) {
                    $mailBody .= "$key: $value2\n";
                }
            } else {
                $mailBody .= "$key: $value\n";
            }
            for ($line_count = 0; $line_count < $this->config_vars_array["line_spacing"]; $line_count++) {
                $mailBody .= "\n";
            }
        }

        $mailHeaders = "From: " . $this->config_vars_array["email"] . "\n";
        
        if($this->config_vars_array["cc_email"] == 1) {
            $mailHeaders .= "Cc: " . $this->config_vars_array["email"] . "\n";
        }

        if(mail($this->config_vars_array["recipient"], $this->config_vars_array["subject"], $mailBody, $mailHeaders)) {
            return true;
        } else {
            return false;
        }
    }


    function display_thankyou()
    {
        /**
        * display_thankyou();
        */

        if($this->config_vars_array["redirect"] == "") {
            $this->display_header();
            print "<p>&nbsp;</p><h1>Thank You</h1>";
            print "<h2>" . $this->config_vars_array["thank_you_message"] . "</h2><br>\n";
            if ($this->config_vars_array["return_link_url"] != "") {
                print "<br>\n";
                print "<a href=\"" . $this->config_vars_array["return_link_url"] . "\">" . $this->config_vars_array["return_link_title"] . "</a><br>\n";
            }
            $this->display_footer();
        } else {
            header("Location: " . $this->config_vars_array["redirect"]);
        }
        exit;
    }

    function display_missing_fields_error()
    {
        /**
        * display_missing_fields_error();
        */

        if($this->config_vars_array["missing_fields_redirect"] == "") {
            $this->display_header();
            print "<h1>S'il vous plait</h1>";
            print $this->config_vars_array["missing_fields_message"];
            $this->display_footer();
        } else {
            header("Location: " . $this->config_vars_array["missing_fields_redirect"]);
        }
        exit;
    }

    function display_error()
    {
        /**
        * display_error();
        */

        $this->display_header();
        print "System error:<br>\n";
        print $this->error_message;
        $this->display_footer();
        exit;
    }

    function display_header()
    {
        /**
        * display_header();
        */

        $bgcolor = $this->config_vars_array["bgcolor"];
        $text_color = $this->config_vars_array["text_color"];
        $background = $this->config_vars_array["background"];
        $link_color = $this->config_vars_array["link_color"];
        $vlink_color = $this->config_vars_array["vlink_color"];
        $alink_color = $this->config_vars_array["alink_color"];

        print "<html>\n";
        print "<head>\n";
        print "<title>$title</title>\n";
        print "</head>\n";
        print "<body text=\"$text_color\" background=\"$background\" bgcolor=\"$bgcolor\" link=\"$link_color\" alink=\"$alink_color\" vlink=\"$vlink_color\">\n";
		print "<div id='border'></div>\n";
		print "<header>
            <h1>Welcome</h1>
            <a href='index.html'>
                <img src='img/logo-gusteaus.png' alt='Gusteaus Logo' class='animated fadeInLeft'>
            </a>
            
            <nav id='main-menu' class='animated fadeInRight'>
                <ul class='menu-left'>
                    <li class='link-effect active'>
                        <a href='index.html'>Home</a>
                    </li>
                    <li class='link-effect'>
                        <a href='about.html'>About</a>
                    </li>
                </ul>
                <ul class='menu-right'>
                    <li class='link-effect'>
                        <a href='menu.html'>Menu</a>
                    </li>
                    <li class='link-effect'>
                        <a href='contact.html'>Contact</a>
                    </li>
                </ul>

            </nav>
            <div class='pretty-divider animated fadeInRight'>
                <span><img class='flourish' src='img/flourish.jpg' alt='flourish' title='flourish'></span>
            </div>
        </header>\n";
		
        print "\n<div align=\"center\">";
    }

    function display_footer()
    {
        /**
        * display_footer();
        */

        print "</div>\n";
		print "<footer id='home-footer' data-sr='enter right' class='clearfix'>
            <div class='pretty-divider'>
                <span><img class='flourish' src='img/flourish.jpg' alt='flourish' title='flourish'></span>
            </div>
            <div class='third'>
                <h3>Phone</h3>
                <p>
                    <a href='tel:1-727-555-1234'>727.555.1234</a>
                </p>
            </div>
            <div class='third'>
                <h3>Address</h3>
                <p>200 Gulf Boulevard<br>
                    Indian Rocks Beach, FL 33785</p>
            </div>
            <div class='third'>
                <h3>Hours</h3>
                <p>Tuesday through Sunday<br>
                    3pm-11pm</p>
            </div>
            
        </footer>
        <div class='copyright'>
            &copy;2015 Gusteau's
        </div>\n";
        print "</body>\n";
        print "</html>\n";
    }

}
?>
