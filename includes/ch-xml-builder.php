<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('esc_xml')) {
    function esc_xml($string) {
        return htmlspecialchars($string ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}

function ch_generate_in01_xml($token) {

    global $wpdb;

    $formation_table = $wpdb->prefix . 'companyformation';

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

    // Values
    $company_type     = 'BYSHR';
    $country_incorp   = 'EW';

    $premise          = !empty($data['step2_addr_line1'])   ? $data['step2_addr_line1']   : '123 High Street';
    $post_town        = !empty($data['step2_addr_line4'])   ? $data['step2_addr_line4']   : 'London';
    $postcode         = !empty($data['step2_addr_postcode']) ? strtoupper(str_replace(' ', '', $data['step2_addr_postcode'])) : 'EC1A1BB';
    $address_country  = 'GB-ENG';

    $title            = 'Mr';
    $forename         = !empty($data['director_forename']) ? $data['director_forename'] : 'Thomas';
    $surname          = !empty($data['director_surname'])  ? $data['director_surname']  : 'Anderson';

    $dob_year         = !empty($data['director_dob_year'])  ? $data['director_dob_year']  : '1988';
    $dob_month        = !empty($data['director_dob_month']) ? str_pad($data['director_dob_month'], 2, '0', STR_PAD_LEFT) : '08';
    $dob_day          = !empty($data['director_dob_day'])   ? str_pad($data['director_dob_day'],   2, '0', STR_PAD_LEFT) : '15';
    $dob              = "{$dob_year}-{$dob_month}-{$dob_day}";

    $nationality      = 'British';
    $country_res      = 'United Kingdom';

    $sic_code         = '62020';
    $reg_email        = 'thomas.anderson@exampleltd.co.uk';

    // XML with exact schema sequence for Director
    $xml = '<CompanyIncorporation
    xmlns="http://xmlgw.companieshouse.gov.uk"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://xmlgw.companieshouse.gov.uk http://xmlgw.companieshouse.gov.uk/v1-0/schema/forms/CompanyIncorporation-v3-8.xsd">

    <CompanyType>' . esc_xml($company_type) . '</CompanyType>

    <CountryOfIncorporation>' . esc_xml($country_incorp) . '</CountryOfIncorporation>

    <RegisteredOfficeAddress>
        <Premise>' . esc_xml($premise) . '</Premise>
        <PostTown>' . esc_xml($post_town) . '</PostTown>
        <Country>' . esc_xml($address_country) . '</Country>
        <Postcode>' . esc_xml($postcode) . '</Postcode>
    </RegisteredOfficeAddress>

    <DataMemorandum>true</DataMemorandum>

    <Articles>BYSHRMODEL</Articles>

    <RestrictedArticles>false</RestrictedArticles>

    <Appointment>
        <ConsentToAct>true</ConsentToAct>
        <Director>
            <Title>' . esc_xml($title) . '</Title>
            <Forename>' . esc_xml($forename) . '</Forename>
            <Surname>' . esc_xml($surname) . '</Surname>

            <ServiceAddress>
                <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
            </ServiceAddress>

            <DOB>' . esc_xml($dob) . '</DOB>

            <Nationality>' . esc_xml($nationality) . '</Nationality>
            <CountryOfResidence>' . esc_xml($country_res) . '</CountryOfResidence>

            <PreviousNames>
                <CONDate>1900-01-01</CONDate>
                <CompanyName>None</CompanyName>
            </PreviousNames>

            <ResidentialAddress>
                <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
            </ResidentialAddress>

        </Director>
    </Appointment>

    <PSCs>
        <NoPSCStatement>PSC01</NoPSCStatement>
    </PSCs>

    <StatementOfCapital>
        <Capital>
            <TotalAmountUnpaid>0.00</TotalAmountUnpaid>
            <TotalNumberOfIssuedShares>100</TotalNumberOfIssuedShares>
            <ShareCurrency>GBP</ShareCurrency>
            <TotalAggregateNominalValue>100.00</TotalAggregateNominalValue>
            <Shares>
                <ShareClass>Ordinary</ShareClass>
                <PrescribedParticulars>Ordinary shares with full voting rights, equal dividend and capital distribution rights.</PrescribedParticulars>
                <NumShares>100</NumShares>
                <AggregateNominalValue>100.00</AggregateNominalValue>
            </Shares>
        </Capital>
    </StatementOfCapital>

    <Subscribers>
        <Person>
            <Forename>' . esc_xml($forename) . '</Forename>
            <Surname>' . esc_xml($surname) . '</Surname>
        </Person>
        <Address>
            <Premise>' . esc_xml($premise) . '</Premise>
            <PostTown>' . esc_xml($post_town) . '</PostTown>
            <Country>' . esc_xml($address_country) . '</Country>
            <Postcode>' . esc_xml($postcode) . '</Postcode>
        </Address>
        <Authentication>
            <SubscriberAuthentication>SUBSCRIBER_AGREES_NAME_USED_TO_AUTHENTICATE</SubscriberAuthentication>
        </Authentication>
        <Shares>
            <ShareClass>Ordinary</ShareClass>
            <NumShares>100</NumShares>
            <AmountPaidDuePerShare>1.00</AmountPaidDuePerShare>
            <AmountUnpaidPerShare>0.00</AmountUnpaidPerShare>
            <ShareCurrency>GBP</ShareCurrency>
            <ShareValue>1.00</ShareValue>
        </Shares>
        <MemorandumStatement>Each subscriber to this memorandum of association wishes to form a company under the Companies Act 2006 and agrees to become a member of the company and to take at least one share.</MemorandumStatement>
    </Subscribers>

    <Authoriser>
        <Subscribers>
            <Subscriber>
                <Person>
                    <Forename>' . esc_xml($forename) . '</Forename>
                    <Surname>' . esc_xml($surname) . '</Surname>
                </Person>
                <Authentication>
                    <SubscriberAuthentication>SUBSCRIBER_AGREES_NAME_USED_TO_AUTHENTICATE</SubscriberAuthentication>
                </Authentication>
            </Subscriber>
        </Subscribers>
    </Authoriser>

    <SameDay>false</SameDay>

    <SICCodes>
        <SICCode>' . esc_xml($sic_code) . '</SICCode>
    </SICCodes>

    <RegisteredEmailAddress>' . esc_xml($reg_email) . '</RegisteredEmailAddress>

    <AcceptLawfulPurposeStatement>true</AcceptLawfulPurposeStatement>

</CompanyIncorporation>';

    return $xml;
}