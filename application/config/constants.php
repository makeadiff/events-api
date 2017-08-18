<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);
define('PAGINATION_CONSTANT', '15');
define('PAGINATION_PCKAGE_CONSTANT', '12');
define('STATUS', '0');
define('ACTIVE', '1');
define('INACTIVE', '0');
define('DISABLED', '2');
define('DEMO', '5');
define('FAILD', '0');
define('DEMO_PACK', '5');
define('DELETE', '1');
define('MAIN_ATTCH', '1');
define('LOGO', '2');

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
define('FAILED', '0');
define('ALREADY', '01');
define('MAIN_ATTACH', '1');
define('SUB_ATTACH', '0');
/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
define('SHOW_DEBUG_BACKTRACE', TRUE);
/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */


/*define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
define('LIMIT', 1);
define('AGENTS', 'Mozilla');
define('EXIST', 1);
define('NOT_EXIST', 2);
define('APP_KEY', '489');
define('SUCCESS', '1');
define('NOUPDATE', '1');
define('NOCHANGE', 'noupdation');
define('INITIAL_PAGE_NO', '0');
define('SI_NO', '1');
define('NOT_PRESENT', '0');
define('VALID_AGENTS', '1');
define('API_FAILED', '2');
define('VALID_USER', '1');
define('NO_ACTIVE_PACK', '3');
define('INVALID_USER', '4');
define('MAIN', '1');
define('USER_INACTIVE', '1');
define('TIME_EXPIRED', '6');
define('GREATER_AMOUNT', '7');
define('ACTIVE_STATUS', '0');
define('BID_EXIST', '8');
define('ZERO_BID', '9');
define('NO_UPDATES', '2');
define('UPDATED', '1');
define('MOBILE', '1');
define('PUSH_ACTIVE_PACK', '1');
define('PUSH_WINNER', '0');
define('ACTIVE_PUSH_USER', '0');
define('INVALID_PUSH_USER', '1');
define('PUSH_WINNERS', '0');
define('PUSH_NEW_PACK', '1');
define('ACTIVEUSER', '1');
define('WINNINGUSER', '0');
define('ADMIN_ADD', '1');
define('USER_ADD', '0');
define('NOT_EXISTS', '0');
define('PACKET', '500');
define('INSTANCE', '1');
define('START_INSTANCE', '0');
define('NEXT_ID', '1');
define('REGISTERD', '1');
define('UNREGISTERD', '2');
define('UPDATE_GCM_STAT', '1');
define('REGISTERD_NEVER_BID','3');
define('ALL_USERS','4');

define('REG_SUCC_OTP_NOT_VARI', '1');
define('REG_SUCC_OTP_VARI', '2');
define('REG_FAILED', '3');
define('INVALID_MOB_NUM', '4');
define('REG_ALRDY_SUCC_OTP_NOT_VARI', '5');
define('MOB_NUM_ALRDY_EXIST', '6');
define('IMEI_NUM_ALRDY_EXIST', '7');

define('FIRST_INSTLN_POINT_ID', '1');
define('REGSTRN_POINT_ID', '2');
define('BIDDING_POINT_ID', '3');
define('LOSS_BID_POINT_ID', '4');
define('REFER_FRND_POINT_ID', '5');

define('TBL_STATUS', '1');
define('BID_LOSS_NOT_DISP', '0');
define('BID_LOSS_DISP', '1');

define('INCORRECT_OTP', '2');
define('FAILED_ACTION', '3');

define('PUSH_NOTI_TITLE', 'Go Kerala');
define('NOT_APPLBLE_TEXT', 'NA');

define('LOGO_ATTACH', '2');
define('REFERRED_FRND_MSG', 'Your friend installed the app, here is 500 GoKerala points for you');
define('LATEST_APP_VERSION', '2.1.0');*/

/*
 * Start by Rejeesh K.Nair 
 */
define('VALIDATION_FAILED', '2');
define('ACTIVEUSER', '1');
define('VALID_AGENTS', '1');
define('LOGIN_SUCCESS', '1');
define('LOGIN_FAILURE', '0');
define('INVALID_CREDENTIALS', '5');
define('INVALID_EMAIL', '3');
define('INVALID_PASSWORD', '4');
define('NULL_CREDENTIALS', '6');
define('ALREADY_EXIST', '7');

define('NATIONAL_DIRECTOR', '339');
define('CITY_MANAGER', '9');
define('VOLUNTEER', '5');

define('DB_VER_DEF_CNT', '0');
define('SUCCESS', '1');
define('FAILURE', '0');
define('ACTIVEEVNT', '1');
define('INACTIVEEVNT', '0');
define('INVITED_EVENT', '1');
define('POSTED_EVENT', '2');
define('USER_CITY_EVENT', '3');
define('GO', '1');
define('MYBEGO', '2');
define('CANTGO', '3');
define('PAGINATION_CONST', '15');
define('APP', '2');
define('MARK_ATTENDANCE', '2');
define('RSVP', '3');
define('ON', '1');
define('OFF', '0');
define('DEFAULT_STATUS', '0');
define('ATTENDED', '1');
define('LATE', '2');
define('MISSED', '3');
// push notification type
define('TYPE_INVITE', '1');
define('TYPE_UPDATE', '2');
define('TYPE_CANCEL', '3');
define('NO_CITY_MEMBS', '2');

//define('ACTIVE', '1');

/*
 * End by Rejeesh K.Nair 
 */
