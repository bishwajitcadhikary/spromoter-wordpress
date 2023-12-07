<?php

use Spromoter\Api\SpromoterApi;

function spromoter_display_register_page() {
	$first_name = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
	$last_name  = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
	$email      = isset( $_POST['email'] ) ? $_POST['email'] : '';
	?>
    <div class="spromoter-wrapper">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="#" class="app-brand-link gap-2">
                                <img src="<?= spromoter_get_logo_url() ?>" alt="Brand Logo" style="width: 100%;">
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">Get started with SPromoter</h4>
                        <p class="mb-4">Make your review management easy!</p>

                        <form id="formAuthentication" class="mb-3" method="POST">
							<?= wp_nonce_field( 'spromoter_registration_form' ); ?>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="first_name"
                                        name="first_name"
                                        placeholder="Enter your first name"
                                        autofocus
                                        required
                                        value="<?= $first_name ?>"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="last_name"
                                        name="last_name"
                                        placeholder="Enter your last name"
                                        required
                                        value="<?= $last_name ?>"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                       placeholder="Enter your email" value="<?= $email ?>" required/>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                            type="password"
                                            id="password"
                                            class="form-control"
                                            name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password"
                                            required
                                            min="8"
                                    />
                                </div>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="confirmation_password">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                            type="password"
                                            id="confirmation_password"
                                            class="form-control"
                                            name="confirmation_password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="confirmation_password"
                                            required
                                            min="8"
                                    />
                                </div>
                            </div>


                            <button class="btn btn-primary d-grid w-100">Sign up</button>
                        </form>

                        <form method="post">
                            <input type="hidden" name="page_type" value="login">
                            <p class="text-center">
                                <span>Already have an account?</span>
                                <button type="submit" class="btn btn-secondary">
                                    Configure Here
                                </button>
                            </p>
                        </form>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
	<?php
}

function spromoter_display_settings_page() {
    $spromoter_settings = get_option( 'spromoter_settings', spromoter_get_default_settings() );

	$app_id = $spromoter_settings['app_id'];
	$api_key = $spromoter_settings['api_key'];
    $order_status = $spromoter_settings['order_status'];
	$disable_native_review_system = $spromoter_settings['disable_native_review_system'];
	?>
    <div class="spromoter-wrapper">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <form id="formAuthentication" class="mb-3" method="POST">
		                    <?= wp_nonce_field( 'spromoter_settings_form' ); ?>
                            <input type="hidden" name="page_type" value="settings">
                            <div class="mb-3">
                                <label for="app_id" class="form-label">APP ID</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="app_id"
                                        name="app_id"
                                        placeholder="Enter app id"
                                        autofocus
                                        required
                                        value="<?= $app_id ?>"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="api_key"
                                        name="api_key"
                                        placeholder="Enter api key"
                                        required
                                        value="<?= $api_key ?>"
                                />
                            </div>

                            <div class="mt-3">
                                <label for="order_status" class="form-label">Order Status</label>
                                <select name="order_status" id="order_status" class="form-select w-100">
                                    <option value="completed" <?= selected('completed', $order_status, false) ?>>Completed</option>
                                    <option value="processing" <?= selected('processing', $order_status, false) ?>>Processing</option>
                                    <option value="on-hold" <?= selected('on-hold', $order_status, false) ?>>On Hold</option>
                                    <option value="canceled" <?= selected('canceled', $order_status, false) ?>>Canceled</option>
                                    <option value="refunded" <?= selected('refunded', $order_status, false) ?>>Refunded</option>
                                    <option value="failed" <?= selected('failed', $order_status, false) ?>>Failed</option>
                                </select>
                            </div>

                            <div class="form-check mt-3">
                                <input
                                        type="checkbox"
                                        name="disable_native_review_system"
                                        id="disable_native_review_system"
                                        class="form-check-input"
                                        value="1"
			                        <?php echo checked($disable_native_review_system) ?>
                                >
                                <label class="form-check-label" for="disable_native_review_system"> Disable native reviews system </label>
                            </div>

                            <button class="btn btn-primary d-grid w-100 mt-3">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function spromoter_save_settings(){
    if (isset($_POST['page_type']) && $_POST['page_type'] == 'settings') {
        check_admin_referer('spromoter_settings_form');
        $spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());
        $spromoter_settings['app_id'] = $_POST['app_id'];
        $spromoter_settings['api_key'] = $_POST['api_key'];
        $spromoter_settings['order_status'] = $_POST['order_status'];
        $spromoter_settings['disable_native_review_system'] = ($_POST['disable_native_review_system'] == '1');
        // Check credentials
        // $spromoter = new SpromoterApi();
        // $authenticated = $spromoter->checkCredentials($_POST['api_key'], $_POST['app_id']);
        // if ($authenticated){
	        update_option('spromoter_settings', $spromoter_settings);
            spromoter_display_messages('Settings saved successfully');
        // }else{
        //     spromoter_display_messages('Please check your api credentials', true);
        // }
    }
}