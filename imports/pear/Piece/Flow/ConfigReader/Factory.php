<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Flow
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @author     MIYAI Fumihiko <fumichz@yahoo.co.jp>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: Factory.php 241 2006-10-16 02:29:41Z iteman $
 * @link       http://piece-framework.com/piece-flow/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Flow/Error.php';

// {{{ Piece_Flow_ConfigReader_Factory

/**
 * An factory class for Piece_Flow_Config drivers.
 *
 * @package    Piece_Flow
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @author     MIYAI Fumihiko <fumichz@yahoo.co.jp>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.8.0
 * @link       http://piece-framework.com/piece-flow/
 * @since      Class available since Release 0.1.0
 */
class Piece_Flow_ConfigReader_Factory
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ factory()

    /**
     * Creates a new Piece_Flow_Config driver object from the given source.
     *
     * @param mixed  $source
     * @param string $driverName
     * @param string $cacheDirectory
     * @return mixed
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     * @throws PIECE_FLOW_ERROR_INVALID_DRIVER
     * @static
     */
    function &factory($source, $driverName = null)
    {
        if (is_null($driverName)) {
            $driverName = Piece_Flow_ConfigReader_Factory::_guessDriver($source);
        }

        if ($driverName == 'XML') {
            $driverName = Piece_Flow_ConfigReader_Factory::_getDriverForXML();
        }

        $class = "Piece_Flow_ConfigReader_$driverName";
        if (!class_exists($class)) {
            Piece_Flow_ConfigReader_Factory::_loadDriver($class);
            if (Piece_Flow_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }
        }

        $driver = &new $class($source);
        return $driver;
    }

    /**#@-*/

    /**#@+
     * @access private
     * @static
     */

    // }}}
    // {{{ _guessDriver()

    /**
     * Guesses a driver from the given source.
     *
     * @param mixed $source
     * @return string
     */
    function _guessDriver($source)
    {
        return strtoupper(substr(strrchr($source, '.'), 1));
    }

    // }}}
    // {{{ _getDriverForXML()

    /**
     * Gets an appropriate XML driver according to the version number of PHP.
     *
     * @return string
     */
    function _getDriverForXML()
    {
        if (version_compare(phpversion(), '5.0.0', '>=')) {
            return 'XML5';
        }
        
        return 'XML4';
    }

    // }}}
    // {{{ _loadDriver()

    /**
     * Loads the file corresponding to the given class.
     *
     * @param string $class
     * @return string
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     * @throws PIECE_FLOW_ERROR_INVALID_DRIVER
     */
    function _loadDriver($class)
    {
        $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (!include_once $file) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The driver file [ $file ] not found or was not readable."
                                   );
            return;
        }

        if (!class_exists($class)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_INVALID_DRIVER,
                                   "The driver [ $class ] not defined in the file [ $file ]."
                                   );
        }
    }

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>
