<?php
    if(isset($_POST['save_btn'])){
        $Email = $_POST['Email'];
        $id = $_POST['id'];
        $First_Name = $_POST['First_Name'];
        $Last_Name = $_POST['Last_Name'];
        $Phone = $_POST['Phone'];
        $Support_Rep = $_POST['Support_Rep'];
        $Notes = $_POST['Notes'];
        if(!empty($Email) && is_email($Email) && !empty($First_Name) ){
            $my_wpdb->update($dim_amb_tbl,['Email'=>$Email, 'First_Name'=>$First_Name, 'Last_Name'=>$Last_Name, 'Support_Rep'=>$Support_Rep, 'Notes'=>$Notes,'Phone'=>$Phone ],['id'=>$id]);

            $msg = dim_amb_display_msg('Ambassador Lead saved successfully.');
            
        }else{
            $error = true;
            $msg = dim_amb_display_msg('Error: Email and First Name fields must be filled.',2);
        }
    }
    $lead = $my_wpdb->get_row("SELECT * FROM $dim_amb_tbl WHERE id=$id");
    
    
?>

<div class="wrap">
    <h2>ADD AMBASSADOR PROGRAM LEAD</h2>
    <?php if(isset($msg)) echo $msg; ?>
    <form method="post" action="">
        <input name="id" type="hidden" value="<?php if(isset($lead)) echo $lead->id; ?>">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="email">Email Address: </label></th>
                <td><input name="Email" type="email" id="email" value="<?php if(isset($lead)) echo $lead->Email; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="First_Name">First Name: </label></th>
                <td><input name="First_Name" type="text" id="First_Name" value="<?php if(isset($lead)) echo $lead->First_Name; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="Last_Name">Last Name: </label></th>
                <td><input name="Last_Name" type="text" id="Last_Name" value="<?php if(isset($lead)) echo $lead->Last_Name; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="Phone">Phone: </label></th>
                <td><input name="Phone" type="text" id="Phone" value="<?php if(isset($lead)) echo $lead->Phone; ?>" class="regular-text ltr"></td>
            </tr>
            <tr>
                <th scope="row"><label for="Support_Rep">Support Rep: </label></th>
                <td>
                    <select name="Support_Rep"  class="regular-text ltr">
                        <?php
                            $reps = ['Sherica', 'Russell', 'Omar', 'Luke'];
                            foreach ($reps as $rep) {
                                $selected = isset($lead) && $lead->Support_Rep==$rep?'selected="selected"':'';
                                echo '<option '.$selected.' value="'.$rep.'">'.$rep.'</option>';
                            }
                        ?>
                    </select>
                    
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="notes">Notes: </label></th>
                <td>
                    <textarea class="regular-text ltr" name="Notes" id="notes" rows="6" cols="80"><?php if(isset($lead)) echo $lead->Notes; ?></textarea></td>
            </tr>
            </tbody></table>
            <style>
            </style>

        <p class="submit"><input type="submit" name="save_btn" id="submit" class="button button-primary" value="UPDATE LEAD"></p></form>
   

</div>
<style>
    .wrap{padding: 10px 20px;}
</style>