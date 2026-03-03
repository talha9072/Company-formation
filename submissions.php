<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

/*
|------------------------------------------------------------------
| 1. ENVIRONMENT
|------------------------------------------------------------------
*/
$environment = get_option('ch_environment', 'test');

$presenter_id = trim(($environment === 'live')
    ? get_option('ch_presenter_id_live')
    : get_option('ch_presenter_id_test'));

$password = trim(($environment === 'live')
    ? get_option('ch_auth_code_live')
    : get_option('ch_auth_code_test'));

$gateway_url = 'https://xmlgw.companieshouse.gov.uk/v1-0/xmlgw/Gateway';

/*
|------------------------------------------------------------------
| 2. TABLES
|------------------------------------------------------------------
*/
$saved_table      = $wpdb->prefix . 'saved_companies';
$formation_table  = $wpdb->prefix . 'companyformation';

/*
|------------------------------------------------------------------
| 3. HANDLE SUBMIT ACTION
|------------------------------------------------------------------
*/
if (isset($_GET['action']) && $_GET['action'] === 'submit' && !empty($_GET['token'])) {

    $token = sanitize_text_field($_GET['token']);

    require_once plugin_dir_path(__FILE__) . 'includes/ch-xml-builder.php';

    $company_xml = ch_generate_in01_xml($token);

    if (!$company_xml) {
        echo '<div class="notice notice-error"><p>❌ XML Generation Failed.</p></div>';
    } else {

        /*
        |------------------------------------------------------------------
        | FETCH COMPANY NAME
        |------------------------------------------------------------------
        */
        $formation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$formation_table} WHERE token = %s LIMIT 1",
                $token
            )
        );

        $company_name = 'TEST COMPANY ' . time() . ' LTD';

        if ($formation) {
            $data = maybe_unserialize($formation->data ?? '');
            if (is_array($data) && !empty($data['company_name'])) {
                $company_name = $data['company_name'];
            }
        }

        /*
        |------------------------------------------------------------------
        | GENERATE IDS
        |------------------------------------------------------------------
        */
        $transaction_id   = time() . rand(1000,9999);
        $submission_number = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $sender_id  = md5(strtolower($presenter_id));
        $auth_value = md5(strtolower($password));

        /*
        |------------------------------------------------------------------
        | BUILD FULL GOVTALK XML
        |------------------------------------------------------------------
        */
        $full_xml = '<?xml version="1.0" encoding="UTF-8"?>
<GovTalkMessage xmlns="http://www.govtalk.gov.uk/CM/envelope">
    <EnvelopeVersion>2.0</EnvelopeVersion>

    <Header>
        <MessageDetails>
            <Class>CompanyIncorporation</Class>
            <Qualifier>request</Qualifier>
            <TransactionID>' . esc_xml($transaction_id) . '</TransactionID>
        </MessageDetails>

        <SenderDetails>
            <IDAuthentication>
                <SenderID>' . esc_xml($sender_id) . '</SenderID>
                <Authentication>
                    <Method>clear</Method>
                    <Value>' . esc_xml($auth_value) . '</Value>
                </Authentication>
            </IDAuthentication>
            <EmailAddress>system@test.com</EmailAddress>
        </SenderDetails>
    </Header>

    <Body>
        <FormSubmission
            xmlns="http://xmlgw.companieshouse.gov.uk/Header"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="
                http://xmlgw.companieshouse.gov.uk/Header
                http://xmlgw.companieshouse.gov.uk/v2-1/schema/forms/FormSubmission-v2-11.xsd">

            <FormHeader>
                <CompanyName>' . esc_xml($company_name) . '</CompanyName>
                <PackageReference>' . esc_xml($transaction_id) . '</PackageReference>
                <FormIdentifier>CompanyIncorporation</FormIdentifier>
                <SubmissionNumber>' . esc_xml($submission_number) . '</SubmissionNumber>
                <ContactName>System</ContactName>
                <ContactNumber>00000000000</ContactNumber>
            </FormHeader>

            <DateSigned>' . date('Y-m-d') . '</DateSigned>

            <Form>
                ' . $company_xml . '
            </Form>

        </FormSubmission>
    </Body>
