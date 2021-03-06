<?php

/**
 * OpenReviewScript
 *
 * An Open Source Review Site Script
 *
 * @package		OpenReviewScript
 * @subpackage          manager
 * @author		OpenReviewScript.org
 * @copyright           Copyright (c) 2011, OpenReviewScript.org
 * @license		This file is part of OpenReviewScript - free software licensed under the GNU General Public License version 2 - http://OpenReviewScript.org/license
 * @link		http://OpenReviewScript.org
 */
// ------------------------------------------------------------------------

/**    This file is part of OpenReviewScript.
 *
 *    OpenReviewScript is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 2 of the License, or
 *    (at your option) any later version.
 *
 *    OpenReviewScript is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OpenReviewScript.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Reset_password controller class
 *
 * Allows user to reset password
 *
 * @package		OpenReviewScript
 * @subpackage          manager
 * @category            controller
 * @author		OpenReviewScript.org
 * @link		http://OpenReviewScript.org
 */
class Reset_password extends CI_Controller {

    /*
     * Forgot_login controller class constructor
     */

    function Reset_password() {
	parent::__construct();
	$this->load->model('User_model');
	$this->load->library('email');
	// load all settings into an array
	$this->setting = $this->Setting_model->getEverySetting();
    }

    /*
     * do_reset function
     *
     * check the provided key, reset the password and send an email to the user
     */

    function do_reset($key) {
	debug('manager/reset_password page | do_reset function');
	// check key was provided
	if ($key !== '') {
	    // use key to find the user's email address
	    $user_email = $this->User_model->getEmailFromKey($key);
	    if ($user_email) {
		debug('found user\'s email address using key');
		// create a new password for the user
		$newPassword = $this->User_model->resetPassword($key);
		if ($newPassword) {
		    debug('created new password');
		    // send email to the user with the new password
		    $email_message = lang('manager_login_forgot_email_message_2a') . "\n\n";
		    $email_message .= $newPassword . "\n\n";
		    $email_message .= lang('manager_login_forgot_email_message_2b') . ' ' . base_url() . 'manager/login';
		    $this->email->from($this->setting['site_email'], $this->setting['site_name']);
		    $this->email->to($user_email);
		    $this->email->subject(lang('manager_login_forgot_new_password_subject'));
		    $this->email->message($email_message);
		    debug('sending email message to user');
		    if ($this->email->send()) {
			// email sent... display the 'password reset' page
			$data[] = '';
			debug('loading "manager/password_reset" view');
			$sections = array('content' => 'manager/' . $this->setting['current_manager_theme'] . '/template/login/password_reset');
			$this->template->load('manager/' . $this->setting['current_manager_theme'] . '/template/manager_template', $sections, $data);
		    } else {
			debug('error sending email (server error)');
			show_error(lang('error_sending_email'));
			exit;
		    }
		} else {
		    // problem creating password - redirect to
		    debug('problem creating new password - show error');
		    show_error(lang('error_creating_password'));
		    exit;
		}
	    } else {
		// email not found - redirect to log in page
		debug('email addresss not found - redirecting to "manager/home"');
		redirect('/manager/home', '301');
	    }
	} else {
	    // no key - redirect to log in page
	    debug('no key provided - redirecting to "manager/home"');
	    redirect('/manager/home', '301');
	}
    }

}

/* End of file reset_password.php */
/* Location: ./application/controllers/manager/reset_password.php */