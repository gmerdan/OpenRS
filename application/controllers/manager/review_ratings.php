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
 * Review ratings listing controller class
 *
 * Displays a list of review ratings for a review
 *
 * @package		OpenReviewScript
 * @subpackage          manager
 * @category            controller
 * @author		OpenReviewScript.org
 * @link		http://OpenReviewScript.org
 */class Review_ratings extends CI_Controller {

    function Review_ratings() {
	parent::__construct();
	$this->load->model('Rating_model');
	$this->load->model('Review_model');
	$this->load->model('Review_rating_model');
	$this->load->helper('form');
	// load all settings into an array
	$this->setting = $this->Setting_model->getEverySetting();
    }

    /*
     * show function
     *
     * display list of review ratings for a review
     */

    function show($review_id) {
	debug('manager/review_ratings page | show function');
	// check user is logged in with manager level permissions
	$this->secure->allowManagers($this->session);
	if ($review_id) {
	    debug('loaded review');
	    $data['values'] = array('--------', '1', '2', '3', '4', '5');
	    // load a page of review features for this review into an array for displaying in the view
	    $review = $this->Review_model->getReviewById($review_id);
	    if ($review) {
		$data['allreviewratings'] = $this->Review_rating_model->getReviewRatingsForReviewById($review_id, 10, $this->uri->segment(5));
		if ($data['allreviewratings']) {
		    debug('loaded review ratings');
		    $data['review'] = $review;
		    $data['ratings'] = $this->Rating_model->getRatingsDropDown(1);
		    // show the review ratings page
		    debug('loading "manager/review_ratings" view');
		    $sections = array('content' => 'manager/' . $this->setting['current_manager_theme'] . '/template/review_ratings/review_ratings', 'sidebar' => 'manager/' . $this->setting['current_manager_theme'] . '/template/sidebar');
		    $this->template->load('manager/' . $this->setting['current_manager_theme'] . '/template/manager_template', $sections, $data);
		} else {
		    // no data... show the 'no review ratings' page
		    $data['review'] = $review;
		    debug('no review ratings found - loading "manager/no_review_ratings" view');
		    $sections = array('content' => 'manager/' . $this->setting['current_manager_theme'] . '/template/review_ratings/no_review_ratings', 'sidebar' => 'manager/' . $this->setting['current_manager_theme'] . '/template/sidebar');
		    $this->template->load('manager/' . $this->setting['current_manager_theme'] . '/template/manager_template', $sections, $data);
		}
	    } else {
		// no review data... redirect to manager home page
		debug('no review found - redirecting to "manager/home"');
		redirect('/manager/home', '301');
	    }
	} else {
	    // no review id... redirect to manager home page
	    debug('no review if provided - redirecting to "manager/home"');
	    redirect('/manager/home', '301');
	}
    }

    /*
     * edit function
     *
     * modify review ratings for the review id, then display ratings for this review again
     */

    function edit($id) {
	debug('manager/review_ratings page | edit function');
	// check user is logged in with manager level permissions
	$this->secure->allowManagers($this->session);
	$data['review'] = $this->Review_model->getReviewById($id);
	// check form data was submitted
	if ($this->input->post('review_ratings_submit')) {
	    debug('form submitted');
	    $count = $this->input->post('rating_count');
	    for ($index = 0; $index < $count; $index++) {
		$review_rating_id = $this->input->post('review_rating_id' . $index);
		$rating_id = $this->input->post('rating_id' . $index);
		$value = $this->input->post('value' . $index);
		$updateReview_rating = $this->Review_rating_model->updateReviewRating($review_rating_id, $rating_id, $value);
	    }
	    debug('redirecting to "manager/review_ratings/show/"' . $id);
	    redirect('/manager/review_ratings/show/' . $id, '301');
	} else {
	    // form not submitted so redirect back to review_ratings/show
	    debug('form not submitted');
	    debug('redirecting to "manager/review_ratings/show/"' . $id);
	    redirect('/manager/review_ratings/show/' . $id, '301');
	}
    }

}

/* End of file review_ratings.php */
/* Location: ./application/controllers/manager/review_ratings.php */