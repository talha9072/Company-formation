<?php
if (!defined('ABSPATH')) exit;

// Load saved addresses
$addresses = get_option('ncuk_registered_addresses', []);

// Save on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ncuk_save_addresses'])) {

    $names        = $_POST['address_name'] ?? [];
    $descriptions = $_POST['address_desc'] ?? [];

    $new = [];

    foreach ($names as $i => $name) {
        $name = sanitize_text_field($name);
        $desc = sanitize_textarea_field($descriptions[$i]);

        if (!empty($name)) {
            $new[] = [
                'name' => $name,
                'desc' => $desc
            ];
        }
    }

    update_option('ncuk_registered_addresses', $new);

    echo '<div class="updated"><p>Addresses updated successfully.</p></div>';
}
?>

<div class="wrap">
    <h1>Registered Office Addresses</h1>

    <p>Add unlimited Registered Office Address options below.</p>

    <form method="post">

        <table class="form-table" id="address-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="width:80px;">Remove</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($addresses)) : ?>
                    <?php foreach ($addresses as $index => $addr) : ?>
                        <tr>
                            <td>
                                <input type="text" 
                                       name="address_name[]" 
                                       value="<?php echo esc_attr($addr['name']); ?>" 
                                       class="regular-text" />
                            </td>
                            <td>
                                <textarea name="address_desc[]" 
                                          rows="3" 
                                          style="width:100%;"><?php echo esc_textarea($addr['desc']); ?></textarea>
                            </td>
                            <td>
                                <button type="button" class="button remove-row">X</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p>
            <button type="button" class="button button-primary" id="add-row">+ Add New Address</button>
        </p>

        <p>
            <input type="submit" 
                   name="ncuk_save_addresses" 
                   class="button button-primary" 
                   value="Save Addresses" />
        </p>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.querySelector("#address-table tbody");
    const addBtn = document.getElementById("add-row");

    addBtn.addEventListener("click", function () {

        const row = document.createElement("tr");

        row.innerHTML = `
            <td>
                <input type="text" name="address_name[]" class="regular-text" placeholder="Address Name" />
            </td>
            <td>
                <textarea name="address_desc[]" rows="3" style="width:100%;" placeholder="Description"></textarea>
            </td>
            <td>
                <button type="button" class="button remove-row">X</button>
            </td>
        `;

        tableBody.appendChild(row);
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-row")) {
            e.target.closest("tr").remove();
        }
    });

});
</script>
