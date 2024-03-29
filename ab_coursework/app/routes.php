<?php
/**
 * routes.php
 *
 * Script that requires all route scripts located in the routes folder
 * so they can be loaded into the Slim application.
 *
 * Author: Team AB
 * Date: 28/11/2020
 *
 */

require 'routes/homepage.php';
require 'routes/registration.php';
require 'routes/login.php';
require 'routes/loginsubmit.php';
require 'routes/registrationsubmit.php';
require 'routes/menu.php';
require 'routes/sendmessage.php';
require 'routes/downloadmessage.php';
require 'routes/viewmessage.php';
require 'routes/logout.php';
require 'routes/sendmessagesubmit.php';
require 'routes/messagesent.php';
require 'routes/adminmenu.php';
require 'routes/viewallusers.php';
require 'routes/viewallmessages.php';