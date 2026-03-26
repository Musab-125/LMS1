<?php
$selected_department = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;
?>

<!-- Select Department -->
<form method="GET">
    <select name="department_id" onchange="this.form.submit()">
        <option value="">-- Select Department --</option>
        <?php
        $deps = $conn->query("SELECT * FROM departments");
        while($d = $deps->fetch_assoc()){
        ?>
            <option value="<?= $d['id'] ?>" <?= ($selected_department==$d['id'])?'selected':'' ?>>
                <?= $d['department_name'] ?>
            </option>
        <?php } ?>
    </select>
</form>

<!-- Show Modules -->
<table>
<tr>
    <th>#</th>
    <th>Module Name</th>
</tr>

<?php
if($selected_department > 0){
    $modules = $conn->query("SELECT * FROM modules WHERE department_id='$selected_department'");
} else {
    $modules = $conn->query("SELECT * FROM modules");
}

while($m = $modules->fetch_assoc()){
?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['module_name']) ?></td>
</tr>
<?php } ?>
</table>