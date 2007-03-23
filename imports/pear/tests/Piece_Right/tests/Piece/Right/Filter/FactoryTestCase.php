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
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: FactoryTestCase.php 331 2007-02-18 14:59:45Z iteman $
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Filter_Factory
 * @since      File available since Release 0.3.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Filter/Factory.php';
require_once 'Piece/Right/Error.php';

// {{{ Piece_Right_Filter_FactoryTestCase

/**
 * TestCase for Piece_Right_Filter_Factory
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.5.0
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Filter_Factory
 * @since      Class available since Release 0.3.0
 */
class Piece_Right_Filter_FactoryTestCase extends PHPUnit_TestCase
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_oldFilterDirectories;

    /**#@-*/

    /**#@+
     * @access public
     */

    function setUp()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'var_dump($error); return ' . PEAR_ERRORSTACK_DIE . ';'));
        $this->_oldFilterDirectories = $GLOBALS['PIECE_RIGHT_Filter_Directories'];
        Piece_Right_Filter_Factory::addFilterDirectory(dirname(__FILE__) . '/FactoryTestCase');
    }

    function tearDown()
    {
        Piece_Right_Filter_Factory::clearInstances();
        $GLOBALS['PIECE_RIGHT_Filter_Directories'] = $this->_oldFilterDirectories;
        Piece_Right_Error::clearErrors();
        Piece_Right_Error::popCallback();
    }

    function testFailureToCreateByNonExistingFile()
    {
        Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));

        Piece_Right_Filter_Factory::factory('NonExisting');

        $this->assertTrue(Piece_Right_Error::hasErrors('exception'));

        $error = Piece_Right_Error::pop();

        $this->assertEquals(PIECE_RIGHT_ERROR_NOT_FOUND, $error['code']);

        Piece_Right_Error::popCallback();
    }

    function testFactory()
    {
        $fooFilter = &Piece_Right_Filter_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_a($fooFilter, 'Piece_Right_Filter_FactoryTestCase_Foo'));

        $barFilter = &Piece_Right_Filter_Factory::factory('FactoryTestCase_Bar');

        $this->assertTrue(is_a($barFilter, 'Piece_Right_Filter_FactoryTestCase_Bar'));

        $fooFilter->baz = 'qux';

        $filter = &Piece_Right_Filter_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(array_key_exists('baz', $fooFilter));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testAlias()
    {
        Piece_Right_Filter_Factory::addFilterPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Right_Filter_Factory::factory('FooFilter');

        $this->assertTrue(is_object($foo));
        $this->assertTrue(is_a($foo, 'FactoryTestCaseAlias_FooFilter'));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testAliasWithEmptyPrefix()
    {
        Piece_Right_Filter_Factory::addFilterPrefix('');
        $bar = &Piece_Right_Filter_Factory::factory('BarFilter');

        $this->assertTrue(is_object($bar));
        $this->assertTrue(is_a($bar, 'BarFilter'));
    }

    /**
     * @since Method available since Release 1.5.0
     */
    function testCreateExistingClass()
    {
        Piece_Right_Filter_Factory::addFilterPrefix('FactoryTestCaseAlias');
        $foo = &Piece_Right_Filter_Factory::factory('FactoryTestCase_Foo');

        $this->assertTrue(is_object($foo));
        $this->assertFalse(is_a($foo, 'FactoryTestCaseAlias_FactoryTestCase_Foo'));
        $this->assertTrue(is_a($foo, 'Piece_Right_Filter_FactoryTestCase_Foo'));
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