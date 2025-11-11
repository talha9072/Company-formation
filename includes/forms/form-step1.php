<div class="step-form-wrapper">
    <h3 class="form-title">Particulars</h3>

    <form id="step1form">
        <div class="step-grid">
            <!-- Type of Company -->
            <div class="form-group">
                <label for="company_type">Type of Company <span class="info-icon" title="Select the type of company you want to register.">ℹ️</span></label>
                <select id="company_type" name="company_type">
                    <option value="Limited By Shares">Limited By Shares</option>
                    <option value="Limited By Guarantee">Limited By Guarantee</option>
                    <option value="LLP">LLP</option>
                    <option value="PLC">PLC</option>
                </select>
            </div>

            <!-- Jurisdiction -->
            <div class="form-group">
                <label for="jurisdiction">Jurisdiction <span class="info-icon" title="Choose your registration region.">ℹ️</span></label>
                <select id="jurisdiction" name="jurisdiction">
                    <option value="England & Wales">England & Wales</option>
                    <option value="Scotland">Scotland</option>
                    <option value="Northern Ireland">Northern Ireland</option>
                </select>
            </div>

            <!-- Business Activities -->
            <div class="form-group">
                <label for="business_activity">What are your business activities <span class="info-icon" title="Select your primary business activity.">ℹ️</span></label>
                <select id="business_activity" name="business_activity">
                    <option value="">Select a category:</option>
                    <option value="Technology">Technology</option>
                    <option value="Retail">Retail</option>
                    <option value="Finance">Finance</option>
                    <option value="Construction">Construction</option>
                </select>
            </div>

            <!-- SIC Codes -->
            <div class="form-group">
                <label for="sic_codes">Selected SIC codes <span class="info-icon" title="SIC codes represent your company’s type of business.">ℹ️</span></label>
                <p id="selected_sic_codes">None Selected</p>
            </div>
        </div>

        <div class="form-footer">
            <button type="button" class="button button-primary">Save & Continue</button>
        </div>
    </form>
</div>
