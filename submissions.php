<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

/*
|--------------------------------------------------------------------------
| 1. ENVIRONMENT
|--------------------------------------------------------------------------
*/
$environment = get_option('ch_environment', 'test');

$presenter_id = ($environment === 'live')
    ? get_option('ch_presenter_id_live')
    : get_option('ch_presenter_id_test');

/*
|--------------------------------------------------------------------------
| 2. TABLES
|--------------------------------------------------------------------------
*/
$saved_table      = $wpdb->prefix . 'saved_companies';
$formation_table  = $wpdb->prefix . 'companyformation';

/*
|--------------------------------------------------------------------------
| 3. FETCH SUBMISSIONS
|--------------------------------------------------------------------------
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

    <p><strong>Environment:</strong> <?php echo esc_html(strtoupper($environment)); ?></p>
    <p><strong>Presenter ID:</strong> <?php echo esc_html($presenter_id); ?></p>

    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th>ID</th>
                <th>Company Name</th>
                <th>Submission No.</th>
                <th>Status</th>
                <th>Date</th>
                <th width="260">Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($submissions)) : ?>

            <?php foreach ($submissions as $submission) : ?>

                <?php
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

                    <td>
                        <strong><?php echo esc_html($company_name); ?></strong>
                    </td>

                    <td>—</td>

                    <td>
                        <?php
                        switch ($status) {
                            case 'saved':
                                echo '<span style="color:#dba617;font-weight:600;">Saved</span>';
                                break;
                            case 'accepted':
                                echo '<span style="color:green;font-weight:600;">Accepted</span>';
                                break;
                            case 'rejected':
                                echo '<span style="color:red;font-weight:600;">Rejected</span>';
                                break;
                            default:
                                echo esc_html(ucfirst($status));
                        }
                        ?>
                    </td>

                    <td><?php echo esc_html($submission->created_at); ?></td>

                    <td>

                        <!-- EDIT -->
                        <a href="?page=namecheck-uk-submissions&action=edit&token=<?php echo esc_attr($submission->formation_token); ?>"
                           class="button button-primary">
                            Edit
                        </a>

                        <!-- DETAILS -->
                        <a href="?page=namecheck-uk-submissions&action=details&token=<?php echo esc_attr($submission->formation_token); ?>"
                           class="button">
                            Details
                        </a>

                        <!-- SUBMIT (ALLOWED FOR SAVED + REJECTED) -->
                        <?php if ($status !== 'accepted') : ?>
                            <a href="?page=namecheck-uk-submissions&action=submit&token=<?php echo esc_attr($submission->formation_token); ?>"
                               class="button button-secondary"
                               onclick="return confirm('Are you sure you want to submit this to Companies House?');">
                                Submit
                            </a>
                        <?php endif; ?>

                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else : ?>

            <tr>
                <td colspan="6">No submissions found.</td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>
</div>
