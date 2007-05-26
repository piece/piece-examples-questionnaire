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
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: CommonTestCase.php 748 2007-03-08 18:09:40Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Plugin_Common
 * @since      File available since Release 0.12.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/Plugin/Factory.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Unity/Config.php';

// {{{ Piece_Unity_Plugin_CommonTestCase

/**
 * TestCase for Piece_Unity_Plugin_Common
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.12.0
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Plugin_Common
 * @since      Class available since Release 0.12.0
 */
class Piece_Unity_Plugin_CommonTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_oldPluginDirectories;
    var $_oldPluginPrefixes;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Unity_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_oldPluginDirectories = $GLOBALS['PIECE_UNITY_Plugin_Directories'];
        Piece_Unity_Plugin_Factory::addPluginDirectory(dirname(__FILE__) . '/CommonTestCase');
        $this->_oldPluginPrefixes = $GLOBALS['PIECE_UNITY_Plugin_Prefixes'];
    }

    function tearDown()
    {
        Piece_Unity_Context::clear();
        $GLOBALS['PIECE_UNITY_Plugin_Prefixes'] = $this->_oldPluginPrefixes;
        Piece_Unity_Plugin_Factory::clearInstances();
        $GLOBALS['PIECE_UNITY_Plugin_Directories'] = $this->_oldPluginDirectories;
        Piece_Unity_Error::clearErrors();
        Piece_Unity_Error::popCallback();
    }

    function testCannotGetConfigurationWithPluginPrefix()
    {
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Foo', 'bar', 'baz');
        $config->setExtension('Foo', 'baz', 'Qux');
        $config->setConfiguration('CannotGetConfigurationWithPluginPrefixFoo', 'bar', 'baz');
        $config->setExtension('CannotGetConfigurationWithPluginPrefixFoo', 'baz', 'CannotGetConfigurationWithPluginPrefixQux');
        $context = &Piece_Unity_Context::singleton();
        $context->setConfiguration($config);
        Piece_Unity_Plugin_Factory::addPluginPrefix('CommonTestCaseAlias');
        Piece_Unity_Plugin_Factory::addPluginPrefix('');

        $foo = &Piece_Unity_Plugin_Factory::factory('Foo');
        $foo->invoke();

        $this->assertEquals('baz', $foo->_bar);
        $this->assertEquals(strtolower('CommonTestCaseAlias_Qux'), strtolower(get_class($foo->_baz)));

        $empty = &Piece_Unity_Plugin_Factory::factory('CannotGetConfigurationWithPluginPrefixFoo');
        $empty->invoke();

        $this->assertEquals('baz', $empty->_bar);
        $this->assertEquals(strtolower('CannotGetConfigurationWithPluginPrefixQux'), strtolower(get_class($empty->_baz)));
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