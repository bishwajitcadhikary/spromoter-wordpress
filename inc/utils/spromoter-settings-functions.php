<?php

use Spromoter\Api\SpromoterApi;

function spromoter_display_register_page()
{
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    ?>
    <div class="spromoter-container">
        <div class="authentication-wrapper">
            <div class="authentication-inner">
                <div class="spromoter-settings-card">
                    <div class="card-body">
                        <!-- Logo -->
                        <a href="#" class="spromoter-brand">
                            <img src="<?= spromoter_get_image_url('logo.png') ?>" alt="SPromoter">
                        </a>
                        <h2 class="mb-2">Get started with SPromoter</h2>
                        <p class="mb-4">Make your review management easy!</p>

                        <form id="spromoterRegisterForm" method="POST">
                            <?= wp_nonce_field('spromoter_registration_form'); ?>
                            <input type="hidden" name="page_type" value="register">
                            <div class="mb-3">
                                <label for="first_name" class="spromoter-form-label mb-2">First Name</label>
                                <input
                                        type="text"
                                        class="spromoter-form-input"
                                        id="first_name"
                                        name="first_name"
                                        placeholder="Enter your first name"
                                        autofocus
                                        required
                                        value="<?= $first_name ?>"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="spromoter-form-label mb-2">Last Name</label>
                                <input
                                        type="text"
                                        class="spromoter-form-input"
                                        id="last_name"
                                        name="last_name"
                                        placeholder="Enter your last name"
                                        required
                                        value="<?= $last_name ?>"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="spromoter-form-label mb-2">Email</label>
                                <input type="text" class="spromoter-form-input" id="email" name="email"
                                       placeholder="Enter your email" value="<?= $email ?>" required/>
                            </div>

                            <div class="form-password-toggle mb-3">
                                <label class="spromoter-form-label mb-2" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                            type="password"
                                            id="password"
                                            class="spromoter-form-input"
                                            name="password"
                                            placeholder=""
                                            aria-describedby="password"
                                            required
                                            min="8"
                                    />
                                </div>
                            </div>

                            <div class="form-password-toggle mb-4">
                                <label class="spromoter-form-label mb-2" for="password_confirmation">Confirm
                                    Password</label>
                                <div class="input-group input-group-merge">
                                    <input
                                            type="password"
                                            id="password_confirmation"
                                            class="spromoter-form-input"
                                            name="password_confirmation"
                                            placeholder=""
                                            aria-describedby="password_confirmation"
                                            required
                                            min="8"
                                    />
                                </div>
                            </div>

                            <button class="spromoter-button mb-3 w-100">Sign up</button>
                        </form>

                        <form method="post">
                            <input type="hidden" name="page_type" value="login">
                            <p class="text-center">
                                <span>Already have an account?</span>
                                <button type="submit" class="spromoter-button-link">
                                    Configure Here
                                </button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function spromoter_display_settings_page()
{
    $spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());

    $app_id = $spromoter_settings['app_id'];
    $api_key = $spromoter_settings['api_key'];
    $order_status = $spromoter_settings['order_status'];
    $disable_native_review_system = $spromoter_settings['disable_native_review_system'];
    $enable_bottom_line_widget = $spromoter_settings['show_bottom_line_widget'];
    ?>
    <div class="spromoter-container">
        <div class="spromoter-authentication-bg-shape">
            <img src="<?= spromoter_get_image_url('shape.png') ?>" alt="SPromoter">
        </div>

        <div class="authentication-wrapper">
            <div class="authentication-inner">
                <div class="spromoter-settings-card">
                    <div class="card-body">
                        <!-- Logo -->
                        <a href="#" class="spromoter-brand">
                            <img src="<?= spromoter_get_image_url('logo.png') ?>" alt="SPromoter">
                        </a>
                        <h2 class="mb-4">Configure your settings!</h2>

                        <form id="spromoterSettingsForm" method="POST">
                            <?= wp_nonce_field('spromoter_settings_form'); ?>
                            <input type="hidden" name="page_type" value="settings">

                            <div class="mb-3">
                                <label for="app_id" class="spromoter-form-label mb-2">APP ID</label>
                                <input
                                        type="text"
                                        class="spromoter-form-input"
                                        id="app_id"
                                        name="app_id"
                                        placeholder="Enter app id"
                                        autofocus
                                        required
                                        value="<?= $app_id ?>"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="api_key" class="spromoter-form-label mb-2">API Key</label>
                                <input
                                        type="text"
                                        class="spromoter-form-input"
                                        id="api_key"
                                        name="api_key"
                                        placeholder="Enter api key"
                                        required
                                        value="<?= $api_key ?>"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="order_status" class="spromoter-form-label mb-2">Order Status</label>
                                <select name="order_status" id="order_status"
                                        class="spromoter-form-input spromoter-form-select">
                                    <option value="completed" <?= selected('completed', $order_status, false) ?>>
                                        Completed
                                    </option>
                                    <option value="processing" <?= selected('processing', $order_status, false) ?>>
                                        Processing
                                    </option>
                                    <option value="on-hold" <?= selected('on-hold', $order_status, false) ?>>On Hold
                                    </option>
                                    <option value="canceled" <?= selected('canceled', $order_status, false) ?>>
                                        Canceled
                                    </option>
                                    <option value="refunded" <?= selected('refunded', $order_status, false) ?>>
                                        Refunded
                                    </option>
                                    <option value="failed" <?= selected('failed', $order_status, false) ?>>Failed
                                    </option>
                                </select>
                            </div>

                            <div class="spromoter-form-check ps-0 mb-3">
                                <input
                                        type="checkbox"
                                        name="disable_native_review_system"
                                        id="disable_native_review_system"
                                        class="spromoter-form-check-input"
                                        value="1"
                                    <?php echo checked($disable_native_review_system) ?>
                                >
                                <label class="spromoter-form-check-label" for="disable_native_review_system"> Disable
                                    native reviews system </label>
                            </div>

                            <div class="spromoter-form-check ps-0 mb-4">
                                <input
                                        type="checkbox"
                                        name="show_bottom_line_widget"
                                        id="show_bottom_line_widget"
                                        class="spromoter-form-check-input"
                                        value="1"
                                    <?php echo checked($enable_bottom_line_widget) ?>
                                >
                                <label class="spromoter-form-check-label" for="show_bottom_line_widget"> Enable button
                                    line in product page </label>
                            </div>
                        </form>

                        <form method="POST" id="spromoterExportForm">
                            <?= wp_nonce_field('spromoter_export_form'); ?>
                            <input type="hidden" name="export_reviews" value="true">
                        </form>

                        <div class="spromoter-button-group">
                            <button type="submit" class="spromoter-secondary-button" form="spromoterExportForm">Export
                                Reviews
                            </button>
                            <button type="submit" class="spromoter-button" form="spromoterSettingsForm">Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function spromoter_save_settings()
{
    if (isset($_POST['page_type']) && $_POST['page_type'] == 'settings') {
        check_admin_referer('spromoter_settings_form');
        $spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());
        $spromoter_settings['app_id'] = $_POST['app_id'];
        $spromoter_settings['api_key'] = $_POST['api_key'];
        $spromoter_settings['order_status'] = $_POST['order_status'];
        $spromoter_settings['disable_native_review_system'] = ($_POST['disable_native_review_system'] == '1');
        $spromoter_settings['show_bottom_line_widget'] = ($_POST['show_bottom_line_widget'] == '1');

        // Check credentials
        $spromoter = new SpromoterApi();
        $authenticated = $spromoter->checkCredentials($_POST['api_key'], $_POST['app_id']);
        if ($authenticated) {
            update_option('spromoter_settings', $spromoter_settings);
            spromoter_display_messages('Settings saved successfully');
        } else {
            spromoter_display_messages('Please check your api credentials', true);
        }
    }
}

function spromoter_register_user()
{
    if (isset($_POST['page_type']) && $_POST['page_type'] == 'register') {
        check_admin_referer('spromoter_registration_form');
        $spromoter_settings = get_option('spromoter_settings', spromoter_get_default_settings());

        // Check credentials
        $spromoter = new SpromoterApi();
        $response = $spromoter->registerUser(array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'password_confirmation' => $_POST['password_confirmation'],
            'store_url' => get_site_url(),
            'store_name' => get_bloginfo('name'),
            'store_logo' => ''
        ));

        if ($response) {
            $spromoter_settings['app_id'] = $response['app_id'];
            $spromoter_settings['api_key'] = $response['api_key'];

            update_option('spromoter_settings', $spromoter_settings);
            spromoter_display_messages('User registered successfully');

            return true;
        } else {
            spromoter_display_messages('Please check your credentials', true);
            return false;
        }
    }
}