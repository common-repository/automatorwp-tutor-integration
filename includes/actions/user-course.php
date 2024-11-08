<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\Tutor_LMS\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Tutor_LMS_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'tutor';
    public $action = 'tutor_user_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Enroll user to a course', 'automatorwp-tutor' ),
            'select_option'     => __( 'Enroll user to <strong>a course</strong>', 'automatorwp-tutor' ),
            /* translators: %1$s: Operation (add or remove). %2$s: Post title. */
            'edit_label'        => sprintf( __( 'Enroll user to %1$s', 'automatorwp-tutor' ), '{post}' ),
            /* translators: %1$s: Operation (add or remove). %2$s: Post title. */
            'log_label'         => sprintf( __( 'Enroll user to %1$s', 'automatorwp-tutor' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp-tutor' ),
                    'option_none_label' => __( 'all courses', 'automatorwp-tutor' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp-tutor' ),
                    'post_type'         => apply_filters( 'tutor_course_post_type', 'courses' ),
                ) ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $course_id = $action_options['post'];

        $courses = array();

        // Check specific course
        if( $course_id !== 'any' ) {

            $course = get_post( $course_id );

            // Bail if course doesn't exists
            if( ! $course ) {
                return;
            }

            $courses = array( $course_id );

        } else {
            // If enrolling to all courses, get all courses
            $query = new WP_Query( array(
                'post_type'		=> apply_filters( 'tutor_course_post_type', 'courses' ),
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();
        }

        // Enroll user in courses
        foreach( $courses as $course_id ) {
            tutor_utils()->do_enroll( $course_id, $order_id = 0, $user_id );
        }

    }

}

new AutomatorWP_Tutor_LMS_User_Course();