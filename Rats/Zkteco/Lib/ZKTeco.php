<?php

namespace Rats\Zkteco\Lib;

use ErrorException;
use Exception;
use Rats\Zkteco\Lib\Helper\Attendance;
use Rats\Zkteco\Lib\Helper\Device;
use Rats\Zkteco\Lib\Helper\Face;
use Rats\Zkteco\Lib\Helper\Fingerprint;
use Rats\Zkteco\Lib\Helper\Os;
use Rats\Zkteco\Lib\Helper\Pin;
use Rats\Zkteco\Lib\Helper\Platform;
use Rats\Zkteco\Lib\Helper\SerialNumber;
use Rats\Zkteco\Lib\Helper\Ssr;
use Rats\Zkteco\Lib\Helper\Time;
use Rats\Zkteco\Lib\Helper\User;
use Rats\Zkteco\Lib\Helper\Util;
use Rats\Zkteco\Lib\Helper\Connect;
use Rats\Zkteco\Lib\Helper\Version;

class ZKTeco
{
  public $_ip;
  public $_port;
  public $_zkclient;

  public $_data_recv = '';
  public $_session_id = 0;
  public $_section = '';

  /**
   * ZKLib constructor.
   * @param string $ip Device IP
   * @param integer $port Default: 4370
   */
  public function __construct($ip, $port = 4370)
  {
    $this->_ip = $ip;
    $this->_port = $port;

    $this->_zkclient = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    $timeout = array('sec' => 60, 'usec' => 500000);
    socket_set_option($this->_zkclient, SOL_SOCKET, SO_RCVTIMEO, $timeout);
  }

  /**
   * Create and send command to device
   *
   * @param string $command
   * @param string $command_string
   * @param string $type
   * @return bool|mixed
   */
  public function _command($command, $command_string, $type = Util::COMMAND_TYPE_GENERAL)
  {
    $chksum = 0;
    $session_id = $this->_session_id;

    $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6/H2h7/H2h8', substr($this->_data_recv, 0, 8));
    $reply_id = hexdec($u['h8'] . $u['h7']);

    $buf = Util::createHeader($command, $chksum, $session_id, $reply_id, $command_string);

    socket_send($this->_zkclient, $buf, strlen($buf), 0);

    try {
      @socket_recv($this->_zkclient, $this->_data_recv, 1024, 0);

      $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6', substr($this->_data_recv, 0, 8));

      $ret = false;
      $session = hexdec($u['h6'] . $u['h5']);

      if ($type === Util::COMMAND_TYPE_GENERAL && $session_id === $session) {
        $ret = substr($this->_data_recv, 8);
      } else if ($type === Util::COMMAND_TYPE_DATA && !empty($session)) {
        $ret = $session;
      }

      return $ret;
    } catch (ErrorException $e) {
      return false;
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * Connect to device
   *
   * @return bool
   */
  public function connect()
  {
    return socket_connect($this->_zkclient, $this->_ip, $this->_port);
  }

  /**
   * Disconnect from device
   *
   * @return bool
   */
  public function disconnect()
  {
    return socket_close($this->_zkclient);
  }

  // Other methods remain the same...
}
