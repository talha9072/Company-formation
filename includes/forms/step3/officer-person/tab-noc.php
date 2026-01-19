<?php if (!defined('ABSPATH')) exit; ?>

<div id="tab-noc" class="step3-tab-content" style="display:none;">

    <h3>Nature of Control</h3>

    <!-- ================================================= -->
    <!-- COMPANY CONTROL -->
    <!-- ================================================= -->
    <h4>Does this officer have a controlling interest in this company?</h4>

    <table class="form-table">
        <tbody>

            <tr>
                <th><label>Ownership of shares</label></th>
                <td>
                    <select name="psc_company_shares">
                        <option value="na" selected>N/A</option>
                        <option value="25-50">More than 25% but not more than 50%</option>
                        <option value="50-75">More than 50% but less than 75%</option>
                        <option value="75+">75% or more</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label>Voting rights</label></th>
                <td>
                    <select name="psc_company_voting">
                        <option value="na" selected>N/A</option>
                        <option value="25-50">More than 25% but not more than 50%</option>
                        <option value="50-75">More than 50% but less than 75%</option>
                        <option value="75+">75% or more</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label>Appoint or remove the majority of the board of directors</label></th>
                <td>
                    <select name="psc_company_directors">
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label>Other significant influence or control</label></th>
                <td>
                    <select name="psc_company_other">
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </td>
            </tr>

        </tbody>
    </table>

    <!-- ================================================= -->
    <!-- FIRM CONTROL -->
    <!-- ================================================= -->
    <hr>

    <h4>
        Does this officer have a controlling influence over a Firm(s) and/or the
        Members of that Firm(s), which also has a controlling influence in this company?
    </h4>

    <label>
        <input type="radio" name="psc_as_firm" value="0" checked> No
    </label>
    &nbsp;&nbsp;
    <label>
        <input type="radio" name="psc_as_firm" value="1"> Yes
    </label>

    <div id="psc-firm-section">

        <h4>What influence or control does this officer have over this company in their capacity within the Firm(s)?</h4>

        <table class="form-table">
            <tbody>

                <tr>
                    <th><label>Ownership of shares</label></th>
                    <td>
                        <select name="psc_firm_shares">
                            <option value="na">N/A</option>
                            <option value="25-50">More than 25% but not more than 50%</option>
                            <option value="50-75">More than 50% but less than 75%</option>
                            <option value="75+">75% or more</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Voting rights</label></th>
                    <td>
                        <select name="psc_firm_voting">
                            <option value="na">N/A</option>
                            <option value="25-50">More than 25% but not more than 50%</option>
                            <option value="50-75">More than 50% but less than 75%</option>
                            <option value="75+">75% or more</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Appoint or remove the majority of the board of directors</label></th>
                    <td>
                        <select name="psc_firm_directors">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Other significant influence or control</label></th>
                    <td>
                        <select name="psc_firm_other">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </td>
                </tr>

            </tbody>
        </table>

    </div>

    <!-- ================================================= -->
    <!-- TRUST CONTROL -->
    <!-- ================================================= -->
    <hr>

    <h4>
        Does this officer have a controlling influence over a trust(s) and/or the
        trustees of that trust(s), which has a controlling interest in this company?
    </h4>

    <label>
        <input type="radio" name="psc_as_trust" value="0" checked> No
    </label>
    &nbsp;&nbsp;
    <label>
        <input type="radio" name="psc_as_trust" value="1"> Yes
    </label>

    <div id="psc-trust-section">

        <h4>
            What control or influence does this officer have over this company
            in their capacity within the Trust(s)?
        </h4>

        <table class="form-table">
            <tbody>

                <tr>
                    <th><label>Ownership of shares</label></th>
                    <td>
                        <select name="psc_trust_shares">
                            <option value="na">N/A</option>
                            <option value="25-50">More than 25% but not more than 50%</option>
                            <option value="50-75">More than 50% but less than 75%</option>
                            <option value="75+">75% or more</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Voting rights</label></th>
                    <td>
                        <select name="psc_trust_voting">
                            <option value="na">N/A</option>
                            <option value="25-50">More than 25% but not more than 50%</option>
                            <option value="50-75">More than 50% but less than 75%</option>
                            <option value="75+">75% or more</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Appoint or remove the majority of the board of directors</label></th>
                    <td>
                        <select name="psc_trust_directors">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label>Other significant influence or control</label></th>
                    <td>
                        <select name="psc_trust_other">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </td>
                </tr>

            </tbody>
        </table>

    </div>

</div>
