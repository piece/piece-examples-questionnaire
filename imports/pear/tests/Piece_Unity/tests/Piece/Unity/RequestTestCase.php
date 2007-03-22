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
 * @version    SVN: $Id: RequestTestCase.php 694 2007-01-12 02:13:31Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Request
 * @since      File available since Release 0.1.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Unity/Request.php';

// {{{ Piece_Unity_RequestTestCase

/**
 * TestCase for Piece_Unity_Request
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Unity_Request
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_RequestTestCase extends PHPUnit_TestCase
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

    function testSettingParameter()
    {
        $request = &new Piece_Unity_Request();
        $request->setParameter('foo', 'bar');
        $request->setParameter('bar', 'baz');

        $this->assertEquals('bar', $request->getParameter('foo'));
        $this->assertEquals('baz', $request->getParameter('bar'));
    }

    function testCheckingParameter()
    {
        $request = &new Piece_Unity_Request();
        $request->setParameter('foo', 'bar');
        $request->setParameter('bar', 'baz');

        $this->assertTrue($request->hasParameter('foo'));
        $this->assertTrue($request->hasParameter('bar'));
        $this->assertFalse($request->hasParameter('baz'));
    }

    function testImportingParameters()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'bar';
        $_GET['bar'] = 'baz';

        $request = &new Piece_Unity_Request();

        $this->assertEquals('bar', $request->getParameter('foo'));
        $this->assertEquals('baz', $request->getParameter('bar'));

        unset($_SERVER['REQUEST_METHOD']);
        unset($_GET['foo']);
        unset($_GET['bar']);
    }

    function testImportingPathinfo()
    {
        $_SERVER['PATH_INFO'] = '/foo/bar/bar/baz/qux';

        $request = &new Piece_Unity_Request();
        $request->importPathInfo();

        $this->assertEquals('bar', $request->getParameter('foo'));
        $this->assertEquals('baz', $request->getParameter('bar'));
        $this->assertNull($request->getParameter('qux'));

        unset($_SERVER['PATH_INFO']);
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
