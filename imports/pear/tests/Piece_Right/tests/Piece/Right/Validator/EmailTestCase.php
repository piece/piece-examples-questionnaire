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
 * @version    SVN: $Id: EmailTestCase.php 331 2007-02-18 14:59:45Z iteman $
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Email
 * @since      File available since Release 0.5.0
 */

require_once 'PHPUnit.php';
require_once 'Piece/Right/Validator/Email.php';

// {{{ Piece_Right_Validator_EmailTestCase

/**
 * TestCase for Piece_Right_Validator_Email
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.5.0
 * @link       http://piece-framework.com/piece-right/
 * @see        Piece_Right_Validator_Email
 * @since      Class available since Release 0.5.0
 */
class Piece_Right_Validator_EmailTestCase extends PHPUnit_TestCase
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

    function testSuccess()
    {
        $validator = &new Piece_Right_Validator_Email();

        $this->assertTrue($validator->validate('foo@example.org'));
        $this->assertTrue($validator->validate('foo@ example.org'));
        $this->assertTrue($validator->validate('foo @ example.org'));
        $this->assertTrue($validator->validate('-foo@example.org'));

        $validator = &new Piece_Right_Validator_Email();
        $validator->setRules(array('allowDotBeforeAtmark' => true));

        $this->assertTrue($validator->validate('foo.@example.org'));
        $this->assertTrue($validator->validate('-_-/.@example.org'));
    }

    function testFailure()
    {
        $validator = &new Piece_Right_Validator_Email();

        $this->assertFalse($validator->validate('foo.@example.org'));
        $this->assertFalse($validator->validate('foo'));
        $this->assertFalse($validator->validate('foo bar@example.org'));

        $validator = &new Piece_Right_Validator_Email();
        $validator->setRules(array('allowDotBeforeAtmark' => true));

        $this->assertFalse($validator->validate('foo.@.org'));
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