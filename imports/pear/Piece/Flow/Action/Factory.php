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
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: Factory.php 262 2006-12-06 16:40:37Z iteman $
 * @link       http://piece-framework.com/piece-flow/
 * @since      File available since Release 1.0.0
 */

require_once 'Piece/Flow/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_FLOW_Action_Instances'] = array();
$GLOBALS['PIECE_FLOW_Action_Directory'] = null;

// }}}
// {{{ Piece_Flow_Action_Factory

/**
 * A factory class for creating action objects.
 *
 * @package    Piece_Flow
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.8.0
 * @link       http://piece-framework.com/piece-flow/
 * @since      Class available since Release 1.0.0
 */
class Piece_Flow_Action_Factory
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
     * Creates an action object from a configuration file or a cache.
     *
     * @param string $class
     * @return mixed
     * @throws PIECE_FLOW_ERROR_NOT_GIVEN
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     * @throws PIECE_FLOW_ERROR_NOT_READABLE
     * @static
     */
    function &factory($class)
    {
        if (!array_key_exists($class, $GLOBALS['PIECE_FLOW_Action_Instances'])) {
            Piece_Flow_Action_Factory::load($class);
            if (Piece_Flow_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            $instance = &new $class();
            $GLOBALS['PIECE_FLOW_Action_Instances'][$class] = &$instance;
        }

        return $GLOBALS['PIECE_FLOW_Action_Instances'][$class];
    }

    // }}}
    // {{{ setActionDirectory()

    /**
     * Sets a directory as the action directory.
     *
     * @param string $directory
     */
    function setActionDirectory($directory)
    {
        $GLOBALS['PIECE_FLOW_Action_Directory'] = $directory;
    }

    // }}}
    // {{{ clearInstances()

    /**
     * Clears the action instances.
     */
    function clearInstances()
    {
        $GLOBALS['PIECE_FLOW_Action_Instances'] = array();
    }

    // }}}
    // {{{ getInstances()

    /**
     * Gets the action instances.
     *
     * @return array
     */
    function getInstances()
    {
        return $GLOBALS['PIECE_FLOW_Action_Instances'];
    }

    // }}}
    // {{{ setInstances()

    /**
     * Sets an array as the action instances.
     *
     * @param array $instances
     */
    function setInstances($instances)
    {
        $GLOBALS['PIECE_FLOW_Action_Instances'] = $instances;
    }

    // }}}
    // {{{ load()

    /**
     * Loads an action class corresponding to the given class name.
     *
     * @param string $class
     * @throws PIECE_FLOW_ERROR_NOT_GIVEN
     * @throws PIECE_FLOW_ERROR_NOT_FOUND
     * @throws PIECE_FLOW_ERROR_NOT_READABLE
     * @static
     */
    function load($class)
    {
        if (Piece_Flow_Action_Factory::_loaded($class)) {
            return;
        }

        if (is_null($GLOBALS['PIECE_FLOW_Action_Directory'])) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_GIVEN,
                                   'The action directory was not given.'
                                   );
            return;
        }

        $file = "{$GLOBALS['PIECE_FLOW_Action_Directory']}/" . str_replace('_', '/', $class) . '.php';

        if (!file_exists($file)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The action file [ $file ] for the class [ $class ] not found."
                                   );
            return;
        }

        if (!is_readable($file)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_READABLE,
                                   "The action file [ $file ] was not readable."
                                   );
            return;
        }

        if (!include_once $file) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The action file [ $file ] not found or was not readable."
                                   );
            return;
        }

        if (!Piece_Flow_Action_Factory::_loaded($class)) {
            Piece_Flow_Error::push(PIECE_FLOW_ERROR_NOT_FOUND,
                                   "The action [ $class ] not defined in the file [ $file ]."
                                   );
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _loaded()

    /**
     * Returns whether the given class has already been loaded or not.
     *
     * @param string $class
     * @return boolean
     * @static
     */
    function _loaded($class)
    {
        if (version_compare(phpversion(), '5.0.0', '<')) {
            return class_exists($class);
        } else {
            return class_exists($class, false);
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
