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
 * @version    SVN: $Id: FactoryTestCase.php 724 2007-02-19 05:44:28Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Plugin_Factory
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/Plugin/Factory.php';
require_once 'Piece/Unity/Error.php';

// {{{ Piece_Unity_Plugin_FactoryTestCase

/**
 * TestCase for Piece_Unity_Plugin_Factory
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Plugin_Factory
 */
class Piece_Unity_Plugin_FactoryTestCase extends PHPUnit_TestCase
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
        Piece_Unity_Plugin_Factory::addPluginDirectory(dirname(__FILE__) . '/FactoryTestCase');
        $this->_oldPluginPrefixes = $GLOBALS['PIECE_UNITY_Plugin_Prefixes'];
    }

    function tearDown()
    {
        $GLOBALS['PIECE_UNITY_Plugin_Prefixes'] = $this->_oldPluginPrefixes;
        Piece_Unity_Plugin_Factory::clearInstances();
        $GLOBALS['PIECE_UNITY_Plugin_Directories'] = $this->_oldPluginDirectories;
        Piece_Unity_Error::clearErrors();
        Piece_Unity_Error::popCallback();
    }

    function testFailureToCreateByNonExistingFile()
    {
        Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));

        Piece_Unity_Plugin_Factory::factory('NonExisting');

        $this->assertTrue(Piece_Unity_Error::hasErrors('exception'));

        $error = Piece_Unity_Error::pop();

        $this->assertEquals(PIECE_UNITY_ERROR_NOT_FOUND, $error['code']);

        Piece_Unity_Error::popCallback();
    }

    function testFailureToCreateByInvalidPlugin()
    {
        Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));

        Piece_Unity_Plugin_Factory::factory('FactoryTestCase_Invalid');

        $this->assertTrue(Piece_Unity_Error::hasErrors('exception'));

        $error = Piece_Unity_Error::pop();

        $this->assertEquals(PIECE_UNITY_ERROR_INVALID_PLUGIN, $error['code']);

        Piece_Unity_Error::popCallback();
    }

    function testFactory()
    {
        $fooPlugin = &Piece_Unity_Plugin_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_a($fooPlugin, 'Piece_Unity_Plugin_FactoryTestCase_Foo'));

        $barPlugin = &Piece_Unity_Plugin_Factory::factory('FactoryTestCase_Bar');

        $this->assertTrue(is_a($barPlugin, 'Piece_Unity_Plugin_FactoryTestCase_Bar'));

        $fooPlugin->baz = 'qux';

        $plugin = &Piece_Unity_Plugin_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(array_key_exists('baz', $fooPlugin));
    }

    /**
     * @since Method available since Release 0.11.0
     */
    function testAlias()
    {
        Piece_Unity_Plugin_Factory::addPluginPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Unity_Plugin_Factory::factory('Foo');

        $this->assertTrue(is_object($foo));
        $this->assertTrue(is_a($foo, 'FactoryTestCaseAlias_Foo'));
    }

    /**
     * @since Method available since Release 0.11.0
     */
    function testAliasWithEmptyPrefix()
    {
        Piece_Unity_Plugin_Factory::addPluginPrefix('');
        $bar = &Piece_Unity_Plugin_Factory::factory('Bar');

        $this->assertTrue(is_object($bar));
        $this->assertTrue(is_a($bar, 'Bar'));
    }

    /**
     * @since Method available since Release 0.11.0
     */
    function testCreateExistingClass()
    {
        Piece_Unity_Plugin_Factory::addPluginPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Unity_Plugin_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_object($foo));
        $this->assertFalse(is_a($foo, 'FactoryTestCaseAlias_FactoryTestCase_Foo'));
        $this->assertTrue(is_a($foo, 'Piece_Unity_Plugin_FactoryTestCase_Foo'));
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