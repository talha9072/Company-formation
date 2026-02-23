<?php
if (!defined('ABSPATH')) {
    exit;
}

function ch_generate_in01_xml($token) {

    global $wpdb;

    $formation_table = $wpdb->prefix . 'companyformation';
    $officers_table  = $wpdb->prefix . 'companyformation_officers';

    $formation = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$formation_table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    if (!$formation) {
        return false;
    }

    $data = maybe_unserialize($formation->data ?? '');

    $company_name  = esc_xml($data['company_name'] ?? '');
    $company_type  = esc_xml($data['company_type'] ?? '');
    $jurisdiction  = esc_xml($data['jurisdiction'] ?? '');

    $officers = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$officers_table} WHERE token = %s",
            $token
        )
    );

    $xml = '<CompanyIncorporation>';

    $xml .= '<CompanyName>' . $company_name . '</CompanyName>';
    $xml .= '<CompanyType>' . $company_type . '</CompanyType>';
    $xml .= '<Jurisdiction>' . $jurisdiction . '</Jurisdiction>';

    // Registered Office
    $xml .= '<RegisteredOfficeAddress>';
    $xml .= '<AddressLine1>' . esc_xml($formation->step2_addr_line1 ?? '') . '</AddressLine1>';
    $xml .= '<AddressLine2>' . esc_xml($formation->step2_addr_line2 ?? '') . '</AddressLine2>';
    $xml .= '<PostTown>' . esc_xml($formation->step2_addr_line4 ?? '') . '</PostTown>';
    $xml .= '<Country>' . esc_xml($formation->step2_addr_country ?? '') . '</Country>';
    $xml .= '<Postcode>' . esc_xml($formation->step2_addr_postcode ?? '') . '</Postcode>';
    $xml .= '</RegisteredOfficeAddress>';

    // Officers
    if ($officers) {
        foreach ($officers as $officer) {
            if (!empty($officer->role_director)) {
                $xml .= '<Director>';
                $xml .= '<Person>';
                $xml .= '<Title>' . esc_xml($officer->title ?? '') . '</Title>';
                $xml .= '<Forename>' . esc_xml($officer->first_name ?? '') . '</Forename>';
                $xml .= '<Surname>' . esc_xml($officer->last_name ?? '') . '</Surname>';
                $xml .= '<DOB>' . esc_xml($officer->dob ?? '') . '</DOB>';
                $xml .= '<Nationality>' . esc_xml($officer->nationality ?? '') . '</Nationality>';
                $xml .= '</Person>';
                $xml .= '</Director>';
            }

            if (!empty($officer->role_shareholder)) {
                $xml .= '<Subscriber>';
                $xml .= '<Person>';
                $xml .= '<Forename>' . esc_xml($officer->first_name ?? '') . '</Forename>';
                $xml .= '<Surname>' . esc_xml($officer->last_name ?? '') . '</Surname>';
                $xml .= '</Person>';
                $xml .= '<Shares>';
                $xml .= '<ShareClass>' . esc_xml($officer->share_class ?? '') . '</ShareClass>';
                $xml .= '<NumShares>' . esc_xml($officer->share_quantity ?? '0') . '</NumShares>';
                $xml .= '<NominalValue>' . esc_xml($officer->share_price ?? '0') . '</NominalValue>';
                $xml .= '</Shares>';
                $xml .= '</Subscriber>';
            }
        }
    }

    $xml .= '</CompanyIncorporation>';

    return $xml;
}