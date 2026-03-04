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

    // Fetch stored data (for future dynamic use), but for exact test we override with example values
    $formation = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$formation_table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    // Use example values exactly — for testing
    $company_name     = 'JOHN SMITH EXAMPLE LIMITED';
    $package_ref      = '9999';
    $submission_num   = 'INCb05';
    $date_signed      = '2019-11-01'; // You can change to date('Y-m-d') later

    $company_type     = 'BYSHR';
    $country_incorp   = 'EW';

    $xml = '
<FormSubmission xmlns="http://xmlgw.companieshouse.gov.uk/Header"
    xmlns:bs="http://xmlgw.companieshouse.gov.uk"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://xmlgw.companieshouse.gov.uk/Header http://xmlgw.companieshouse.gov.uk/v1-0/schema/forms/FormSubmission-v2-11.xsd">

    <FormHeader>
        <CompanyName>' . esc_xml($company_name) . '</CompanyName>
        <PackageReference>' . esc_xml($package_ref) . '</PackageReference>
        <FormIdentifier>CompanyIncorporation</FormIdentifier>
        <SubmissionNumber>' . esc_xml($submission_num) . '</SubmissionNumber>
    </FormHeader>

    <DateSigned>' . esc_xml($date_signed) . '</DateSigned>

    <Form>

        <CompanyIncorporation xmlns="http://xmlgw.companieshouse.gov.uk"
            xsi:schemaLocation="http://xmlgw.companieshouse.gov.uk http://xmlgw.companieshouse.gov.uk/v1-0/schema/forms/CompanyIncorporation-v3-8.xsd">

            <CompanyType>' . esc_xml($company_type) . '</CompanyType>
            <CountryOfIncorporation>' . esc_xml($country_incorp) . '</CountryOfIncorporation>

            <RegisteredOfficeAddress>
                <Premise>1</Premise>
                <Street>Any Road</Street>
                <Thoroughfare>Area</Thoroughfare>
                <PostTown>Anytown</PostTown>
                <Country>GBR</Country>
                <Postcode>ZB2 2ZZ</Postcode>
            </RegisteredOfficeAddress>

            <DataMemorandum>true</DataMemorandum>
            <Articles>BYSHRMODEL</Articles>

            <Appointment>
                <ConsentToAct>true</ConsentToAct>
                <Director>
                    <Person>
                        <Forename>Fred</Forename>
                        <Surname>Jones</Surname>
                        <ServiceAddress>
                            <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
                        </ServiceAddress>
                        <DOB>1992-01-01</DOB>
                        <Nationality>British</Nationality>
                        <CountryOfResidence>WALES</CountryOfResidence>
                        <ResidentialAddress>
                            <Address>
                                <Premise>1</Premise>
                                <Street>High Street</Street>
                                <PostTown>Anywhere</PostTown>
                                <Country>GB-WLS</Country>
                                <Postcode>QP1 1XY</Postcode>
                            </Address>
                        </ResidentialAddress>
                        <VerificationDetails>
                            <CompaniesHousePersonalCode>12345678901</CompaniesHousePersonalCode>
                            <VerificationStatements>
                                <VerificationStatementForIndividual>INDIVIDUAL_VERIFIED</VerificationStatementForIndividual>
                            </VerificationStatements>
                            <NameMismatchReason>LEGALLY_CHANGED</NameMismatchReason>
                        </VerificationDetails>
                    </Person>
                </Director>
            </Appointment>

            <Appointment>
                <ConsentToAct>true</ConsentToAct>
                <Director>
                    <Corporate>
                        <CorporateName>A UK Company Ltd</CorporateName>
                        <Address>
                            <Premise>1</Premise>
                            <Street>High Street</Street>
                            <PostTown>Anywhere</PostTown>
                            <Country>GB-WLS</Country>
                            <Postcode>QP1 1XY</Postcode>
                        </Address>
                        <CompanyIdentification>
                            <UK>
                                <RegistrationNumber>12345678</RegistrationNumber>
                            </UK>
                        </CompanyIdentification>
                    </Corporate>
                </Director>
            </Appointment>

            <Appointment>
                <ConsentToAct>true</ConsentToAct>
                <Director>
                    <Corporate>
                        <CorporateName>A Non UK Company Ltd</CorporateName>
                        <Address>
                            <Premise>2</Premise>
                            <Street>A Street In Belgium</Street>
                            <PostTown>Anywhere</PostTown>
                            <Country>BEL</Country>
                            <Postcode>9999</Postcode>
                        </Address>
                        <CompanyIdentification>
                            <NonUK>
                                <PlaceRegistered>Belgium</PlaceRegistered>
                                <RegistrationNumber>12345678</RegistrationNumber>
                                <LawGoverned>Belgium</LawGoverned>
                                <LegalForm>Belgium Limited Liability Company</LegalForm>
                            </NonUK>
                        </CompanyIdentification>
                    </Corporate>
                </Director>
            </Appointment>

            <Appointment>
                <ConsentToAct>true</ConsentToAct>
                <Secretary>
                    <Person>
                        <Forename>Harry</Forename>
                        <Surname>Smith</Surname>
                        <ServiceAddress>
                            <Address>
                                <Premise>1</Premise>
                                <Street>No Street</Street>
                                <PostTown>Nowhere</PostTown>
                                <Country>CAN</Country>
                            </Address>
                        </ServiceAddress>
                    </Person>
                </Secretary>
            </Appointment>

            <PSCs>
                <PSC>
                    <PSCNotification>
                        <Individual>
                            <Forename>John</Forename>
                            <Surname>Smith</Surname>
                            <ServiceAddress>
                                <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
                            </ServiceAddress>
                            <DOB>1900-01-01</DOB>
                            <Nationality>British</Nationality>
                            <CountryOfResidence>United Kingdom</CountryOfResidence>
                            <ResidentialAddress>
                                <Address>
                                    <Premise>742</Premise>
                                    <Street>Long Street</Street>
                                    <PostTown>Someplace</PostTown>
                                    <Country>GB-WLS</Country>
                                    <Postcode>QP12 0NN</Postcode>
                                </Address>
                            </ResidentialAddress>
                            <VerificationDetails>
                                <CompaniesHousePersonalCode>12345678901</CompaniesHousePersonalCode>
                                <VerificationStatements>
                                    <VerificationStatementForIndividual>INDIVIDUAL_VERIFIED</VerificationStatementForIndividual>
                                </VerificationStatements>
                                <NameMismatchReason>LEGALLY_CHANGED</NameMismatchReason>
                            </VerificationDetails>
                            <ConsentStatement>true</ConsentStatement>
                        </Individual>
                        <NatureOfControls>
                            <NatureOfControl>OWNERSHIPOFSHARES_25TO50PERCENT</NatureOfControl>
                        </NatureOfControls>
                    </PSCNotification>
                </PSC>
                <PSC>
                    <PSCNotification>
                        <Individual>
                            <Forename>Jane</Forename>
                            <Surname>Smith</Surname>
                            <ServiceAddress>
                                <SameAsRegisteredOffice>true</SameAsRegisteredOffice>
                            </ServiceAddress>
                            <DOB>1985-01-02</DOB>
                            <Nationality>British</Nationality>
                            <CountryOfResidence>United Kingdom</CountryOfResidence>
                            <ResidentialAddress>
                                <Address>
                                    <Premise>744</Premise>
                                    <Street>Long Street</Street>
                                    <PostTown>Someplace</PostTown>
                                    <Country>GB-ENG</Country>
                                    <Postcode>QP12 0NN</Postcode>
                                </Address>
                            </ResidentialAddress>
                            <ConsentStatement>true</ConsentStatement>
                        </Individual>
                        <NatureOfControls>
                            <NatureOfControl>OWNERSHIPOFSHARES_50TO75PERCENT</NatureOfControl>
                        </NatureOfControls>
                    </PSCNotification>
                </PSC>
                <PSC>
                    <PSCNotification>
                        <Corporate>
                            <CorporateName>BIG OLD COMPANY LIMITED</CorporateName>
                            <Address>
                                <Premise>11</Premise>
                                <PostTown>Cardiff</PostTown>
                                <Country>GB-WLS</Country>
                            </Address>
                            <PSCCompanyIdentification>
                                <PSCPlaceRegistered>London</PSCPlaceRegistered>
                                <PSCRegistrationNumber>12345678</PSCRegistrationNumber>
                                <LawGoverned>BigOldLaw</LawGoverned>
                                <LegalForm>BigOldForm</LegalForm>
                                <CountryOrState>England</CountryOrState>
                            </PSCCompanyIdentification>
                        </Corporate>
                        <NatureOfControls>
                            <NatureOfControl>OWNERSHIPOFSHARES_25TO50PERCENT</NatureOfControl>
                        </NatureOfControls>
                    </PSCNotification>
                </PSC>
            </PSCs>

            <StatementOfCapital>
                <Capital>
                    <TotalAmountUnpaid>10</TotalAmountUnpaid>
                    <TotalNumberOfIssuedShares>100</TotalNumberOfIssuedShares>
                    <ShareCurrency>GBP</ShareCurrency>
                    <TotalAggregateNominalValue>100</TotalAggregateNominalValue>
                    <Shares>
                        <ShareClass>Ordinary</ShareClass>
                        <PrescribedParticulars>None</PrescribedParticulars>
                        <NumShares>100</NumShares>
                        <AggregateNominalValue>100</AggregateNominalValue>
                    </Shares>
                </Capital>
            </StatementOfCapital>

            <Subscribers>
                <Person>
                    <Forename>Fred</Forename>
                    <Surname>Jones</Surname>
                </Person>
                <Address>
                    <Premise>1</Premise>
                    <Street>Fred Street</Street>
                    <PostTown>Fred Town</PostTown>
                    <Country>GBR</Country>
                    <Postcode>QP12 0NN</Postcode>
                </Address>
                <Authentication>
                    <MemorandumPersonalAuthentication>SUBSCRIBER_AGREES_NAME_USED_TO_AUTHENTICATE</MemorandumPersonalAuthentication>
                </Authentication>
                <Shares>
                    <ShareClass>Ordinary</ShareClass>
                    <NumShares>100</NumShares>
                    <AmountPaidDuePerShare>0.99</AmountPaidDuePerShare>
                    <AmountUnpaidPerShare>0.01</AmountUnpaidPerShare>
                    <ShareCurrency>GBP</ShareCurrency>
                    <ShareValue>1</ShareValue>
                </Shares>
                <MemorandumStatement>Each subscriber to this memorandum of association wishes to form a company under the Companies Act 2006 and agrees to become a member of the company and to take at least one share.</MemorandumStatement>
            </Subscribers>

            <Authoriser>
                <Agent>
                    <Corporate>
                        <Forename>Fred</Forename>
                        <Surname>Jones</Surname>
                        <CorporateName>Jones and Co</CorporateName>
                    </Corporate>
                    <Authentication>
                        <AuthoriserPersonalAuthentication>AUTHORISER_AGREES_NAME_USED_TO_AUTHENTICATE</AuthoriserPersonalAuthentication>
                    </Authentication>
                    <Address>
                        <Premise>1</Premise>
                        <Street>MOO STREET</Street>
                        <PostTown>Cardiff</PostTown>
                        <Country>GB-WLS</Country>
                    </Address>
                </Agent>
            </Authoriser>

            <SameDay>false</SameDay>
            <RejectReference>XYZ12345</RejectReference>

            <SICCodes>
                <SICCode>71129</SICCode>
            </SICCodes>

            <RegisteredEmailAddress>test@test.com</RegisteredEmailAddress>
            <AcceptLawfulPurposeStatement>true</AcceptLawfulPurposeStatement>

        </CompanyIncorporation>

    </Form>

</FormSubmission>';

    return $xml;
}