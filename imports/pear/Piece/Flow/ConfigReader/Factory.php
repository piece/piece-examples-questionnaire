<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: Factory.php 296 2007-07-01 07:05:23Z iteman $
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Flow/Error.php';
require_once 'Piece/Flow/ClassLoader.php';

// {{{ Piece_Flow_ConfigReader_Factory

/**
 * An factory class for Piece_Flow_Config drivers.
 *
 * @package    Piece_Flow
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.10.0
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
     * @throws PIECE_FLOW_ERROR_NOT_READABLE
     * @throws PIECE_FLOW_ERROR_CANNOT_READ
     * @static
     */
    function &factory($source, $driverName, $cacheDirectory)
    {
        if (is_null($driverName)) {
            $driverName = strtoupper(substr(strrchr($source, '.'), 1));
        }

        if ($driverName == 'XML') {
            if (version_compare(phpversion(), '5.0.0', '>=')) {
                $driverName = 'XML5';
            } else {
                $driverName = 'XML4';
            }
        }

        $class = "Piece_Flow_ConfigReader_$driverName";
        if (!Piece_Flow_ClassLoader::loaded($class)) {
            Piece_Flow_ClassLoader::load($class);
            if (Piece_Flow_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            if (!Piece_Flow_ClassLoader::loaded($class)) {
                Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                       "The class [ $class ] not found in the loaded file."
                                       );
                $return = null;
                return $return;
            }
        }

        $driver = &new $class($source, $cacheDirectory);
        return $driver;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

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