</GovTalkMessage>';

        /*
        |------------------------------------------------------------------
        | DEBUG OUTPUT
        |------------------------------------------------------------------
        */
        echo '<h2>📤 XML Sent</h2>';
        echo '<pre style="background:#fff;padding:15px;border:1px solid #ccc;max-height:400px;overflow:auto;">';
        echo esc_html($full_xml);
        echo '</pre>';

        /*
        |------------------------------------------------------------------
        | SEND REQUEST (NO BASIC AUTH)
        |------------------------------------------------------------------
        */
        $response = wp_remote_post($gateway_url, [
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8'
            ],
            'body'    => $full_xml,
            'timeout' => 60
        ]);

        if (is_wp_error($response)) {

            echo '<div class="notice notice-error"><p>❌ Connection Error: '
                 . esc_html($response->get_error_message()) . '</p></div>';

        } else {

            $body = wp_remote_retrieve_body($response);

            echo '<h2>📥 Raw Response</h2>';
            echo '<pre style="background:#fff;padding:15px;border:1px solid #ccc;max-height:400px;overflow:auto;">';
            echo esc_html($body);
            echo '</pre>';

            if (stripos($body, '<Status>ACCEPTED</Status>') !== false) {

                $wpdb->update(
                    $saved_table,
                    ['status' => 'accepted'],
                    ['formation_token' => $token]
                );

                echo '<div class="notice notice-success"><p>✅ Submission Accepted!</p></div>';

            } else {

                $wpdb->update(
                    $saved_table,
                    ['status' => 'rejected'],
                    ['formation_token' => $token]
                );

                echo '<div class="notice notice-error"><p>❌ Submission Rejected.</p></div>';

                if (preg_match('/<Text>(.*?)<\/Text>/', $body, $matches)) {
                    echo '<h3>Rejection Reason:</h3>';
                    echo '<pre style="background:#fff;padding:10px;border:1px solid #ccc;">'
                        . esc_html($matches[1]) .
                        '</pre>';
                }
            }
        }
    }
}

/*
|------------------------------------------------------------------
| 4. FETCH SUBMISSIONS
|------------------------------------------------------------------
*/
$submissions = $wpdb->get_results("
    SELECT s.id,
           s.formation_token,
           s.status,
           s.created_at,
           f.data
    FROM {$saved_table} s
    LEFT JOIN {$formation_table} f
    ON s.formation_token = f.token
    ORDER BY s.id DESC
");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">NameCheck Submissions</h1>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Company Name</th>
                <th>Status</th>
                <th>Date</th>
                <th width="300">Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php if (!empty($submissions)) : ?>
            <?php foreach ($submissions as $submission) :

                $company_name = '—';

                if (!empty($submission->data)) {
                    $unserialized = maybe_unserialize($submission->data);
                    if (is_array($unserialized)) {
                        $company_name = $unserialized['company_name'] ?? '—';
                    }
                }

                $status = $submission->status ?? 'unknown';
            ?>
                <tr>
                    <td><?php echo esc_html($submission->id); ?></td>
                    <td><strong><?php echo esc_html($company_name); ?></strong></td>
                    <td><?php echo esc_html(ucfirst($status)); ?></td>
                    <td><?php echo esc_html($submission->created_at); ?></td>
                    <td>

                        <?php if ($status === 'saved') : ?>
                            <a href="?page=namecheck-uk-submissions&action=submit&token=<?php echo esc_attr($submission->formation_token); ?>"
                               class="button button-primary">Submit</a>

                        <?php elseif ($status === 'rejected') : ?>
                            <a href="?page=namecheck-uk-submissions&action=submit&token=<?php echo esc_attr($submission->formation_token); ?>"
                               class="button button-primary">Submit Again</a>

                        <?php elseif ($status === 'accepted') : ?>
                            <span style="color:green;font-weight:bold;">Accepted</span>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="5">No submissions found.</td></tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>