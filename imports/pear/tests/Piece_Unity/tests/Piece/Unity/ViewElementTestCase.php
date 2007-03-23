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
 * @version    SVN: $Id: ViewElementTestCase.php 694 2007-01-12 02:13:31Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_ViewElement
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/ViewElement.php';

// {{{ Piece_Unity_ViewElementTestCase

/**
 * TestCase for Piece_Unity_ViewElement
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_ViewElement
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_ViewElementTestCase extends PHPUnit_TestCase
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

    function testSettingElement()
    {
        $viewElement = &new Piece_Unity_ViewElement();
        $viewElement->setElement('foo', 'bar');
        $viewElement->setElement('bar', 'baz');

        $this->assertTrue($viewElement->hasElement('foo'));
        $this->assertTrue($viewElement->hasElement('bar'));

        $elements = $viewElement->getElements();

        $this->assertEquals('bar', $elements['foo']);
        $this->assertEquals('baz', $elements['bar']);
    }

    function testSettingElementByReference()
    {
        $foo = &new stdClass();
        $viewElement = &new Piece_Unity_ViewElement();
        $viewElement->setElementByRef('foo', $foo);
        $foo->bar = 'baz';

        $this->assertTrue($viewElement->hasElement('foo'));

        $elements = $viewElement->getElements();

        $this->assertTrue(array_key_exists('foo', $elements));
        $this->assertTrue(array_key_exists('bar', $elements['foo']));
        $this->assertEquals('baz', $elements['foo']->bar);
    }

    function testGettingElement()
    {
        $element1 = array('foo' => 1, 'bar' => 2, 'baz' => 3);
        $viewElement = &new Piece_Unity_ViewElement();
        $viewElement->setElement('foo', $element1);

        $this->assertTrue($viewElement->hasElement('foo'));

        $element2 = $viewElement->getElement('foo');

        $this->assertEquals($element1, $element2);

        $element2['qux'] = 4;
        $viewElement->setElement('foo', $element2);

        $element3 = $viewElement->getElement('foo');

        $this->assertTrue(array_key_exists('qux', $element3));
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