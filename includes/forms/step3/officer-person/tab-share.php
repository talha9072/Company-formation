<?php if (!defined('ABSPATH')) exit; ?>

<div id="tab-share" class="step3-tab-content" style="display:none;">

    <h3>Share Holdings</h3>

    <!-- fixed currency -->
    <input type="hidden" name="share_currency" value="GBP">

    <table class="form-table">
        <tbody>

            <tr>
                <th scope="row">
                    <label for="share_class">Class</label>
                </th>
                <td>
                    <input type="text"
                           id="share_class"
                           name="share_class"
                           class="regular-text"
                           value="ORDINARY">
                </td>

                <th scope="row">
                    <label for="share_quantity">Quantity</label>
                </th>
                <td>
                    <input type="number"
                           id="share_quantity"
                           name="share_quantity"
                           class="small-text"
                           value="1"
                           min="1">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="share_price">Price (GBP)</label>
                </th>
                <td colspan="3">
                    <input type="number"
                           id="share_price"
                           name="share_price"
                           class="small-text"
                           value="1"
                           step="0.01"
                           min="0">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="share_particulars">Particulars</label>
                </th>
                <td colspan="3">
                    <textarea id="share_particulars"
                              name="share_particulars"
                              rows="4"
                              class="large-text">
Full voting rights and full entitlement to profit and capital distribution.
                    </textarea>
                </td>
            </tr>

        </tbody>
    </table>

</div>
