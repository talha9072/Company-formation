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

    /*
    |--------------------------------------------------------------------------
    | COMPANY TYPE MAPPING (ENUM SAFE)
    |--------------------------------------------------------------------------
    */

    $raw_company_type = strtolower(trim($data['company_type'] ?? ''));

    switch ($raw_company_type) {
        case 'limited by shares':
        case 'private limited by shares':
            $company_type = 'BYSHR';
            break;

        case 'limited by guarantee':
        case 'private limited by guarantee':
            $company_type = 'BYGUAR';
            break;

        case 'public limited company':
        case 'plc':
            $company_type = 'PLC';
            break;

        default:
            $company_type = 'BYSHR';
    }

    /*
    |--------------------------------------------------------------------------
    | COUNTRY OF INCORPORATION (ENUM SAFE)
    |--------------------------------------------------------------------------
    */

    $raw_country = strtoupper(trim($data['jurisdiction'] ?? ''));

    switch ($raw_country) {
        case 'SC':
        case 'SCOTLAND':
            $country = 'SC';
            break;

        case 'NI':
        case 'NORTHERN IRELAND':
            $country = 'NI';
            break;

        case 'WA':
        case 'WALES':
            $country = 'WA';
            break;

        default:
            $country = 'EW';
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTERED OFFICE COUNTRY (ISO CODE REQUIRED)
    |--------------------------------------------------------------------------
    */

    $raw_address_country = strtoupper(trim($formation->step2_addr_country ?? ''));

    switch ($raw_address_country) {
        case 'UNITED KINGDOM':
        case 'UK':
        case 'ENGLAND':
            $address_country = 'GB-ENG';
            break;

        case 'SCOTLAND':
            $address_country = 'GB-SCT';
            break;

        case 'WALES':
            $address_country = 'GB-WLS';
            break;

        case 'NORTHERN IRELAND':
            $address_country = 'GB-NIR';
            break;

        default:
            $address_country = 'GB-ENG';
    }

    /*
    |--------------------------------------------------------------------------
    | START XML
    |--------------------------------------------------------------------------
    */

    $xml = '
<CompanyIncorporation
    xmlns="http://xmlgw.companieshouse.gov.uk"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="
        http://xmlgw.companieshouse.gov.uk
        http://xmlgw.companieshouse.gov.uk/v1-0/schema/forms/CompanyIncorporation-v3-8.xsd">

    <CompanyType>' . esc_xml($company_type) . '</CompanyType>
    <CountryOfIncorporation>' . esc_xml($country) . '</CountryOfIncorporation>

    <RegisteredOfficeAddress>
        <Premise>' . esc_xml($formation->step2_addr_line1 ?? '1') . '</Premise>
        <PostTown>' . esc_xml($formation->step2_addr_line4 ?? 'London') . '</PostTown>
        <Country>' . esc_xml($address_country) . '</Country>
        <Postcode>' . esc_xml($formation->step2_addr_postcode ?? 'SW1A1AA') . '</Postcode>
    </RegisteredOfficeAddress>

    <DataMemorandum>true</DataMemorandum>
    <Articles>BYSHRMODEL</Articles>
    <RestrictedArticles>false</RestrictedArticles>

    <!-- REQUIRED: Appointment BEFORE StatementOfCapital -->
    <Appointment>
    <ConsentToAct>true</ConsentToAct>
    <Director>
        <Person>

            <Title>Mr</Title>
            <Forename>John</Forename>
            <OtherForenames>NA</OtherForenames>
            <Surname>Doe</Surname>

            <ServiceAddress>
                <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
            </ServiceAddress>

            <DOB>1990-01-01</DOB>
            <Nationality>British</Nationality>
            <CountryOfResidence>United Kingdom</CountryOfResidence>

            <ResidentialAddress>
                <SameAsServiceAddress>true</SameAsServiceAddress>
            </ResidentialAddress>

            <VerificationDetails>
                <CompaniesHousePersonalCode>12345678901</CompaniesHousePersonalCode>
                <VerificationStatements>
                    <VerificationStatementForIndividual>
                        INDIVIDUAL_VERIFIED
                    </VerificationStatementForIndividual>
                </VerificationStatements>
            </VerificationDetails>

        </Person>
    </Director>
</Appointment>

    <StatementOfCapital>
        <Capital>
            <TotalAmountUnpaid>0.00</TotalAmountUnpaid>
            <TotalNumberOfIssuedShares>1</TotalNumberOfIssuedShares>
            <ShareCurrency>GBP</ShareCurrency>
            <TotalAggregateNominalValue>1.00</TotalAggregateNominalValue>

            <Shares>
                <ShareClass>ORDINARY</ShareClass>
                <PrescribedParticulars>Each share carries one vote and equal dividend rights.</PrescribedParticulars>
                <NumShares>1</NumShares>
                <AggregateNominalValue>1.00</AggregateNominalValue>
            </Shares>
        </Capital>
    </StatementOfCapital>

    <Subscribers>
        <Person>
            <Forename>John</Forename>
            <Surname>Doe</Surname>
        </Person>

        <Address>
            <Premise>1</Premise>
            <PostTown>London</PostTown>
            <Country>' . esc_xml($address_country) . '</Country>
            <Postcode>SW1A1AA</Postcode>
        </Address>

        <Authentication>
            <MemorandumPersonalAuthentication>
                SUBSCRIBER_AGREES_NAME_USED_TO_AUTHENTICATE
            </MemorandumPersonalAuthentication>
        </Authentication>

        <Shares>
            <ShareClass>ORDINARY</ShareClass>
            <NumShares>1</NumShares>
            <AmountPaidDuePerShare>1.00</AmountPaidDuePerShare>
            <AmountUnpaidPerShare>0.00</AmountUnpaidPerShare>
            <ShareCurrency>GBP</ShareCurrency>
            <ShareValue>1.00</ShareValue>
        </Shares>

        <MemorandumStatement>
            Each subscriber to this memorandum of association wishes to form a company under the Companies Act 2006 and agrees to become a member of the company and to take at least one share.
        </MemorandumStatement>
    </Subscribers>

    <!-- REQUIRED -->
    <Authoriser>
        <Person>
            <Forename>John</Forename>
            <Surname>Doe</Surname>
        </Person>
    </Authoriser>

    <SameDay>false</SameDay>

    <SICCodes>
        <SICCode>62020</SICCode>
    </SICCodes>

    <RegisteredEmailAddress>test@testcompany.com</RegisteredEmailAddress>

    <!-- REQUIRED FINAL ELEMENT -->
    <AcceptLawfulPurposeStatement>true</AcceptLawfulPurposeStatement>

</CompanyIncorporation>';

    return $xml;
}